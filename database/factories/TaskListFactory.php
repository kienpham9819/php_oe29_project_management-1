<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TaskList;
use Faker\Generator as Faker;

$factory->define(TaskList::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->text,
        'project_id' => $faker->randomDigitNot(0),
        'user_id' => $faker->randomDigitNot(0),
        'due_date' => $faker->dateTime,
    ];
});
