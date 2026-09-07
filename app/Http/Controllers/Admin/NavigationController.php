<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuItemRequest;
use App\Http\Requests\UpdateMenuItemRequest;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\NavigationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NavigationController extends Controller
{
    public function __construct(
        protected NavigationService $navService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Menu::class);

        $currentKey = $request->input('menu', 'primary');
        $menus = Menu::all();
        $activeMenu = Menu::where('key', $currentKey)->first() ?? $menus->first();

        $items = $activeMenu ? $activeMenu->items()
            ->whereNull('parent_id')
            ->with(['children.category', 'category'])
            ->orderBy('sort_order')
            ->get() : collect();

        $categories = Category::active()->orderBy('name')->get();
        $allowedRoutes = StoreMenuItemRequest::ALLOWED_ROUTES;

        return view('admin.navigation.index', compact('menus', 'activeMenu', 'items', 'categories', 'allowedRoutes', 'currentKey'));
    }

    public function storeItem(StoreMenuItemRequest $request): RedirectResponse
    {
        $this->authorize('create', MenuItem::class);

        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['open_new_tab'] = $request->boolean('open_new_tab', false);

        if (! isset($validated['sort_order'])) {
            $maxOrder = MenuItem::where('menu_id', $validated['menu_id'])->max('sort_order') ?? 0;
            $validated['sort_order'] = $maxOrder + 1;
        }

        $item = MenuItem::create($validated);
        $this->navService->clearCache($item->menu->key);

        return back()->with('success', "Item menu '{$item->label}' berhasil ditambahkan.");
    }

    public function updateItem(UpdateMenuItemRequest $request, MenuItem $item): RedirectResponse
    {
        $this->authorize('update', $item);

        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['open_new_tab'] = $request->boolean('open_new_tab', false);

        $item->update($validated);
        $this->navService->clearCache($item->menu->key);

        return back()->with('success', "Item menu '{$item->label}' berhasil diperbarui.");
    }

    public function destroyItem(MenuItem $item): RedirectResponse
    {
        $this->authorize('delete', $item);

        $label = $item->label;
        $menuKey = $item->menu->key;
        $item->delete();

        $this->navService->clearCache($menuKey);

        return back()->with('success', "Item menu '{$label}' berhasil dihapus.");
    }

    public function reorderItems(Request $request, Menu $menu): RedirectResponse
    {
        $this->authorize('manage', $menu);

        $orderMap = $request->input('order', []);
        foreach ($orderMap as $itemId => $order) {
            MenuItem::where('id', $itemId)->where('menu_id', $menu->id)->update(['sort_order' => (int) $order]);
        }

        $this->navService->clearCache($menu->key);

        return back()->with('success', 'Urutan navigasi berhasil disimpan.');
    }
}
