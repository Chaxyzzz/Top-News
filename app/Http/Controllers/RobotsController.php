<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Serve environment-aware robots.txt.
     */
    public function __invoke(): Response
    {
        $isProduction = app()->isProduction();

        if (! $isProduction) {
            $content = "User-agent: *\nDisallow: /\n";
        } else {
            $sitemapIndex = url('/sitemap.xml');
            $newsSitemap = url('/news-sitemap.xml');

            $content = "User-agent: *\n";
            $content .= "Disallow: /admin/\n";
            $content .= "Disallow: /account/\n";
            $content .= "Disallow: /login\n";
            $content .= "Disallow: /register\n";
            $content .= "Disallow: /password/\n";
            $content .= "Disallow: /*preview*\n\n";
            $content .= "Sitemap: {$sitemapIndex}\n";
            $content .= "Sitemap: {$newsSitemap}\n";
        }

        return response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
