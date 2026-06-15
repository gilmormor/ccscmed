<?php

namespace Database\Factories;

use App\Models\Admin\Permiso;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermisoFactory extends Factory
{
    protected $model = Permiso::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->word,
            'slug'   => $this->faker->word,
        ];
    }
}
