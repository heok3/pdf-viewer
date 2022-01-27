<?php

namespace Tests\Feature\Infrastructure\API;

use App\Models\PdfFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

final class PdfFileControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_can_see_the_list_of_pdf_file(): void
    {
        $pdfFileA = PdfFile::factory()->create();
        $pdfFileB = PdfFile::factory()->create();
        $response = $this->get('/api/pdfs');
        $response->assertStatus(200)->assertJson([
            'data' => [
                [
                    'id' => $pdfFileA->id,
                    'file_name' => $pdfFileA->original_file_name,
                ],
                [
                    'id' => $pdfFileB->id,
                    'file_name' => $pdfFileB->original_file_name,
                ],
            ]
        ]);
    }
}
