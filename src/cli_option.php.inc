<?php

$shortopts = implode('', [
    'v::',
    'h::',
    'g::',
]);

$longopts  = [
    'help',
    'version',
    'git',
];

$options  = getopt($shortopts, $longopts);
$manifest = fetch_manifest('src/manifest.json');

switch (true) {
    case (isset($options['h'])):
    case (isset($options['help'])):
        echo_br(get_text_help($manifest));
        die();
    case (isset($options['v'])):
    case (isset($options['version'])):
        echo_br(fetch_app_version($manifest));
        die();
    case (isset($options['b'])):
    case (isset($options['build'])):
        echo_br(get_git_version($manifest));
        die();
    default:
        //
}

// ---------------------------------------------------------------------

function echo_br($string)
{
    $string = (string) $string;
    echo $string . PHP_EOL;
}

function fetch_manifest($path_file_manifest)
{
    $result = array();

    if (file_exists($path_file_manifest)) {
        $manifest_json = file_get_contents($path_file_manifest);
        $result = json_decode($manifest_json, JSON_OBJECT_AS_ARRAY);
    }

    return $result;
}

function fetch_app_name($manifest)
{
    $result   = 'n/a';

    if (isset($manifest['name'])) {
        $result = $manifest['name'];
    }

    return $result;
}

function fetch_app_version($manifest)
{
    $result = 'n/a';
    $name_app      = fetch_app_name($manifest);
    $version_build = get_build_version();

    if (isset($manifest['version'])) {
        $version_app = $manifest['version'];
        $version     = "${version_app}(${version_build})";
        $result      = "${name_app} version ${version}";
    }

    return $result;
}

function get_build_version()
{
    $build_version = '@package_version@';

    if ('package_version' == trim($build_version, '@')) {
        $build_version = 'unknown';
    }

    return $build_version;
}

function get_text_help($manifest)
{
    $name  = fetch_app_name($manifest);
    $text  = fetch_app_version($manifest);
    $text .=<<<EOL

Available commands:
  help    Displays help for a command
  update  Updates ${name} to the latest version
  version Displays current version

EOL;

    return $text;
}

function is_cli()
{
    return PHP_SAPI === 'cli' || empty($_SERVER['REMOTE_ADDR']);
}
