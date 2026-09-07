<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContactMessageStatus;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\User;
use App\Services\ContactMessageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class ContactMessageController extends Controller
{
    public function __construct(
        protected ContactMessageService $contactService
    ) {}

    /**
     * Display inbox list of contact messages.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ContactMessage::class);

        $query = ContactMessage::query()->with('assignedTo');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($assigned = $request->input('assigned_to')) {
            if ($assigned === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $assigned);
            }
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $counts = [
            'total' => ContactMessage::count(),
            'new' => ContactMessage::where('status', ContactMessageStatus::New)->count(),
            'in_progress' => ContactMessage::where('status', ContactMessageStatus::InProgress)->count(),
            'resolved' => ContactMessage::where('status', ContactMessageStatus::Resolved)->count(),
            'spam' => ContactMessage::where('status', ContactMessageStatus::Spam)->count(),
        ];

        $staffUsers = User::where('status', UserStatus::Active)
            ->where('account_type', 'staff')
            ->orderBy('name')
            ->get();

        return view('admin.contact-messages.index', compact('messages', 'counts', 'staffUsers'));
    }

    /**
     * Display message detail and mark as read.
     */
    public function show(ContactMessage $contactMessage): View
    {
        $this->authorize('view', $contactMessage);

        $this->contactService->markRead($contactMessage, Auth::user());

        $staffUsers = User::where('status', UserStatus::Active)
            ->where('account_type', 'staff')
            ->orderBy('name')
            ->get();

        return view('admin.contact-messages.show', compact('contactMessage', 'staffUsers'));
    }

    /**
     * Update status of message.
     */
    public function updateStatus(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $this->authorize('manage', $contactMessage);

        $validated = $request->validate([
            'status' => ['required', new Enum(ContactMessageStatus::class)],
        ]);

        $status = ContactMessageStatus::from($validated['status']);
        $this->contactService->updateStatus($contactMessage, $status, Auth::user());

        return back()->with('success', "Status pesan berhasil diubah menjadi '{$status->label()}'.");
    }

    /**
     * Assign message to staff user.
     */
    public function assign(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $this->authorize('assign', $contactMessage);

        $validated = $request->validate([
            'assigned_to' => 'nullable|integer|exists:users,id',
        ]);

        $staff = ! empty($validated['assigned_to']) ? User::find($validated['assigned_to']) : null;
        $this->contactService->assign($contactMessage, $staff, Auth::user());

        $assignedName = $staff ? $staff->name : 'belum ditugaskan';

        return back()->with('success', "Pesan berhasil ditugaskan ke {$assignedName}.");
    }

    /**
     * Mark message as spam.
     */
    public function markSpam(ContactMessage $contactMessage): RedirectResponse
    {
        $this->authorize('manage', $contactMessage);

        $this->contactService->markSpam($contactMessage, Auth::user());

        return back()->with('success', 'Pesan telah ditandai sebagai spam.');
    }

    /**
     * Delete message.
     */
    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $this->authorize('delete', $contactMessage);

        $contactMessage->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Pesan berhasil dihapus.');
    }
}
