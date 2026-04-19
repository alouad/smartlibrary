<?php

namespace App\Policies;

use App\Models\Book;

class BookPolicy
{
    /**
     * Determine if the given user can update the given book.
     */
    public function update($user, Book $book): bool
    {
        if ($user instanceof \App\Models\User) {
            return true; // Admin can update
        }

        if ($user instanceof \App\Models\Author) {
            return $user->id === $book->author_id;
        }

        return false;
    }

    /**
     * Determine if the given user can delete the given book.
     */
    public function delete($user, Book $book): bool
    {
        if ($user instanceof \App\Models\User) {
            return true; // Admin can delete
        }

        if ($user instanceof \App\Models\Author) {
            return $user->id === $book->author_id;
        }

        return false;
    }
}
