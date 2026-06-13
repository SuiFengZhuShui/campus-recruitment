<?php

use App\Models\Enterprise;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Enterprise::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'credit_code' => '91110000' . $faker->unique()->randomNumber(8),
        'industry' => $faker->randomElement(['IT', '金融', '教育', '制造', '医疗', '房地产']),
        'scale' => $faker->randomElement(['1-50人', '50-200人', '200-500人', '500-1000人', '1000人以上']),
        'intro' => $faker->sentence(10),
        'contact_name' => $faker->name,
        'contact_phone' => $faker->numerify('139########'),
        'email' => 'ent' . $faker->unique()->randomNumber(5) . '@test.com',
        'status' => 'pending',
    ];
});

$factory->state(Enterprise::class, 'approved', function (Faker $faker) {
    return [
        'status' => 'approved',
    ];
});

$factory->state(Enterprise::class, 'rejected', function (Faker $faker) {
    return [
        'status' => 'rejected',
        'audit_remark' => $faker->sentence(3),
    ];
});
