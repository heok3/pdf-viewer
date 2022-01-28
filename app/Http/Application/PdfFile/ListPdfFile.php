<?php

namespace App\Http\Application\PdfFile;

use App\Http\Domain\PdfFile\PdfCollection;
use App\Models\PdfFile;

class ListPdfFile
{
    public function execute(): PdfCollection
    {
        return new PdfCollection(PdfFile::all());
    }
}
