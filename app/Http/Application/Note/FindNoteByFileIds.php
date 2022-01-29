<?php

namespace App\Http\Application\Note;

use App\Models\Note;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FindNoteByFileIds
{
    /**
     * @throws FileIdsCannotBeEmptyException
     * @throws ModelNotFoundException
     */
    public function execute(array $fileIds): Note
    {
        if (empty($fileIds)) {
            throw new FileIdsCannotBeEmptyException('Note does not belong to any file');
        }

        return Note::whereJsonContains('pdf_file_ids', $fileIds)
            ->whereJsonLength('pdf_file_ids', count($fileIds))
            ->FirstOrFail();
    }
}
