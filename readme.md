<h1 align="center">Laravel Ally</h1>
<p align="center">
    <a href="https://travis-ci.org/ZapsterStudios/Ally">
        <img src="https://travis-ci.org/ZapsterStudios/Ally.svg?branch=master" alt="Travis Build Status">
    </a>
    <a href="https://coveralls.io/github/ZapsterStudios/Ally?branch=master">
        <img src="https://coveralls.io/repos/github/ZapsterStudios/Ally/badge.svg?branch=master" alt="Coverage Status">
    </a>
    <a href="https://styleci.io/repos/93747168">
        <img src="https://styleci.io/repos/93747168/shield?style=flat" alt="StyleCI Status">
    </a>
    <a href="https://gemnasium.com/github.com/ZapsterStudios/Ally">
        <img src="https://gemnasium.com/badges/github.com/ZapsterStudios/Ally.svg" alt="Dependency Status">
    </a>
</p>

## Introduction
Laravel Ally is a fully API based team subscription service currently supporting the [Braintree](https://www.braintreepayments.com) payment system.
The project is heavily inspired by [Laravel Spark](https://spark.laravel.com/) and take advantage of [Laravel Passport](https://github.com/laravel/passport) and [Laravel Cashier](https://github.com/laravel/cashier-braintree).

The project is in a somewhat feature complete state however updating, versioning and various other aspect of the project will most like be changed.

## Documentation
Coming soon.

## Ecosystem
[JavaScript SDK](https://github.com/ZapsterStudios/Ally-JS-SDK) - Consume the REST API from JavaScript.

[VueJS Project](https://github.com/ZapsterStudios/Ally-VueJS) - Interact with the REST API from Vue components.

## Installation
Installation and updating are currently just based on git clone/pull (will be change).

### Clone the Repository
```bash
$ git clone git@github.com:ZapsterStudios/Ally.git
```

### Require and Register the Repository in ``composer.json``
```json
"require": {
    ...
    "zapsterstudios/ally": "*@dev"
},
"repositories": [
    {
        "type": "path",
        "url": "./Ally"
    }
]
```

### Run Installation
```bash
$ php artisan ally:install
```

## Testing
Some tests require a Braintree Sandbox account and are grouped under 'Subscription'.

### Non-Subscription Tests
#### Command
```bash
$ vendor/bin/phpunit Ally --exclude-group Subscription
```

### Subscription Tests
#### Braintree Plans
```
valid-first-plan  | Valid First Plan  | $5
valid-second-plan | Valid Second Plan | $10
```

#### Command
```bash
$ vendor/bin/phpunit Ally --group Subscription
```

## License
Laravel Ally is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)