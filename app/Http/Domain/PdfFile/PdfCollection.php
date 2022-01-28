<?php

namespace App\Http\Domain\PdfFile;

use App\Models\PdfFile;
use Illuminate\Support\Collection;

class PdfCollection extends Collection
{
    /**
     * @param int $id
     *
     * @return PdfFile
     *
     * @throws PdfFileNotFoundException
     */
    public function find(int $id): PdfFile
    {
        $pdfFile = $this->first(fn(PdfFile $file) => $file->id === $id);
        if (is_null($pdfFile)) {
            throw new PdfFileNotFoundException('Pdf file does not exist(id: ' . $id);
        }

        return $pdfFile;
    }
}
