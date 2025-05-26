<?php

namespace App\Repositories\Location;

use App\Models\Location;
use Exception;

class LocationRepository implements LocationRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(private Location $location)
    {
        //
    }

    public function paginate(int $perPage = 15)
    {
        return $this->location->paginate($perPage);
    }

    public function findById(int $id)
    {
        return $this->location->find($id);
    }

    public function create(array $data)
    {
        $exists = $this->location->where('name', $data['name'])->exists();

        if ($exists) {
            throw new Exception('Location already exists');
        }

        $created = $this->location->create($data);

        if (! $created) {
            throw new Exception('Error creating location');
        }

        return $created;
    }

    public function update(array $data, Location $location)
    {
        $updated = $location->update($data);

        if (! $updated) {
            throw new Exception('Error updating location');
        }

        return $location;
    }

    public function delete(Location $location)
    {
        $deleted = $location->delete();

        if (! $deleted) {
            throw new Exception('Error deleting location');
        }

        return $deleted;
    }
}
