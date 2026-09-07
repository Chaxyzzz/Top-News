<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ContentController extends Controller
{
    /**
     * Display the Content Management overview and structure.
     */
    public function index(): View
    {
        return view('admin.content.index');
    }
}
