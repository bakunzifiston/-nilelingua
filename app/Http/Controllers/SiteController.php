<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Public pages. Content comes from config('site'), which the SetLocale middleware fills with the
 * published CMS content for the active language (or the draft, in an admin preview).
 */
class SiteController extends Controller
{
    public function home(): View
    {
        $home = config('site.home');

        return view('site.home', [
            'page' => [
                'title' => $home['title'] ?? config('site.name'),
                'meta' => $home['meta'] ?? null,
                'keyphrase' => $home['keyphrase'] ?? null,
            ],
            'sections' => $home['sections'] ?? [],
        ]);
    }

    public function service(string $slug): View
    {
        $page = config("site.services.{$slug}");

        abort_if(! is_array($page), 404);

        return view('site.page', [
            'page' => $page,
            'kind' => 'service',
            'cards' => $page['cards'] ?? [],
        ]);
    }

    public function article(string $slug): View
    {
        $page = config("site.articles.{$slug}");

        abort_if(! is_array($page), 404);

        return view('site.page', [
            'page' => $page,
            'kind' => 'article',
            'cards' => [],
            'articleSlug' => $slug,
        ]);
    }

    public function page(string $slug): View
    {
        $page = config("site.pages.{$slug}");

        abort_if(! is_array($page), 404);

        $cards = match ($slug) {
            'services' => collect(config('site.services'))->map(fn ($service, $key) => [
                'title' => $service['title'],
                'body' => $service['intro'],
                'href' => "/services/{$key}",
                'label' => __('Explore service'),
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
    }

    public function thankYou(): View
    {
        return view('site.thank-you');
    }
}
