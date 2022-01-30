<?php

namespace Tests\Integration\Application\Note;

use App\Http\Application\Note\FindNoteByFileIds;
use App\Http\Application\Note\StoreNote;
use App\Models\Note;
use App\Models\PdfFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

final class StoreNoteTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_store_a_note_for_a_file(): void
    {
        $pdfFile = PdfFile::factory()->create();
        $text = 'some text';
        $fileIds = [$pdfFile->id];
        $findNoteByFileIds = new FindNoteByFileIds();
        $action = new StoreNote($findNoteByFileIds);
        $action->execute($text, $fileIds);
        self::assertDatabaseHas(Note::class, ['text' => $text]);
    }
}
