<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssetPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Both admin and uploader can view assets
    }

    public function view(User $user, Asset $asset)
    {
        return $user->hasRole('admin') ||
               ($user->hasRole('uploader') && $asset->uploaded_by === $user->id);
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['admin', 'uploader']);
    }

    public function update(User $user, Asset $asset)
    {
        return $user->hasRole('admin') ||
               ($user->hasRole('uploader') && $asset->uploaded_by === $user->id);
    }

    public function delete(User $user, Asset $asset)
    {
        return $user->hasRole('admin') ||
               ($user->hasRole('uploader') && $asset->uploaded_by === $user->id);
    }

    public function download(User $user, Asset $asset)
    {
        return $this->view($user, $asset);
    }

    public function share(User $user, Asset $asset)
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, Asset $asset)
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Asset $asset)
    {
        return $user->hasRole('admin');
    }
}
