<?php

namespace App\Http\Application\Note;

use App\Models\Note;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StoreNote
{
    public function __construct(
        private FindNoteByFileIds $findNoteByFileIds
    )
    {
    }

    /**
     * @param string $text
     * @param array $fileIds
     *
     * @throws FileIdsCannotBeEmptyException
     */
    public function execute(string $text, array $fileIds): void
    {
        try {
            $note = $this->findNoteByFileIds->execute($fileIds);
            $note->text = $text;
            $note->save();
        } catch (ModelNotFoundException) {
            Note::create([
                'text' => $text,
                'pdf_file_ids' => $fileIds,
            ]);
        }
    }
}
