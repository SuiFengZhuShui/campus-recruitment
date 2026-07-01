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
        'major' => $faker->randomElement(['计算机科学与技术', '软件工程', '电子信息工程', '通信工程', '数据科学与大数据技术', '人工智能', '网络工程', '不限']),
        'skills' => $faker->randomElement(['Java,Spring Boot,MySQL', 'Python,Django,PostgreSQL', 'Vue.js,React,TypeScript', 'Docker,K8s,CI/CD', 'Redis,RabbitMQ,ES', 'Linux,Nginx,Shell', 'Go,gRPC,Microservices', 'HTML,CSS,JavaScript', 'PHP,Laravel,MySQL', 'C++,Qt,嵌入式', 'Flutter,Dart,移动端', 'Hadoop,Spark,大数据']),
        'duty' => implode("\n", $faker->sentences(3)),
        'requirement' => implode("\n", $faker->sentences(3)),
        'welfare' => $faker->optional(0.7)->sentence(6),
        'status' => 'active',
    ];
});
