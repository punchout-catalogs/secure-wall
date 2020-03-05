Composer dependencies:
```
    "require": {
        "php": ">=7.1",
        "ext-mcrypt": "*",
        "illuminate/encryption": "5.7.*",
        "guzzlehttp/guzzle": "^6.5"
    },
```


PHP Example:
```
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$key = 'secret-key-to-decode-returned-result';
$token = '32symbolshere';
$url = 'http://punchout-cloud-url.com';

$client = \PunchoutCatalog\SecuredWall\Client::getInstance($key, $token, $url);


//Save data to the Secured Wall
$save = $client->set([
    'test-key1' => 'v1',
    'test-key2' => 'v2',
    'test-key3' => 'v3',
]);

//Get data from the Secured Wall
$res = $client->get(['test-key1', 'test-key2', 'test-key3']);
