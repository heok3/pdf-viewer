<?php

namespace App\Http\Infrastructure\PdfFile\API;

use App\Http\Application\PdfFile\ListPdfFile;
use Illuminate\Routing\Controller;

class PdfFileController extends Controller
{
    public function index(ListPdfFile $listPdfFile): ListPdfFileView
    {
        return new ListPdfFileView($listPdfFile->execute());
    }
}
