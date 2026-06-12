<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttachmentPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function delete(User $user, Attachment $attachment): bool
    {
        if ($attachment->user_id === $user->id) {
            return true;
        }

        $attachable = $attachment->attachable;

        return $attachable
            ? $user->can('update', $attachable)
            : false;
    }

    public function view(User $user, Attachment $attachment): bool
    {
        $attachable = $attachment->attachable;

        return $attachable
            ? $user->can('view', $attachable)
            : false;
    }
}
