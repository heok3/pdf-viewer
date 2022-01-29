<?php

namespace Tests\Feature\Infrastructure\WebPage;

use App\Models\PdfFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

final class PdfFileControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function anyone_can_be_redirected_to_main_page_when_they_visit_home(): void
    {
        self::get('/home')->assertRedirect('/');
    }

    /** @test */
    public function anyone_can_be_redirected_to_main_page_when_they_visit_pdfs_page(): void
    {
        self::get('/pdfs')->assertRedirect('/');
    }

    /** @test */
    public function anyone_can_see_list_of_pdfs(): void
    {
        $pdfFileA = PdfFile::factory()->create();
        $pdfFileB = PdfFile::factory()->create();
        self::get('/')
            ->assertOk()
            ->assertSeeText($pdfFileA->original_file_name)
            ->assertSeeText($pdfFileB->original_file_name);
    }

    /** @test */
    public function anyone_can_upload_a_pdf_file(): void
    {
        Storage::fake('public');
        $fileUuid = Str::uuid();
        Str::createUuidsUsing(fn() => $fileUuid);
        $pdfSize = 1000;
        $uploadedFile = UploadedFile::fake()->create('test.pdf', $pdfSize);
        self::post('/pdfs', [
            'pdf_file' => $uploadedFile,
        ])
            ->assertRedirect();

        self::assertTrue(
            Storage::disk('public')
                ->exists('/' . $fileUuid->toString() . '.pdf')
        );

        self::assertTrue(
            DB::table('pdf_files')
                ->where('unique_file_name', $fileUuid->toString())
                ->exists()
        );
    }

    /** @test */
    public function anyone_cannot_upload_a_file_other_than_pdf_file(): void
    {
        Storage::fake('public');
        $fileUuid = Str::uuid();
        Str::createUuidsUsing(fn() => $fileUuid);
        $fileSize = 1000;
        $uploadedFile = UploadedFile::fake()->create('test.jpg', $fileSize);
        self::post('/pdfs', [
            'pdf_file' => $uploadedFile,
        ])
            ->assertStatus(400);

        self::assertFalse(
            Storage::disk('public')
                ->exists('/' . $fileUuid->toString() . '.pdf')
        );

        self::assertFalse(
            DB::table('pdf_files')
                ->where('unique_file_name', $fileUuid->toString())
                ->exists()
        );
    }
}
