<?php

namespace App\Repositories;

use App\Models\Profile;

class ProfileRepository
{
    public function getAll()
    {
        return Profile::with('user.roles')->paginate(15);
    }

    public function findById(int $id)
    {
        return Profile::findOrFail($id);
    }

    public function create(array $data)
    {
        return Profile::create($data);
    }

    public function update(Profile $profile, array $data)
    {
        $profile->update($data);
        return $profile;
    }

    public function delete(Profile $profile)
    {
        return $profile->delete();
    }
}
