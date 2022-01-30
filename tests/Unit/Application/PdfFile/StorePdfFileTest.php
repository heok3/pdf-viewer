<?php

namespace Tests\Unit\Application\PdfFile;

use App\Http\Application\PdfFile\PdfFileExtensionException;
use App\Http\Application\PdfFile\StorePdfFile;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

final class StorePdfFileTest extends TestCase
{
    /** @test */
    public function it_cannot_store_a_file_other_than_pdf_file(): void
    {
        $fileSize = 1000;
        $uploadedFile = UploadedFile::fake()->create('test.jpg', $fileSize);
        $action = new StorePdfFile();
        self::expectException(PdfFileExtensionException::class);
        $action->execute($uploadedFile);
    }
}
