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
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'country' => $faker->countryCode,
    ];
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->state(App\User::class, 'verified', function (Faker\Generator $faker) {
    return [
        'email_verified' => 1,
        'email_token' => null,
    ];
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->state(App\User::class, 'suspended', function (Faker\Generator $faker) {
    return [
        'suspended_at' => '2017-01-01 00:00:00',
        'suspended_reason' => 'Factory',
    ];
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Team::class, function (Faker\Generator $faker) {
    $company = $faker->company;

    return [
        'user_id' => factory(App\User::class)->create()->id,
        'name' => $company,
        'slug' => App\Team::generateSlug(str_slug($company)),
    ];
});

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->state(App\Team::class, 'suspended', function (Faker\Generator $faker) {
    return [
        'suspended_at' => '2017-01-01 00:00:00',
        'suspended_reason' => 'Factory',
    ];
});
