<?php

namespace Tests\Integration\Application\Note;

use App\Http\Application\Note\FileIdsCannotBeEmptyException;
use App\Http\Application\Note\FindNoteByFileIds;
use App\Models\Note;
use App\Models\PdfFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

final class FindNoteByFileIdsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_find_a_note_that_belongs_to_files(): void
    {
        $fileA = PdfFile::factory()->create();
        $fileB = PdfFile::factory()->create();
        $text = 'some text';
        $note = Note::factory()->create([
            'pdf_file_ids' => [
                $fileA->id,
                $fileB->id,
            ],
            'text' => $text,
        ]);

        $action = new FindNoteByFileIds();
        $result = $action->execute([
            $fileA->id,
            $fileB->id,
        ]);
        self::assertEquals($note->id, $result->id);
    }

    /** @test */
    public function it_cannot_find_a_note_with_empty_file_ids(): void
    {
        $fileA = PdfFile::factory()->create();
        $fileB = PdfFile::factory()->create();
        $text = 'some text';
        $note = Note::factory()->create([
            'pdf_file_ids' => [
                $fileA->id,
                $fileB->id,
            ],
            'text' => $text,
        ]);

        $action = new FindNoteByFileIds();
        self::expectException(FileIdsCannotBeEmptyException::class);
        $action->execute([]);
    }

    /** @test */
    public function it_cannot_find_a_note_if_there_is_nothing_saved(): void
    {
        $fileA = PdfFile::factory()->create();
        $fileB = PdfFile::factory()->create();
        $action = new FindNoteByFileIds();
        self::expectException(ModelNotFoundException::class);
        $action->execute([
            $fileA->id,
            $fileB->id,
        ]);
    }
}
