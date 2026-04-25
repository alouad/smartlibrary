<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookController extends Controller
{
    /**
     * Download the book's PDF.
     */
    public function downloadPdf(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        // Autorisation via la Policy
        if (!Gate::allows('download', $book)) {
            return response()->json([
                'message' => 'Accès refusé. Un abonnement premium actif est requis pour télécharger ce livre.'
            ], 403);
        }

        // Simuler le chemin du fichier (à adapter selon le stockage réel)
        $path = storage_path('app/private/books/' . $book->file_path);

        if (!file_exists($path)) {
            return response()->json(['message' => 'Fichier introuvable.'], 404);
        }

        return response()->download($path, $book->title . '.pdf');
    }

    /**
     * Read the book online.
     */
    public function readOnline(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        // Autorisation via la Policy
        if (!Gate::allows('readOnline', $book)) {
            return response()->json([
                'message' => 'Accès refusé. Un abonnement premium actif est requis pour lire ce livre.'
            ], 403);
        }

        // Retourner les infos nécessaires pour le lecteur React (ex: URL du PDF ou des pages)
        return response()->json([
            'book' => $book,
            'pdf_url' => url('/api/books/' . $book->id . '/stream'),
            'message' => 'Livre prêt pour la lecture en ligne.'
        ]);
    }
}
