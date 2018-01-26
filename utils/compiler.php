<?php

const DIR_SEP          = DIRECTORY_SEPARATOR;
const DIR_CURRENT      = '.' . DIR_SEP;
const DIR_PARRENT      = '..' . DIR_SEP;
const NAME_DIR_BIN     = 'bin';
const NAME_DIR_SRC     = 'src';
const NAME_DIR_UTILS   = 'utils';
const NAME_BIN_BOX     = 'box.phar';
const PATH_DIR_SRC     = DIR_CURRENT    . NAME_DIR_SRC   . DIR_SEP;
const PATH_DIR_UTILS   = DIR_CURRENT    . NAME_DIR_UTILS . DIR_SEP;
const PATH_DIR_BIN     = PATH_DIR_UTILS . NAME_DIR_BIN   . DIR_SEP;
const URL_BIN_BOX      = 'https://box-project.github.io/box2/installer.php';

function check_file_exist($path_file)
{
    echo_head("Checking file existance (${path_file}) ...");
    $result = file_exists($path_file);

    return ($result)
        ? echo_tail('... OK.')
        : echo_tail('... Error.[File not found]');
}

function create_dir_bin()
{
    if (! dir_exists(PATH_DIR_BIN)) {
        echo_head('Creating bin directory ...');
        if (touch_dir(PATH_DIR_BIN)) {
            touch(PATH_DIR_BIN . 'index.html');
            echo_tail('... Done.');
        } else {
            echo_tail('... Error.');
            return false;
        }
    }

    return true;
}

function dir_exists($path_dir)
{
    return is_dir((string) $path_dir);
}

function download_url_to_bin_dir($url)
{
    create_dir_bin();

    $name_bin_box_installer = fetch_filename_url(URL_BIN_BOX);
    $path_bin_box_installer = PATH_DIR_BIN . $name_bin_box_installer;

    echo 'Downloading box.phar installer ...';
    $contents = file_get_contents($url);
    if (file_put_contents($path_bin_box_installer, $contents, LOCK_EX)) {
        echo '... Done.' . PHP_EOL;
    } else {
        echo '... Error.' . PHP_EOL;
    }
}

function echo_br($string)
{
    echo $string . PHP_EOL;
}

function echo_head($string)
{
    // here record string width
    echo $string;
}

function echo_hr($return = false)
{
    $hr = '==========================================';
    if ($return) {
        return $hr;
    } else {
        echo PHP_EOL . $hr . PHP_EOL;
    }
}

function echo_tail($string)
{
    // here fills dots then string
    echo $string . PHP_EOL;
}

function echo_title()
{
    echo_hr();
    echo ' Phar archiver';
    echo_hr();
}

function fetch_filename_url($url)
{
    $dirs  = explode('/', parse_url($url)['path']);
    $count = ((integer) count($dirs)) -1;

    return $dirs[$count];
}

function initialize_box()
{
    if (is_phar_readonly()) {
        die('Phar is read only. Please change php.ini settings.');
    }

    $path_bin_box = PATH_DIR_BIN . NAME_BIN_BOX;

    $file_exists = check_file_exist($path_bin_box);

    if ($file_exists) {
        update_box($path_bin_box);
        return true;
    }

    echo_br('[Installing box]');
    echo_hr();

    $name_file_installer = fetch_filename_url(URL_BIN_BOX);
    $path_file_installer = PATH_DIR_BIN . $name_file_installer;

    $file_exists = check_file_exist($path_file_installer);
    if (! $file_exists) {
        download_url_to_bin_dir(URL_BIN_BOX, $path_file_installer);

        $path_file_installed = install_bin_box($path_file_installer);

        move_file($path_file_installed, $path_bin_box);
        set_mod_file_executable($path_bin_box);

        unlink_file($path_file_installer);
    }

    return file_exists($path_bin_box);
}

function install_bin_box($path_bin_box_installer)
{
    echo_head('Checkgin installer ...');

    if (file_exists($path_bin_box_installer)) {
        echo_tail('... Done.[Now Running Installer]');

        // Include and execute!
        // Noe: It installs to current dir.
        include_once($path_bin_box_installer);

        if (file_exists(DIR_CURRENT . NAME_BIN_BOX)) {
            return DIR_CURRENT . NAME_BIN_BOX;
        } else {
            return false;
        }
    } else {
        echo_tail('... Error.[File not found]');
    }
}

function is_phar_readonly()
{
    return (ini_get('phar.readonly') || ini_get('phar.require_hash'));
}

function move_file($path_file_from, $path_file_to)
{

    echo_head("Moving file to ${path_file_to} ...");

    if (file_exists($path_file_from)) {
        if (rename($path_file_from, $path_file_to)) {
            echo_tail('... Done.');
        } else {
            echo_tail('... Error.[Can not move box.phar]');
        }
    } else {
        echo_tail('... Error.[box.phar not found]');
    }
}

function set_mod_file_executable($path_file)
{
    echo_head('Changing mod executable ...');
    $result = chmod($path_file, 0755);
    echo_tail(($result) ? '...Done.' : '...Error.');
    return $result;
}

function set_files_under($files, $path_dir)
{
    foreach ($files as $key => $name_file) {
        $path_file_tmp = $path_dir . $name_file;
        $files[$key] = $path_file_tmp;
    }

    return $files;
}

function touch_dir($path_dir)
{
    $mod = 0777;
    if (@mkdir($path_dir, $mod, true) && chmod($path_dir, $mod)) {
        return realpath($path_dir);
    } else {
        die("Error making dir at: ${path_dir_bin}");
    }
}

function unlink_file($path_file)
{
    echo_head("Unlinking file (${path_file}) ...");

    if (file_exists($path_file)) {
        if (unlink($path_file)) {
            echo_tail('... Done.');
        } else {
            echo_tail('... Error.[Can not unlink file]');
        }
    } else {
        echo_tail('... Error.[File not found]');
    }
}

function update_box($path_bin_box)
{
    if (! file_exists($path_bin_box)) {
        return false;
    }

    $cmd_update = "php ${path_bin_box} update";
    echo('\'box.phar\' found. ');
    echo_br(`$cmd_update`);
}
