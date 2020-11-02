<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class FileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $file = UploadedFile::fake()->create('photo', 150, 'image/png');
        return [
            'type' => $file->getMimeType(),
            'name' => $file->getClientOriginalName(),
            'url' => $file->store('files'),
            'size' => $file->getSize(),
        ];
    }
}
