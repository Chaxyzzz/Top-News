<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Services\HomepageService;
use Illuminate\View\View;

class HomepagePreviewController extends Controller
{
    public function preview(HomepageService $homepageService): View
    {
        $this->authorize('viewAny', HomepageSection::class);

        $data = $homepageService->getHomepageData();
        $data['isPreview'] = true;

        return view('pages.home', $data);
    }
}
