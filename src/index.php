<?php
include_once('cli_option.php.inc');

echo "Hello World!" . PHP_EOL;

echo "Option dump" . PHP_EOL;
var_dump($options);

echo "Version" . PHP_EOL;
echo fetch_app_version($manifest) . PHP_EOL;
