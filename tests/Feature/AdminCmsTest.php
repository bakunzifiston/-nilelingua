<?php

namespace Tests\Feature;

use App\Models\CmsEntry;
use App\Models\Media;
use App\Models\ProjectEnquiry;
use App\Models\User;
use App\Support\Cms\ContentManager;
use App\Support\Cms\EditorSchema;
use App\Support\Cms\SiteContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCmsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        ContentManager::import();
        SiteContent::flush();
        $this->admin = User::factory()->create(['is_admin' => true, 'password' => 'Secret12345']);
    }

    /** Builds the editor's form input for a content array (list items carry their shape). */
    private function formInput(array $content): array
    {
        $out = [];
        foreach ($content as $key => $value) {
            if (is_array($value) && array_is_list($value)) {
                $out[$key] = array_map(function ($item) {
                    if (is_array($item) && ! array_is_list($item)) {
                        return $this->formInput($item) + ['__v' => EditorSchema::signature(EditorSchema::blank($item))];
                    }

                    return is_array($item) ? $this->formInput($item) : $item;
                }, $value);
            } elseif (is_array($value)) {
                $out[$key] = $this->formInput($value);
            } else {
                $out[$key] = is_bool($value) ? ($value ? '1' : '0') : $value;
            }
        }

        return $out;
    }

    private function entry(string $key): CmsEntry
    {
        return CmsEntry::query()->with('contents')->where('key', $key)->firstOrFail();
    }

    public function test_dashboard_is_only_available_to_administrators(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/content/1')->assertRedirect('/admin/login');

        $visitor = User::factory()->create(['is_admin' => false, 'password' => 'Secret12345']);
        $this->post('/admin/login', ['email' => $visitor->email, 'password' => 'Secret12345'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->actingAs($visitor)->get('/admin')->assertForbidden();

        $this->post('/admin/login', ['email' => $this->admin->email, 'password' => 'Secret12345'])->assertRedirect('/admin');
        $this->get('/admin')->assertOk()->assertSee('Website pages')->assertSee('Needs attention');
    }

    public function test_there_is_no_public_registration(): void
    {
        $this->get('/admin/register')->assertNotFound();
        $this->get('/register')->assertNotFound();
    }

    public function test_drafts_stay_private_until_published(): void
    {
        $entry = $this->entry('page:about');
        $content = $entry->contentFor('en')->published;
        $content['headline'] = 'A new About headline';

        $this->actingAs($this->admin)
            ->put("/admin/content/{$entry->id}?lang=en", ['c' => $this->formInput($content), 'action' => 'save'])
            ->assertRedirect()->assertSessionHas('success');

        $this->get('/about')->assertOk()->assertDontSee('A new About headline');
        $this->get("/admin/content/{$entry->id}/preview?lang=en")->assertOk()->assertSee('A new About headline')->assertSee('Draft preview');

        $this->post("/admin/content/{$entry->id}/publish?lang=en")->assertRedirect();
        $this->get('/about')->assertSee('A new About headline');
        $this->get('/fr/about')->assertDontSee('A new About headline');
        $this->assertSame(1, $entry->revisions()->count());
    }

    public function test_saving_unchanged_content_keeps_it_identical(): void
    {
        $this->actingAs($this->admin);

        foreach (['home', 'page:languages', 'page:privacy-policy', 'article:african-language-localization-guide', 'settings:forms'] as $key) {
            $entry = $this->entry($key);
            $published = $entry->contentFor('en')->published;
            $this->put("/admin/content/{$entry->id}?lang=en", ['c' => $this->formInput($published), 'action' => 'save'])->assertSessionHasNoErrors();
            $this->assertSame($published, $entry->fresh()->contentFor('en')->draft, "Round trip changed {$key}");
        }
    }

    public function test_invalid_links_and_missing_titles_are_rejected(): void
    {
        $entry = $this->entry('page:about');
        $content = $entry->contentFor('en')->published;
        $content['title'] = '';
        $content['pillars'][0]['href'] = 'javascript:alert(1)';

        $this->actingAs($this->admin)
            ->put("/admin/content/{$entry->id}?lang=en", ['c' => $this->formInput($content)])
            ->assertSessionHasErrors(['c.title', 'c.pillars.0.href']);

        $this->assertNull($entry->fresh()->contentFor('en')->draft);
    }

    public function test_homepage_sections_can_be_reordered_and_hidden(): void
    {
        $entry = $this->entry('home');
        $content = $entry->contentFor('en')->published;
        $types = array_column($content['sections'], 'type');
        $why = array_search('why', $types, true);
        $languages = array_search('languages', $types, true);

        [$content['sections'][$why], $content['sections'][$languages]] = [$content['sections'][$languages], $content['sections'][$why]];
        $content['sections'][array_search('tools', $types, true)]['visible'] = false;

        $this->actingAs($this->admin)->put("/admin/content/{$entry->id}?lang=en", ['c' => $this->formInput($content), 'action' => 'publish']);

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertLessThan(strpos($html, 'id="home-languages"'), strpos($html, 'id="home-why"'));
        $this->assertStringNotContainsString('id="home-tools"', $html);
    }

    public function test_new_articles_are_hidden_until_published_and_linked_items_cannot_be_deleted(): void
    {
        $source = $this->entry('article:african-language-localization-guide');

        $this->actingAs($this->admin)->post('/admin/collections/article', [
            'title' => 'Fresh article', 'slug' => 'fresh-article', 'source' => $source->id, 'copy_text' => '1',
        ])->assertRedirect();

        $this->get('/knowledge-center/fresh-article')->assertNotFound();

        $article = $this->entry('article:fresh-article');
        $this->post("/admin/content/{$article->id}/publish?lang=en");
        $this->get('/knowledge-center/fresh-article')->assertOk();
        $this->get('/fr/knowledge-center/fresh-article')->assertNotFound();

        $this->delete("/admin/content/{$article->id}")->assertRedirect();
        $this->get('/knowledge-center/fresh-article')->assertNotFound();

        $linked = $this->entry('service:subtitling');
        $this->delete("/admin/content/{$linked->id}")->assertSessionHas('error');
        $this->get('/services/subtitling')->assertOk();

        $this->delete('/admin/content/'.$this->entry('page:about')->id)->assertForbidden();
    }

    public function test_interface_text_can_be_changed_per_language(): void
    {
        $key = 'Our Mission &amp; <em>Vision</em>';

        $this->actingAs($this->admin)->put('/admin/interface-text', ['strings' => [sha1($key) => [
            'key' => $key, 'en' => 'Our Mission &amp; <em>Goals</em><script>x</script>', 'fr' => 'Notre mission et nos <em>objectifs</em>',
        ]]])->assertSessionHas('success');

        $this->get('/about')->assertSee('Our Mission &amp; <em>Goals</em>', false)->assertDontSee('<script>x', false);
        $this->get('/fr/about')->assertSee('Notre mission et nos <em>objectifs</em>', false);
    }

    public function test_submissions_are_listed_opened_and_their_files_kept_private(): void
    {
        Storage::fake('local');
        $path = Storage::disk('local')->put('enquiries', UploadedFile::fake()->create('brief.pdf', 10));
        $enquiry = ProjectEnquiry::create([
            'name' => 'Amina Example', 'email' => 'amina@example.org', 'service' => 'AI Language Data & MTPE',
            'message' => 'Speech data for a health chatbot.', 'attachment_path' => $path, 'attachment_name' => 'brief.pdf',
        ]);

        $this->get("/admin/submissions/quotes/{$enquiry->id}/file")->assertRedirect('/admin/login');

        $this->actingAs($this->admin)->get('/admin/submissions/quotes')->assertOk()->assertSee('Amina Example')->assertSee('1 new');
        $this->get("/admin/submissions/quotes/{$enquiry->id}")->assertOk()->assertSee('Speech data for a health chatbot.');
        $this->assertNotNull($enquiry->fresh()->read_at);
        $this->get("/admin/submissions/quotes/{$enquiry->id}/file")->assertOk()->assertDownload('brief.pdf');
        $this->get('/admin/submissions/quotes?export=csv')->assertOk()->assertDownload();
        $this->get('/admin')->assertSee('AI Language Data &amp; MTPE', false);

        $this->delete("/admin/submissions/quotes/{$enquiry->id}")->assertRedirect('/admin/submissions/quotes');
        $this->assertModelMissing($enquiry);
        Storage::disk('local')->assertMissing($path);
    }

    public function test_links_to_missing_pages_are_rejected(): void
    {
        $entry = $this->entry('page:about');
        $content = $entry->contentFor('en')->published;
        $content['pillars'][0]['href'] = '/services/does-not-exist';
        $content['intro'] = 'See [our guide](/knowledge-center/not-here).';

        $this->actingAs($this->admin)
            ->put("/admin/content/{$entry->id}?lang=en", ['c' => $this->formInput($content)])
            ->assertSessionHasErrors(['c.pillars.0.href']);

        $content['pillars'][0]['href'] = '/fr/services/subtitling#steps';
        $content['intro'] = 'Plain intro.';
        $this->put("/admin/content/{$entry->id}?lang=en", ['c' => $this->formInput($content)])->assertSessionHasNoErrors();
    }

    public function test_outsiders_cannot_probe_admin_record_ids(): void
    {
        $this->delete('/admin/administrators/'.$this->admin->id)->assertRedirect('/admin/login');
        $this->delete('/admin/administrators/999999')->assertRedirect('/admin/login');
        $this->get('/admin/content/999999')->assertRedirect('/admin/login');
    }

    public function test_public_forms_have_separate_rate_limits(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post('/contact', ['name' => ''])->assertRedirect();
        }
        $this->post('/contact', ['name' => ''])->assertStatus(429)->assertSee('Too many attempts');
        $this->post('/newsletter', ['email' => 'reader@example.org'])->assertRedirect();
    }

    public function test_error_pages_are_branded_and_localized(): void
    {
        $this->get('/no-such-page')->assertNotFound()->assertSee('Page not found');
        $this->get('/fr/no-such-page')->assertNotFound()->assertSee('Page introuvable');
    }

    public function test_svg_uploads_are_cleaned_before_saving(): void
    {
        $svg = '<?xml version="1.0"?><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 120 60" onload="alert(1)">'
            .'<script>alert(2)</script><style>@import url(https://evil.test/x.css); .a{fill:#0F5C52}</style>'
            .'<a href="javascript:alert(3)"><rect class="a" width="120" height="60"/></a>'
            .'<foreignObject><iframe src="https://evil.test"></iframe></foreignObject>'
            .'<use xlink:href="https://evil.test/sprite.svg#x"/><circle cx="30" cy="30" r="10" fill="#E0A84F"/></svg>';

        $this->actingAs($this->admin)
            ->post('/admin/media', ['file' => UploadedFile::fake()->createWithContent('logo.svg', $svg)])
            ->assertSessionHasNoErrors();

        $media = Media::latest('id')->firstOrFail();
        $saved = file_get_contents(public_path($media->path));
        File::delete(public_path($media->path));

        $this->assertSame('image/svg+xml', $media->mime_type);
        $this->assertSame([120, 60], [$media->width, $media->height]);
        $this->assertStringContainsString('<circle', $saved);
        foreach (['onload', '<script', 'javascript:', '@import', 'foreignObject', 'iframe', 'evil.test'] as $unsafe) {
            $this->assertStringNotContainsStringIgnoringCase($unsafe, $saved);
        }
    }

    public function test_files_that_are_not_images_are_rejected(): void
    {
        $this->actingAs($this->admin);
        $this->post('/admin/media', ['file' => UploadedFile::fake()->createWithContent('fake.svg', 'not an svg')])->assertSessionHasErrors('file');
        $this->post('/admin/media', ['file' => UploadedFile::fake()->createWithContent('fake.png', '<?php echo 1;')])->assertSessionHasErrors('file');
        $this->post('/admin/media', ['file' => UploadedFile::fake()->createWithContent('bomb.svg', '<!DOCTYPE svg [<!ENTITY a "aaaa">]><svg xmlns="http://www.w3.org/2000/svg">&a;</svg>')])->assertSessionHasErrors('file');
        $this->assertSame(0, Media::count());

        // A real PNG is still accepted.
        $this->post('/admin/media', ['file' => UploadedFile::fake()->image('photo.png', 40, 40)])->assertSessionHasNoErrors();
        $media = Media::firstOrFail();
        File::delete(public_path($media->path));
        $this->assertSame([40, 40], [$media->width, $media->height]);
    }
}
