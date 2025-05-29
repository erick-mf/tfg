<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Repositories\Location\LocationRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface;
use Exception;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly LocationRepositoryInterface $locationRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $products = $this->productRepository->paginate();
            $locations = $this->locationRepository->all();

            return Inertia::render('Product', compact('products', 'locations'));
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        try {
            $validated = $request->validated();
            $this->productRepository->create($validated);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Producto creado correctamente']);
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        try {
            $validated = $request->validated();
            $this->productRepository->update($validated, $product);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Producto actualizado correctamente']);
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $this->productRepository->delete($product);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Producto eliminado correctamente']);
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
