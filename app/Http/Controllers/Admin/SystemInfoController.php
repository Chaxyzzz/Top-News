<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SystemInfoController extends Controller
{
    /**
     * Display safe system information and diagnostics.
     */
    public function index(Request $request): View
    {
        if (! Auth::user()->isSuperAdmin() && ! Auth::user()->hasRole('admin')) {
            abort(403, 'Akses tidak diizinkan untuk melihat informasi sistem.');
        }

        $databaseStatus = 'Terkoneksi';
        $databaseVersion = 'MySQL';
        try {
            $pdo = DB::connection()->getPdo();
            $databaseVersion = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
        } catch (\Throwable) {
            $databaseStatus = 'Gagal Terhubung';
        }

        $systemDetails = [
            'app_name' => config('app.name'),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug') ? 'Enabled (Local)' : 'Disabled (Production)',
            'url' => config('app.url'),
            'timezone' => config('app.timezone', 'Asia/Jakarta'),
            'locale' => config('app.locale', 'id'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_os' => PHP_OS_FAMILY,
            'database_driver' => config('database.default'),
            'database_version' => $databaseVersion,
            'database_status' => $databaseStatus,
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
            'mail_mailer' => config('mail.default'),
            'storage_symlink' => file_exists(public_path('storage')) ? 'Tersambung (Linked)' : 'Belum Tersambung',
        ];

        return view('admin.system.index', compact('systemDetails'));
    }
}
