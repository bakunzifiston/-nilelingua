<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use App\Models\NewsletterSubscriber;
use App\Models\ProjectEnquiry;

Route::get('/', fn () => view('site.home', [
    'page' => [
        'title' => 'African Language Services & AI Data | Nile Lingua Lab',
        'meta' => 'Nile Lingua Lab delivers African language services from Kigali: localization, Kinyarwanda and Swahili translation, subtitling and AI data. Request a quote.',
        'keyphrase' => 'African language services',
        'eyebrow' => 'Kigali, Rwanda · African language expertise',
        'headline' => 'African Language Solutions & Human-in-the-Loop AI Data',
        'intro' => 'Empowering global organizations to engage seamlessly with African audiences.',
    ],
    'services' => [
        [
            'title' => 'Localization & Digital Adaptation',
            'body' => 'Make your apps, websites, software platforms and digital content feel native to African markets. We go beyond literal translation to adapt interface elements, date and time conventions, local idioms and user workflows.',
            'href' => '/services/localization',
            'label' => 'Learn more',
            'icon' => '↗',
        ],
        [
            'title' => 'Legal, Technical & Corporate Translation',
            'body' => 'High-accuracy translation of legal documents, court records, regulatory filings, corporate communications and technical manuals, across African and international languages.',
            'href' => '/services/legal-technical-translation',
            'label' => 'Learn more',
            'icon' => '§',
        ],
        [
            'title' => 'Audio & Audiovisual Solutions: Subtitling & Transcription',
            'body' => 'End-to-end audiovisual adaptation for media networks, streaming platforms and enterprise learning.',
            'icon' => '≋',
            'deliverables' => [
                [
                    'title' => 'Time-Coded Subtitling & Closed Captioning',
                    'body' => 'Subtitles for film, e-learning and corporate video.',
                    'href' => '/services/subtitling',
                ],
                [
                    'title' => 'Audio & Video Transcription',
                    'body' => 'Verbatim or clean-verbatim transcripts with speaker identification.',
                    'href' => '/services/transcription',
                ],
                [
                    'title' => 'Voiceover & Audio QA',
                    'body' => 'Native voice recording and linguistic quality assurance.',
                ],
            ],
        ],
        [
            'title' => 'AI Language Data & MTPE',
            'body' => 'Human expertise for speech technology, machine translation and Large Language Models (LLMs).',
            'href' => '/services/ai-language-data-mtpe',
            'label' => 'Learn more',
            'icon' => '✳',
            'label' => 'Learn more',
            'deliverables' => [
                [
                    'title' => 'Speech & Text Datasets',
                    'body' => 'Native audio collection, text annotation, sentiment scoring and domain-specific dataset curation.',
                ],
                [
                    'title' => 'Machine Translation Post-Editing (MTPE)',
                    'body' => 'Professional human-in-the-loop review, in Light and Full levels, so machine output meets publication standards.',
                ],
            ],
        ],
    ],
]));

Route::get('/services/{slug}', function (string $slug) {
    $page = config("site.services.{$slug}");

    abort_if($page === null, 404);

    return view('site.page', [
        'page' => $page,
        'kind' => 'service',
        'cards' => $page['cards'] ?? [],
    ]);
})->whereIn('slug', array_keys(config('site.services')));

Route::get('/knowledge-center/{slug}', function (string $slug) {
    $page = config("site.articles.{$slug}");

    abort_if($page === null, 404);

    return view('site.page', [
        'page' => $page,
        'kind' => 'article',
        'cards' => [],
    ]);
})->whereIn('slug', array_keys(config('site.articles')));

Route::post('/contact', function (Request $request) {
    $enquiry = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:255'],
        'organization' => ['nullable', 'string', 'max:160'],
        'service' => ['nullable', 'string', 'max:120'],
        'message' => ['required', 'string', 'max:5000'],
    ]);

    ProjectEnquiry::create($enquiry);

    return redirect('/thank-you')->with('enquiry_submitted', true);
})->name('contact.submit');

Route::post('/newsletter', function (Request $request) {
    // Hidden honeypot field: real visitors leave it empty, simple bots fill it in.
    if ($request->filled('website')) {
        return redirect()->to(url()->previous().'#newsletter');
    }

    $validator = Validator::make($request->only('email'), [
        'email' => ['required', 'email', 'max:255'],
    ]);

    // Return to the footer form (not the top of the page) so the message is visible.
    if ($validator->fails()) {
        return redirect()->to(url()->previous().'#newsletter')->withErrors($validator, 'newsletter')->withInput();
    }

    $subscriber = NewsletterSubscriber::firstOrCreate(['email' => strtolower(trim($validator->validated()['email']))]);

    return redirect()->to(url()->previous().'#newsletter')
        ->with('newsletter_status', $subscriber->wasRecentlyCreated ? 'subscribed' : 'already');
})->name('newsletter.subscribe')->middleware('throttle:6,1');

Route::get('/thank-you', fn () => view('site.thank-you'));

Route::get('/{slug}', function (string $slug) {
    $page = config("site.pages.{$slug}");

    abort_if($page === null, 404);

    $cards = match ($slug) {
        'services' => collect(config('site.services'))->map(fn ($service, $key) => [
            'title' => $service['title'],
            'body' => $service['intro'],
            'href' => "/services/{$key}",
            'label' => 'Explore service',
        ])->values()->all(),
        'knowledge-center' => collect(config('site.articles'))->map(fn ($article, $key) => [
            'title' => $article['title'],
            'body' => $article['intro'],
            'href' => "/knowledge-center/{$key}",
            'label' => $article['category'].' · '.$article['read_time'],
        ])->values()->all(),
        default => $page['cards'] ?? [],
    };

    return view('site.page', [
        'page' => $page,
        'kind' => $slug,
        'cards' => $cards,
        'faqs' => $slug === 'faq' ? config('site.faq') : [],
    ]);
})->whereIn('slug', array_keys(config('site.pages')));
