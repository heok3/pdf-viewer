<?php

namespace App\Http\Infrastructure\API;

use App\Http\Application\ListPdfFile;
use Illuminate\Routing\Controller;

class PdfFileController extends Controller
{
    public function index(ListPdfFile $listPdfFile): ListPdfFileView
    {
        return new ListPdfFileView($listPdfFile->execute());
    }
}
