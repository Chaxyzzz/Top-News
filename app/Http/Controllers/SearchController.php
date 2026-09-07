<?php

namespace App\Http\Controllers;

use App\Services\ArticleSearchService;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ArticleSearchService $searchService
    ) {}

    /**
     * Display the search results page.
     */
    public function index(Request $request): View
    {
        $rawQuery = (string) $request->input('q', '');
        $query = $this->searchService->sanitizeQuery($rawQuery);
        $hasQuery = mb_strlen($query) >= 2;

        $results = $this->searchService->search($request, 12);
        $categories = $this->searchService->getActiveCategories();

        return view('pages.search', [
            'rawQuery' => $rawQuery,
            'query' => $query,
            'hasQuery' => $hasQuery,
            'results' => $results,
            'categories' => $categories,
            'selectedCategory' => $request->input('category'),
            'selectedType' => $request->input('type'),
            'selectedDate' => $request->input('date'),
            'selectedSort' => $request->input('sort', 'relevance'),
            'seoData' => app(SeoService::class)->forSearch($query, (int) $request->input('page', 1)),
        ]);
    }
}
