<?php

namespace App\Http\Infrastructure\Support\WebPage;

use App\Http\Application\PdfFile\ListPdfFile;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Request $request, ListPdfFile $listPdfFile): View
    {
        return view('home')->with([
            'pdfList' => $listPdfFile->execute(),
            'selectedPdfIds' => $request->input('pdf_files') ?? [],
        ]);
    }
}
