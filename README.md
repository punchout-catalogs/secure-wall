Require:
"illuminate/database": "5.7.*",

Table to create:
CREATE TABLE `secured_wall` (
  `id` varchar(255) NOT NULL COMMENT 'id',
  `value` text NOT NULL COMMENT 'value',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'created_at',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'updated_at',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Punchout Vault Table';

PHP Example:
require_once __DIR__ . '/../vendor/autoload.php';

$client = \PunchoutCatalog\SecuredWall\Client::getInstance(
'abrakadabra',
[
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'name_db',
    'username' => 'usr_test',
    'password' => 'pwd_test',
]);

var_dump($client->exists('test-key-1'));
var_dump($client->exists('test-key-2'));
var_dump($client->exists('test-key-3'));
var_dump($client->exists('test-key-4'));

$client->set('test-key-1', 'test-value-1');
$client->set('test-key-2', ['test-value-2' => 'test-value-2-array', 'many' => ['element1', 'element2', 'el3' => ['el31', 'el32']]]);
$client->set('test-key-3', 'test-value-3');

var_dump($client->exists('test-key-1'));
var_dump($client->exists('test-key-2'));
var_dump($client->exists('test-key-3'));
var_dump($client->exists('test-key-4'));

var_dump($client->get('test-key-1'));
var_dump($client->get('test-key-2'));
var_dump($client->get('test-key-3'));

try {
    var_dump($client->get('test-key-4'));
} catch (\PunchoutCatalog\SecuredWall\Exception $e) {
    var_dump($e->getMessage());
}
