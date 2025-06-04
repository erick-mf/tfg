<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuCategoryRequest;
use App\Models\MenuCategory;
use App\Repositories\MenuCategory\MenuCategoryRepositoryInterface;
use Exception;
use Inertia\Inertia;

class MenuCategoryController extends Controller
{
    public function __construct(private readonly MenuCategoryRepositoryInterface $menuCategoryRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $menuCategories = $this->menuCategoryRepository->paginate();

            if ($menuCategories->isEmpty() && $menuCategories->currentPage() > 1) {
                $targetPage = $menuCategories->lastPage() > 0 ? $menuCategories->lastPage() : 1;
                if ($targetPage == $menuCategories->currentPage() && $targetPage > 1) {
                    $targetPage--;
                }
                $targetPage = max(1, $targetPage);

                return redirect()->route('admin.categories.index', ['page' => $targetPage]);
            }

            return Inertia::render('Admin/MenuCategory', compact('menuCategories'));
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);

        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuCategoryRequest $request)
    {
        $validated = $request->validated();
        try {
            $this->menuCategoryRepository->create($validated);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Categoría creada correctamente']);
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MenuCategoryRequest $request, MenuCategory $category)
    {
        $validated = $request->validated();
        try {
            $this->menuCategoryRepository->update($validated, $category);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Categoría actualizada correctamente']);
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuCategory $category)
    {
        try {
            $this->menuCategoryRepository->delete($category);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Categoría eliminada correctamente']);
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
