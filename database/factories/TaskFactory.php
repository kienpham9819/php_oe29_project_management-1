<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Task;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence(config('app.display_limit'), true),
        'task_list_id' => config('app.default'),
        'is_completed' => false,
    ];
});
