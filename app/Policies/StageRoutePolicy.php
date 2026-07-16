<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\StageRoute;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class StageRoutePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StageRoute');
    }

    public function view(AuthUser $authUser, StageRoute $stageRoute): bool
    {
        return $authUser->can('View:StageRoute');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StageRoute');
    }

    public function update(AuthUser $authUser, StageRoute $stageRoute): bool
    {
        return $authUser->can('Update:StageRoute');
    }

    public function delete(AuthUser $authUser, StageRoute $stageRoute): bool
    {
        return $authUser->can('Delete:StageRoute');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StageRoute');
    }

    public function restore(AuthUser $authUser, StageRoute $stageRoute): bool
    {
        return $authUser->can('Restore:StageRoute');
    }

    public function forceDelete(AuthUser $authUser, StageRoute $stageRoute): bool
    {
        return $authUser->can('ForceDelete:StageRoute');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StageRoute');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StageRoute');
    }

    public function replicate(AuthUser $authUser, StageRoute $stageRoute): bool
    {
        return $authUser->can('Replicate:StageRoute');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StageRoute');
    }
}
