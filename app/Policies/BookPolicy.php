<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    /**
     * Determine whether the user can download the book.
     */
    public function download(User $user, Book $book): bool
    {
        return $this->checkAccess($user, $book);
    }

    /**
     * Determine whether the user can read the book online.
     */
    public function readOnline(User $user, Book $book): bool
    {
        return $this->checkAccess($user, $book);
    }

    /**
     * Logique de vérification commune.
     */
    protected function checkAccess(User $user, Book $book): bool
    {
        // Les admins ont toujours accès
        if ($user->isAdmin()) {
            return true;
        }

        // L'auteur du livre a accès à son propre livre
        if ($user->isAuteur() && $book->author_id === $user->id) {
            return true;
        }

        // Si le livre n'est pas premium, tout le monde (authentifié) a accès
        if (!$book->is_premium) {
            return true;
        }

        // Si le livre est premium, l'utilisateur (lecteur) doit avoir un abonnement actif
        return $user->hasActiveSubscription();
    }
}
