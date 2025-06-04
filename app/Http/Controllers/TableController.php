<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableRequest;
use App\Models\Table;
use App\Repositories\Table\TableRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TableController extends Controller
{
    public function __construct(private readonly TableRepositoryInterface $tableRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // NOTE: para que esto funcione, inertia debe usar preserveState: false
            $tables = $this->tableRepository->paginate();
            if ($tables->isEmpty() && $tables->currentPage() > 1) {
                $targetPage = $tables->lastPage() > 0 ? $tables->lastPage() : 1;
                if ($targetPage == $tables->currentPage() && $targetPage > 1) {
                    $targetPage--;
                }
                $targetPage = max(1, $targetPage);

                return redirect()->route('admin.tables.index', ['page' => $targetPage]);
            }

            return Inertia::render('Table', compact('tables'));

        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TableRequest $request)
    {
        try {
            $validated = $request->validated();
            $this->tableRepository->create($validated);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Mesa creada correctamente']);
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TableRequest $request, Table $table)
    {
        try {
            $validated = $request->validated();
            $this->tableRepository->update($validated, $table);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Mesa actualizada correctamente']);
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Table $table)
    {
        try {
            $this->tableRepository->delete($table);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Mesa eliminada correctamente']);
        } catch (Exception $e) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
