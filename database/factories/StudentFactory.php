<?php

use App\Models\Student;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Student::class, function (Faker $faker) {
    $year = $faker->randomElement(['2020', '2021', '2022', '2023', '2024']);
    return [
        'student_no' => $year . str_pad($faker->unique()->randomNumber(5), 5, '0', STR_PAD_LEFT),
        'class_name' => $faker->randomElement(['软工', '计科', '大数据', '人工智能']) . $faker->randomElement(['1班', '2班', '3班']),
        'grade' => $year,
    ];
});
