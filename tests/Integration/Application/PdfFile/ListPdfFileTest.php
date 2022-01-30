<?php

namespace Tests\Integration\Application\PdfFile;

use App\Http\Application\PdfFile\ListPdfFile;
use App\Http\Domain\PdfFile\PdfCollection;
use App\Models\PdfFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

final class ListPdfFileTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_returns_list_of_pdf_file(): void
    {
        PdfFile::factory()->create();
        $pdfList = PdfFile::all();
        $action = new ListPdfFile();
        $list = $action->execute();
        self::assertInstanceOf(PdfCollection::class, $list);
        self::assertCount($pdfList->count(), $list);
    }
}
