<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuItemRequest;
use App\Models\MenuItem;
use App\Repositories\MenuCategory\MenuCategoryRepositoryInterface;
use App\Repositories\MenuItem\MenuItemRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MenuItemController extends Controller
{
    private const IMG_DEFAULT = 'default.webp';

    public function __construct(
        private readonly MenuItemRepositoryInterface $menuItemRepository,
        private readonly MenuCategoryRepositoryInterface $menuCategoryRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $menuItems = $this->menuItemRepository->paginate();
            $categories = $this->menuCategoryRepository->all();

            return Inertia::render('Admin/MenuItem', compact('menuItems', 'categories'));
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuItemRequest $request)
    {
        try {
            $validated = $request->validated();
            $imagePath = null;

            if ($request->hasFile('image_path') && $request->file('image_path')->isValid()) {
                $imagePath = $request->file('image_path')->store('img/menu-items', 'public');
                $validated['image_path'] = basename($imagePath);

            } else {
                $validated['image_path'] = self::IMG_DEFAULT;
            }

            $this->menuItemRepository->create($validated);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Item creado correctamente']);
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MenuItemRequest $request, MenuItem $menuItem)
    {
        try {
            $validated = $request->validated();
            $currentImagePath = $menuItem->image_path;

            if ($request->hasFile('image_path') && $request->file('image_path')->isValid()) {
                $newImageStoredPath = $request->file('image_path')->store('img/menu-items', 'public');
                $validated['image_path'] = basename($newImageStoredPath);

                if ($currentImagePath && $currentImagePath !== $validated['image_path'] && $currentImagePath !== self::IMG_DEFAULT) {
                    $fullOldPath = 'img/menu-items/'.$currentImagePath;
                    if (Storage::disk('public')->exists($fullOldPath)) {
                        Storage::disk('public')->delete($fullOldPath);
                    }
                }
            } else {
                unset($validated['image_path']);
            }

            $this->menuItemRepository->update($validated, $menuItem);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Item actualizado correctamente']);

        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuItem $menuItem)
    {
        try {
            $imagePath = $menuItem->image_path;

            $this->menuItemRepository->delete($menuItem);

            if ($imagePath && $imagePath !== self::IMG_DEFAULT) {
                $fullPath = 'img/menu-items/'.$imagePath;
                if (Storage::disk('public')->exists($fullPath)) {
                    Storage::disk('public')->delete($fullPath);
                }
            }

            return redirect()->back()
                ->with('toast', ['type' => 'success', 'message' => 'Ítem de menú eliminado exitosamente.']);
        } catch (Exception $e) {
            return redirect()->back()
                ->with('toast', ['type' => 'error', 'message' => 'Error al eliminar el ítem de menú: '.$e->getMessage()]);
        }
    }
}
