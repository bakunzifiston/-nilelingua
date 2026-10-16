<?php

namespace Tests\Feature;

use App\Models\ProjectEnquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_planned_pages_and_articles_are_available(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<title>African Language Services &amp; AI Data | Nile Lingua Lab</title>', false)
            ->assertSee('African Language Solutions & Human-in-the-Loop AI Data')
            ->assertSee('What We')
            ->assertSee('Localization & Digital Adaptation')
            ->assertSee('15 LANGUAGES')
            ->assertSee('Trados Studio')
            ->assertSee('contact@nilelingua.com');
        $this->get('/knowledge-center')
            ->assertOk()
            ->assertSee('All Articles')
            ->assertSee('African Language Localization Guide')
            ->assertSee('Machine Translation & African Languages')
            ->assertSee('Cultural Context & Market Entry')
            ->assertSee('Multilingual Communication in East Africa')
            ->assertSee('Why Native Subject-Matter Expertise Matters');

        foreach (array_keys(config('site.pages')) as $slug) {
            $this->get("/{$slug}")->assertOk();
        }

        foreach (array_keys(config('site.services')) as $slug) {
            $this->get("/services/{$slug}")->assertOk();
        }

        foreach (array_keys(config('site.articles')) as $slug) {
            $this->get("/knowledge-center/{$slug}")->assertOk();
        }
    }

    public function test_unknown_service_and_article_pages_return_not_found(): void
    {
        $this->get('/services/not-a-service')->assertNotFound();
        $this->get('/knowledge-center/not-an-article')->assertNotFound();
        $this->get('/not-a-page')->assertNotFound();
    }

    public function test_services_overview_displays_approved_content_and_metadata(): void
    {
        $this->get('/services')
            ->assertOk()
            ->assertSee('<title>Language Services in East Africa | Nile Lingua Lab</title>', false)
            ->assertSee('Enterprise-Grade African Language Solutions Built for Global Impact')
            ->assertSee('language services East Africa')
            ->assertSee('Our Four Core Service Pillars')
            ->assertSee('Human-in-the-loop (HITL) evaluation')
            ->assertSee('How We Work')
            ->assertSee('Independent editing')
            ->assertSee('Kinyarwanda, Kirundi, Swahili, Luganda, Runyankore-Rukiga')
            ->assertSee('Academic research')
            ->assertSee('Book a Consultation')
            ->assertSee('Contact Our Team')
            ->assertSee('/services/localization')
            ->assertSee('/services/legal-technical-translation')
            ->assertSee('/services/subtitling')
            ->assertSee('/services/ai-language-data-mtpe');
    }

    public function test_localization_service_displays_approved_content_and_metadata(): void
    {
        $this->get('/services/localization')
            ->assertOk()
            ->assertSee('<title>Localization Services for African Languages | NLL</title>', false)
            ->assertSee('name="keywords" content="localization services for African languages"', false)
            ->assertSee('Localization Services for African Languages')
            ->assertSee('High-Impact Digital Adaptation for East &amp; Central African Markets', false)
            ->assertSee('Core Localization Capabilities')
            ->assertSee('NGO &amp; Community Outreach Adaptation', false)
            ->assertSee('How We Localize')
            ->assertSee('File and layout checks')
            ->assertSee('Runyankore-Rukiga')
            ->assertSee('Source languages:')
            ->assertSee('Trados Studio')
            ->assertSee('Wordfast Pro')
            ->assertSee('MultiTerm')
            ->assertSee('XLIFF')
            ->assertSee('WhatsApp +250 788 750 971')
            ->assertSee('href="https://wa.me/250788750971"', false)
            ->assertSee('Technology &amp; digital platforms', false);

        $this->get('/services/subtitling')
            ->assertOk()
            ->assertDontSee('Localization Services for African Languages')
            ->assertSee('Keep every voice in the conversation.');
    }

    public function test_contact_form_saves_a_valid_enquiry_and_shows_thank_you(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Avery Example',
            'email' => 'avery@example.com',
            'organization' => 'Example Studio',
            'service' => 'Localization',
            'message' => 'We are preparing a product launch.',
        ]);

        $response->assertRedirect('/thank-you')->assertSessionHas('enquiry_submitted', true);
        $this->assertDatabaseHas('project_enquiries', [
            'email' => 'avery@example.com',
            'service' => 'Localization',
        ]);

        $this->get('/thank-you')->assertOk()->assertSee('Your enquiry has been submitted.');
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $this->from('/contact')->post('/contact', [
            'name' => '',
            'email' => 'not-an-email',
            'message' => '',
        ])->assertRedirect('/contact')->assertSessionHasErrors(['name', 'email', 'message']);

        $this->assertSame(0, ProjectEnquiry::count());
    }
}
