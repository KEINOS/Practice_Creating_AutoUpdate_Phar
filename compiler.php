<?php

include_once('./utils/compiler.php');

echo_h1('  HelloWorld.phar Box compiler');
initialize_box();

$path_bin_box   = './utils/bin/box.phar';
$path_file_json = './compile.json';
$cmd = "php '${path_bin_box}' build -c '${path_file_json}'";

echo `$cmd`;

