<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Services\AdvertisementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class AdTrackingController extends Controller
{
    public function __construct(
        protected AdvertisementService $adService
    ) {}

    /**
     * Track ad click and redirect safely to stored destination.
     * Note: NEVER accepts an external ?url= parameter to avoid open redirects.
     */
    public function click(Advertisement $advertisement): RedirectResponse
    {
        $destination = $this->adService->recordClick($advertisement);

        return redirect()->away($destination);
    }

    /**
     * Client-side beacon for tracking visible ad impression.
     */
    public function impression(Advertisement $advertisement): Response
    {
        $this->adService->recordImpression($advertisement);

        return response()->noContent();
    }
}
