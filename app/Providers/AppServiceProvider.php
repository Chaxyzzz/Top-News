<?php

namespace App\Providers;

use App\Enums\ContactMessageStatus;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.admin', function ($view) {
            $count = 0;
            if (auth()->check() && auth()->user()->can('viewAny', ContactMessage::class)) {
                $count = ContactMessage::where('status', ContactMessageStatus::New)->count();
            }
            $view->with('newContactsCount', $count);
        });
    }
}
