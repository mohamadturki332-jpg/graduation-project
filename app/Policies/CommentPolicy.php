<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function delete(User $user, Comment $comment): bool
    {
        // Admins can remove any comment; everyone else may only delete their own.
        return $user->hasRole('admin') || $comment->user_id === $user->id;
    }
}
