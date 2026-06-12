<?php

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Faker $faker) {
    return [
        'role' => 'student',
        'name' => $faker->name,
        'phone' => $faker->unique()->phoneNumber,
        'password' => Hash::make('password'),
        'status' => 'active',
    ];
});
