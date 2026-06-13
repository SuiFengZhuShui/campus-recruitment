<?php

use App\Models\Job;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Job::class, function (Faker $faker) {
    $min = $faker->numberBetween(3000, 15000);
    return [
        'title' => $faker->randomElement(['前端开发工程师', '后端开发工程师', '测试工程师', '产品经理', 'UI设计师', '数据分析师', '运维工程师', '算法工程师']),
        'count' => $faker->numberBetween(1, 10),
        'city' => $faker->randomElement(['北京', '上海', '广州', '深圳', '杭州', '成都', '武汉', '南京']),
        'salary_min' => $min,
        'salary_max' => $min + $faker->numberBetween(3000, 20000),
        'education' => $faker->randomElement(['大专', '本科', '硕士', '不限']),
        'type' => $faker->randomElement(['full-time', 'internship']),
        'duty' => implode("\n", $faker->sentences(3)),
        'requirement' => implode("\n", $faker->sentences(3)),
        'welfare' => $faker->optional(0.7)->sentence(6),
        'status' => 'active',
    ];
});
