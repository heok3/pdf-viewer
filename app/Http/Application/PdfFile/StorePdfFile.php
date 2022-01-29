<?php

namespace App\Http\Application\PdfFile;

use App\Http\Domain\PdfFile\PdfFileExtensionException;
use App\Models\PdfFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorePdfFile
{
    /**
     * @param UploadedFile $uploadedFile
     *
     * @return PdfFile
     *
     * @throws PdfFileExtensionException
     */
    public function execute(UploadedFile $uploadedFile): PdfFile
    {
        if ($uploadedFile->getClientOriginalExtension() !== 'pdf') {
            throw new PdfFileExtensionException('It must be pdf file');
        }

        $pdfFile = new PdfFile([
            'original_file_name' => pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME),
            'unique_file_name' => Str::uuid()->toString(),
        ]);

        Storage::disk('public')
            ->putFileAs(
                '/',
                $uploadedFile,
                $pdfFile->unique_file_name. '.pdf'
            );

        $pdfFile->save();

        return $pdfFile;
    }
}
