<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeNewsletterRequest;
use App\Models\NewsletterSubscriber;
use App\Services\NewsletterService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function __construct(
        protected NewsletterService $newsletter
    ) {}

    /**
     * Handle public newsletter subscription.
     */
    public function subscribe(SubscribeNewsletterRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $source = $validated['source'] ?? 'homepage';
        $result = $this->newsletter->subscribe($validated['email'], $validated['name'] ?? null, $source);

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        $type = in_array($result['status'], ['subscribed', 'verification_sent', 'already_subscribed']) ? 'success' : 'info';

        return back()->with($type, $result['message']);
    }

    /**
     * Verify subscriber email via token link.
     */
    public function verify(string $token): View
    {
        $result = $this->newsletter->verify($token);

        return view('pages.newsletter.verify-result', compact('result'));
    }

    /**
     * Show unsubscribe confirmation page.
     */
    public function showUnsubscribe(string $uuid): View
    {
        $subscriber = NewsletterSubscriber::where('uuid', $uuid)->firstOrFail();

        return view('pages.newsletter.unsubscribe', compact('subscriber'));
    }

    /**
     * Process unsubscribe request.
     */
    public function unsubscribe(Request $request, string $uuid): RedirectResponse
    {
        $result = $this->newsletter->unsubscribe($uuid);

        return redirect()->route('home')->with('info', $result['message']);
    }
}
