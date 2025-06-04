<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocationRequest;
use App\Models\Location;
use App\Repositories\Location\LocationRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class LocationController extends Controller
{
    public function __construct(private readonly LocationRepositoryInterface $locationRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $locations = $this->locationRepository->paginate();

        if ($locations->isEmpty() && $locations->currentPage() > 1) {
            $targetPage = $locations->lastPage() > 0 ? $locations->lastPage() : 1;
            if ($targetPage == $locations->currentPage() && $targetPage > 1) {
                $targetPage--;
            }
            $targetPage = max(1, $targetPage);

            return redirect()->route('admin.locations.index', ['page' => $targetPage]);
        }

        return Inertia::render('Admin/Location', compact('locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LocationRequest $request)
    {
        $validate = $request->validated();

        try {
            $this->locationRepository->create($validate);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Ubicación creada correctamente']);
        } catch (Exception $e) {
            Log::error('Error creating location: '.$e);

            return redirect()->back()->with('toast', ['type' => 'error', 'message' => 'Error al crear la ubicación']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LocationRequest $request, Location $location)
    {
        try {
            $validated = $request->validated();

            $this->locationRepository->update($validated, $location);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Ubicación actualizada correctamente']);
        } catch (Exception $e) {
            Log::error('Error updating location: '.$e);

            return redirect()->back()->with('toast', ['type' => 'error', 'message' => 'Error al actualizar la ubicación']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        try {
            $this->locationRepository->delete($location);

            return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Ubicación eliminada correctamente']);
        } catch (Exception $e) {
            Log::error('Error deleting location: '.$e);

            return redirect()->back()->with('toast', ['type' => 'error', 'message' => 'Error al eliminar la ubicación']);
        }
    }
}
