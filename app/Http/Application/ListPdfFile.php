<?php

namespace App\Http\Application;

use App\Models\PdfFile;
use Illuminate\Support\Collection;

class ListPdfFile
{
    public function execute(): Collection
    {
        return PdfFile::all();
    }
}
