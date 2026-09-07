<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of system audit and security logs.
     */
    public function index(Request $request): View
    {
        if (! Auth::user()->hasPermission('audit.view')) {
            abort(403, 'Akses tidak diizinkan untuk melihat catatan audit keamanan.');
        }

        $query = AuditLog::with('user')->latest('created_at');

        // Search in description, action, or user name
        if ($search = $request->query('search')) {
            $searchTerm = '%'.trim($search).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', $searchTerm)
                    ->orWhere('action', 'like', $searchTerm)
                    ->orWhereHas('user', function ($uq) use ($searchTerm) {
                        $uq->where('name', 'like', $searchTerm)
                            ->orWhere('email', 'like', $searchTerm);
                    });
            });
        }

        // Action Filter
        if ($action = $request->query('action')) {
            $query->where('action', $action);
        }

        // Distinct available actions from existing logs
        $availableActions = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $logs = $query->paginate(20)->withQueryString();

        return view('admin.audit-logs.index', compact('logs', 'availableActions'));
    }
}
