<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;

class AttachmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('attachments.viewAny');
    }

    public function view(User $user, Attachment $attachment): bool
    {
        return $user->hasPermissionTo('attachments.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('attachments.create');
    }

    public function delete(User $user, Attachment $attachment): bool
    {
        return $user->hasPermissionTo('attachments.delete');
    }
}
