<?php

namespace App\Http\Infrastructure\PdfFile\API;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ListPdfFileView extends JsonResource
{
    public function __construct(private Collection $pdfList)
    {
    }

    public function toArray($request)
    {
        return $this->build();

    }

    private function build(): array
    {
        $data = [];
        foreach($this->pdfList as $pdf) {
            $data[] = [
                'id' => $pdf->id,
                'file_name' => $pdf->original_file_name,
            ];
        }

        return $data;
    }
}
