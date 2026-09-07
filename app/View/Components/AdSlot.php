<?php

namespace App\View\Components;

use App\Models\Advertisement;
use App\Services\AdvertisementService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AdSlot extends Component
{
    public ?Advertisement $ad;

    public function __construct(
        public string $key,
        public ?string $device = null,
        protected ?AdvertisementService $adService = null
    ) {
        $this->adService = $this->adService ?? app(AdvertisementService::class);
        $this->ad = $this->adService->getEligibleAdForSlot($this->key, $this->device);
    }

    public function render(): View|Closure|string
    {
        return view('components.ad-slot');
    }
}
