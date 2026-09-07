<?php

namespace App\Http\Controllers;

use App\Services\HomepageService;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        protected HomepageService $homepageService
    ) {}

    /**
     * Display the dynamic TopNews homepage driven by real published content.
     */
    public function index(Request $request): View
    {
        $data = $this->homepageService->getHomepageData();
        $data['seoData'] = app(SeoService::class)->forHome();

        return view('pages.home', $data);
    }
}
