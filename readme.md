# Frontegg PHP SDK

![alt text](https://fronteggstuff.blob.core.windows.net/frongegg-logos/logo-transparent.png)

Frontegg is a web platform where SaaS companies can set up their fully managed, scalable and brand aware - SaaS features and integrate them into their SaaS portals in up to 5 lines of code.


## Installation

Use the package manager [Composer](https://getcomposer.org/) to install Frontegg SDK for PHP.

```bash
composer install frontegg/php-sdk
```

## Usage

Frontegg offers multiple components for integration with the Frontegg's scalable back-end and front-end libraries.

### Simple Example

````php
<?php

require_once './vendor/autoload.php';
require_once './src/Frontegg/autoload.php';

use Frontegg\Frontegg;

$clientId = '6da27373-1572-444f-b3c5-ef702ce65123';
$apikey = '0cf38799-1dae-488f-8dc8-a09b4c397ad5';
$config = [
    'clientId' => $clientId,
    'clientSecret' => $apikey,
    'apiBaseUrl' => 'https://api.frontegg.com/',
];

$frontegg = new Frontegg($config);
$frontegg->init();
````


!!!!!TODO:!!!!!