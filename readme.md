# Laravel TeamPay
Under construction...

---

## Notes
Clone
```
git clone git@github.com:ZapsterStudios/TeamPay.git
```

Required
```json
"require": {
    ...
    "zapsterstudios/teampay": "*@dev"
}
```

Repositories
```json
"repositories": [
    {
        "type": "path",
        "url": "./TeamPay"
    }
]
```

Providers
```php
'providers' => [
    ...
    Laravel\Cashier\CashierServiceProvider::class,
    Laravel\Passport\PassportServiceProvider::class,
    ZapsterStudios\TeamPay\TeamPayServiceProvider::class,
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

Tests
```
Braintree: valid-first-plan | Valid First Plan | $5 | 1 Month
Braintree: valid-second-plan | Valid Second Plan | $10 | 1 Month
```

```
vendor/bin/phpunit TeamPay
```