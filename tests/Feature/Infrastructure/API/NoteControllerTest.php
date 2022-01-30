<?php

namespace Tests\Feature\Infrastructure\API;

use App\Models\Note;
use App\Models\PdfFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NoteControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_can_get_a_note_of_a_single_file(): void
    {
        $file = PdfFile::factory()->create();
        $note = Note::factory()->create(['pdf_file_ids' => [$file->id]]);
        self::json('GET', $this->getUrl(), ['pdf_files' => [$file->id]])
            ->assertOk()
            ->assertJsonFragment([
                'note' => $note->text,
            ]);
    }

    /** @test */
    public function user_can_get_a_note_of_multiple_files(): void
    {
        $fileA = PdfFile::factory()->create();
        $fileB = PdfFile::factory()->create();
        $note = Note::factory()->create(['pdf_file_ids' => [$fileA->id, $fileB->id]]);
        self::json('GET', $this->getUrl(), ['pdf_files' => [$fileA->id, $fileB->id]])
            ->assertOk()
            ->assertJsonFragment([
                'note' => $note->text,
            ]);
    }

    /** @test */
    public function user_cannot_get_a_note_of_multiple_files_by_sending_part_of_file_id(): void
    {
        $fileA = PdfFile::factory()->create();
        $fileB = PdfFile::factory()->create();
        $note = Note::factory()->create(['pdf_file_ids' => [$fileA->id, $fileB->id]]);
        self::json('GET', $this->getUrl(), ['pdf_files' => [$fileA->id]])
            ->assertOk()
            ->assertJsonMissing([
                'note' => $note->text,
            ]);
    }

    /** @test */
    public function user_can_store_a_note_of_a_file(): void
    {
        $fileA = PdfFile::factory()->create();
        $data =[
            'text' => 'some note',
            'file_ids' => [
                $fileA->id,
            ],
        ];

        self::json('POST', $this->getUrl(), $data)->assertStatus(204);
        self::assertTrue(Note::where('text', $data['text'])->exists());
    }

    /** @test */
    public function user_can_store_a_note_without_any_file_id(): void
    {
        self::json('POST', $this->getUrl())
            ->assertStatus(400);
    }

    private function getUrl(): string
    {
        return '/api/note';
    }
}
