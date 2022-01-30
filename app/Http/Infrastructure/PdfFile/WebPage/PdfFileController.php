<?php

namespace App\Http\Infrastructure\PdfFile\WebPage;

use App\Http\Application\PdfFile\ListPdfFile;
use App\Http\Application\PdfFile\PdfFileExtensionException;
use App\Http\Application\PdfFile\StorePdfFile;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use function redirect;
use function view;

class PdfFileController extends Controller
{
    /**
     * @param Request $request
     * @param ListPdfFile $listPdfFile
     *
     * @return View
     */
    public function index(Request $request, ListPdfFile $listPdfFile): View
    {
        return view('home')->with([
            'pdfList' => $listPdfFile->execute(),
            'selectedPdfIds' => $request->input('pdf_files') ?? [],
        ]);
    }

    /**
     * @param StorePdfFile $storePdfFile
     * @param Request $request
     *
     * @return Application|ResponseFactory|RedirectResponse|Response|Redirector
     */
    public function store(StorePdfFile $storePdfFile, Request $request): Response|Redirector|Application|RedirectResponse|ResponseFactory
    {
        try {
            if ($file = $request->file('pdf_file')) {
                $pdfFile = $storePdfFile->execute($file);

                return redirect(route('home', ['pdf_files' => [$pdfFile->id]]));
            }

            return redirect(route('home'));
        } catch (PdfFileExtensionException) {
            return response('', 400);
        }
    }
}
