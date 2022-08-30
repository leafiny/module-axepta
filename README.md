# Axepta_Payment

The module integrates the payment with BNP Paribas Axepta.

# Installation

Copy `Axepta_Payment` into your Leafiny `modules` directory.

Deploy the resources if the website root is `pub`:

```
php deploy.php
```

# Dependency

**Axepta_Payment** need the native **Leafiny_Payment** module.

# Configuration

In global config file (ex: `etc/config.dev.php`), add the Axepta credentials in **model** configuration:

```php
$config = [
    /* ... */
    'model' => [
        /* ... */
        Axepta_Model_Payment_Online_Axepta::PAYMENT_METHOD => [
            'merchant_id'  => '',
            'hmac_key'     => '',
            'blowfish_key' => '',
            'order_desc'   => '',
        ],
    ],
    /* ... */
];
```

The **order_desc** param is optional (default "Cart Payment"). The content will be displayed on the payment page. Set "**Test:0000**" for a simulation in production (test with a real card without capture).
