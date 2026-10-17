<?php

namespace App\Http\Middleware;

use App\Support\Cms\SiteContent;
use App\Support\Localization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Applies the route's locale: translation strings, translated site content and localized links.
     */
    public function handle(Request $request, Closure $next, string $locale = Localization::DEFAULT): Response
    {
        $locale = array_key_exists($locale, Localization::LOCALES) ? $locale : Localization::DEFAULT;
        App::setLocale($locale);

        // Site content for this language: the published CMS content (see App\Support\Cms\SiteContent).
        config(['site' => SiteContent::published($locale)]);

        $response = $next($request);

        if ($locale !== Localization::DEFAULT && str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            $response->setContent(Localization::localizeLinks((string) $response->getContent(), $locale));
        }

        if ($response->isRedirection() && $locale !== Localization::DEFAULT) {
            $target = $response->headers->get('Location');
            $base = rtrim(url('/'), '/');
            $path = str_starts_with((string) $target, $base) ? substr($target, strlen($base)) : $target;

            if ($path !== null && str_starts_with($path, '/') && Localization::stripPrefix(parse_url($path, PHP_URL_PATH) ?? '/') === (parse_url($path, PHP_URL_PATH) ?? '/')) {
                $response->headers->set('Location', $base.Localization::path($path, $locale));
            }
        }

        $response->headers->set('Content-Language', $locale);

        return $response;
    }
}
