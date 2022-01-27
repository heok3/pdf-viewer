<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PdfFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'original_file_name' => $this->faker->word() . '.pdf',
            'unique_file_name' => Str::uuid()->toString(),
        ];
    }
}
