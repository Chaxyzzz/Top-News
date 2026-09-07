<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTagRequest;
use App\Http\Requests\Admin\UpdateTagRequest;
use App\Models\Tag;
use App\Services\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLog
    ) {}

    /**
     * Display a listing of tags.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Tag::class);

        $query = Tag::withCount('articles')->latest('id');

        if ($search = $request->query('search')) {
            $query->where('name', 'like', '%'.trim($search).'%');
        }

        $tags = $query->paginate(20)->withQueryString();

        return view('admin.tags.index', compact('tags'));
    }

    /**
     * Store a newly created tag in storage.
     */
    public function store(StoreTagRequest $request): RedirectResponse
    {
        $tag = Tag::create($request->validated());
        $this->auditLog->log('tag.created', $tag, "Tagar '#{$tag->name}' dibuat oleh ".Auth::user()->name.'.', Auth::user());

        return redirect()->route('admin.tags.index')
            ->with('success', "Tagar '#{$tag->name}' berhasil ditambahkan.");
    }

    /**
     * Update the specified tag in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag): RedirectResponse
    {
        $tag->update($request->validated());
        $this->auditLog->log('tag.updated', $tag, "Tagar '#{$tag->name}' diperbarui oleh ".Auth::user()->name.'.', Auth::user());

        return redirect()->route('admin.tags.index')
            ->with('success', "Tagar '#{$tag->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified tag from storage.
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $this->authorize('delete', $tag);

        $name = $tag->name;
        $tag->delete();
        $this->auditLog->log('tag.deleted', $tag, "Tagar '#{$name}' dihapus oleh ".Auth::user()->name.'.', Auth::user());

        return redirect()->route('admin.tags.index')
            ->with('success', "Tagar '#{$name}' berhasil dihapus.");
    }
}
