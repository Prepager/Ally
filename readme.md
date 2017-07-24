# Laravel Ally
Under construction...

---

## Notes
Clone
```
git clone git@github.com:ZapsterStudios/Ally.git
```

Required
```json
"require": {
    ...
    "zapsterstudios/ally": "*@dev"
}
```

Repositories
```json
"repositories": [
    {
        "type": "path",
        "url": "./Ally"
    }
]
```

Providers
```php
'providers' => [
    ...
    Laravel\Cashier\CashierServiceProvider::class,
    Laravel\Passport\PassportServiceProvider::class,
    ZapsterStudios\Ally\AllyServiceProvider::class,
]
```

Migrate
```
php artisan migrate
```

Passport
```
php artisan passport:install
```

## Tests

Normal Tests
```
vendor/bin/phpunit Ally --exclude-group Subscription
```

Subscription Tests
```
Braintree plans:
valid-first-plan | Valid First Plan | $5 | 1 Month
valid-second-plan | Valid Second Plan | $10 | 1 Month
```

```
vendor/bin/phpunit Ally --group Subscription
```