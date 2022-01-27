<?php

namespace App\Http\Infrastructure\WebPage;

use App\Http\Application\ListPdfFile;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(ListPdfFile $listPdfFile): View
    {
        return view('home')->with(['pdfList' => $listPdfFile->execute()]);
    }
}
