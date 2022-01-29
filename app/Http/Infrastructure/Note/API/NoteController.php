<?php

namespace App\Http\Infrastructure\Note\API;

use App\Http\Application\Note\FindNoteByFileIds;
use App\Http\Application\Note\FileIdsCannotBeEmptyException;
use App\Http\Application\Note\StoreNote;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class NoteController extends Controller
{
    public function index(FindNoteByFileIds $findNoteByFileIds, Request $request): Response|JsonResponse|Application|ResponseFactory
    {
        try {
            $note = $findNoteByFileIds->execute($request->get('pdf_files') ?? []);

            return new JsonResponse(['note' => $note->text]);
        } catch (ModelNotFoundException|FileIdsCannotBeEmptyException) {
            return new JsonResponse(['note' => '']);
        }
    }

    public function store(StoreNote $storeNote, Request $request): Response
    {
        try {
            $storeNote->execute(
                $request->input('text') ?? '',
                $request->input('file_ids') ?? [],
            );

            return response('', 204);
        } catch (FileIdsCannotBeEmptyException) {
            return response('', 400);
        }
    }
}
