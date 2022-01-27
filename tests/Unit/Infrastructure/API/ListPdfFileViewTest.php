<?php

namespace Tests\Unit\Infrastructure\API;

use App\Http\Infrastructure\API\ListPdfFileView;
use App\Models\PdfFile;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

final class ListPdfFileViewTest extends TestCase
{
    /** @test */
    public function it_can_return_json_response(): void
    {
        $request = self::createMock(Request::class);
        $pdf = self::createMock(PdfFile::class);
        $pdf->id = 1;
        $pdf->original_file_name = 'hello';
        $view = new ListPdfFileView(new Collection([$pdf]));
        $expected = [
            'id' => $pdf->id,
            'file_name' => $pdf->original_file_name,
        ];

        self::assertEquals([$expected], $view->toArray($request));
    }
}
