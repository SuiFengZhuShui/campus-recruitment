<?php

use App\Models\Application;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Application::class, function (Faker $faker) {
    return [
        'status' => $faker->randomElement(['pending', 'pending', 'reviewed', 'interviewed', 'accepted', 'rejected']),
    ];
});
