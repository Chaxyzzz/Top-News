<?php

namespace App\Http\Controllers\Admin;

use App\Enums\NewsletterSubscriberStatus;
use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Services\NewsletterService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NewsletterSubscriberController extends Controller
{
    public function __construct(
        protected NewsletterService $newsletter
    ) {}

    /**
     * Display a listing of newsletter subscribers.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', NewsletterSubscriber::class);

        $query = NewsletterSubscriber::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $subscribers = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $counts = [
            'total' => NewsletterSubscriber::count(),
            'active' => NewsletterSubscriber::active()->count(),
            'pending' => NewsletterSubscriber::pending()->count(),
            'unsubscribed' => NewsletterSubscriber::unsubscribed()->count(),
            'blocked' => NewsletterSubscriber::blocked()->count(),
        ];

        return view('admin.newsletter.index', compact('subscribers', 'counts'));
    }

    /**
     * Toggle block/reactivate status.
     */
    public function toggleBlock(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $this->authorize('manage', $subscriber);

        if ($subscriber->status === NewsletterSubscriberStatus::Blocked) {
            $this->newsletter->reactivate($subscriber, Auth::user());
            $msg = "Pelanggan '{$subscriber->email}' berhasil diaktifkan kembali.";
        } else {
            $this->newsletter->block($subscriber, Auth::user());
            $msg = "Pelanggan '{$subscriber->email}' telah diblokir.";
        }

        return back()->with('success', $msg);
    }

    /**
     * Export subscribers list to CSV.
     */
    public function export(Request $request): Response
    {
        $this->authorize('export', NewsletterSubscriber::class);

        $query = NewsletterSubscriber::query();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $csv = $this->newsletter->exportCsv($query);
        $filename = 'newsletter-subscribers-'.now()->format('Y-m-d-His').'.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Delete subscriber record.
     */
    public function destroy(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $this->authorize('manage', $subscriber);

        $email = $subscriber->email;
        $subscriber->delete();

        return back()->with('success', "Data pelanggan '{$email}' telah dihapus.");
    }
}
