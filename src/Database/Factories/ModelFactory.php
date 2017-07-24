<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(ZapsterStudios\Ally\Models\TeamMember::class, function (Faker\Generator $faker) {
    return [
        'user_id' => factory(App\User::class)->create()->id,
        'team_id' => factory(App\Team::class)->create()->id,
    ];
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(ZapsterStudios\Ally\Models\TeamInvitation::class, function (Faker\Generator $faker) {
    return [
        'team_id' => factory(App\Team::class)->create()->id,
        'email' => factory(App\User::class)->create()->email,
        'group' => 'member',
    ];
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(ZapsterStudios\Ally\Models\Announcement::class, function (Faker\Generator $faker) {
    return [
        'message' => $faker->sentence(),
        'visit' => '#',
    ];
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(ZapsterStudios\Ally\Models\PasswordReset::class, function (Faker\Generator $faker) {
    return [
        'email' => factory(App\User::class)->create()->email,
        'token' => str_random(60),
    ];
});
