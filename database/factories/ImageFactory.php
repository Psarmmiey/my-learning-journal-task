<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;

class ImageFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        //        return [
        //            'model_type' => $this->faker->word(),
        //            'model_id' => $this->faker->word(),
        //            'uuid' => $this->faker->uuid(),
        //            'collection_name' => $this->faker->name(),
        //            'name' => $this->faker->name(),
        //            'file_name' => $this->faker->name(),
        //            'mime_type' => $this->faker->word(),
        //            'disk' => $this->faker->word(),
        //            'conversions_disk' => $this->faker->word(),
        //            'size' => $this->faker->randomNumber(),
        //            'manipulations' => $this->faker->words(),
        //            'custom_properties' => $this->faker->words(),
        //            'generated_conversions' => $this->faker->words(),
        //            'responsive_images' => $this->faker->words(),
        //            'order_column' => $this->faker->randomNumber(),
        //            'owner_type' => $this->faker->word(),
        //            'owner_id' => $this->faker->word(),
        //            'created_at' => Carbon::now(),
        //            'updated_at' => Carbon::now(),
        //        ];
        return [
            'path' => UploadedFile::fake()->image('test-image.jpg'),
            // Add other necessary attributes here
        ];
    }
}
