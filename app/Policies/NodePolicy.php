<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Node;
use Illuminate\Auth\Access\HandlesAuthorization;

class NodePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Node');
    }

    public function view(AuthUser $authUser, Node $node): bool
    {
        return $authUser->can('View:Node');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Node');
    }

    public function update(AuthUser $authUser, Node $node): bool
    {
        return $authUser->can('Update:Node');
    }

    public function delete(AuthUser $authUser, Node $node): bool
    {
        return $authUser->can('Delete:Node');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Node');
    }

    public function restore(AuthUser $authUser, Node $node): bool
    {
        return $authUser->can('Restore:Node');
    }

    public function forceDelete(AuthUser $authUser, Node $node): bool
    {
        return $authUser->can('ForceDelete:Node');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Node');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Node');
    }

    public function replicate(AuthUser $authUser, Node $node): bool
    {
        return $authUser->can('Replicate:Node');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Node');
    }

}