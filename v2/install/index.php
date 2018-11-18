<?php

require_once('core.php');
$step = $_GET['step'] ?? 'language';

ob_start();
switch ($step) {
    case 'language' :
        require('steps/language.php');
        break;

    case 'database' :
        if (isset($_POST['submit'])) {
            $database_host = $_POST['database_host'];
            $database_name = $_POST['database_name'];
            $database_username = $_POST['database_username'];
            $database_password = $_POST['database_password'];
            $table_prefix = $_POST['table_prefix'];

            @$_SESSION['database_host'] = $database_host;
            @$_SESSION['database_username'] = $database_username;
            @$_SESSION['database_password'] = $database_password;
            @$_SESSION['database_name'] = $database_name;
            @$_SESSION['table_prefix'] = $table_prefix;

            @$db = mysqli_connect($database_host, $database_username, $database_password, $database_name);
            if (!$db) {
                $alert_message = _lang("error_check_db_connection");
            } else {
                header("Location: ?step=settings");
            }
        }
        require('steps/database.php');
        break;

    case 'settings' :
        if (isset($_POST['submit'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            @$_SESSION['username'] = $username;
            @$_SESSION['password'] = $password;
            header("Location: ?step=done");
        }
        require('steps/settings.php');
        break;

    case 'done' :
        $database_host = $_SESSION['database_host'];
        $database_username = $_SESSION['database_username'];
        $database_password = $_SESSION['database_password'];
        $database_name = $_SESSION['database_name'];
        $table_prefix = $_SESSION['table_prefix'];

        $username = $_SESSION['username'];
        $password = hash('sha256', $_SESSION['password']);

        $htp = (isset($_SERVER['HTTPS'])) ? 'https' : 'http';
        $site_url = "{$htp}://{$_SERVER['SERVER_NAME']}";
        $fullpath = "{$htp}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $projectsecurity_path = substr($fullpath, 0, strpos($fullpath, '/install'));

        @$db = new mysqli($database_host, $database_username, $database_password, $database_name);
        if ($db) {

            //Importing SQL Tables
            $query = '';
            $sql_dump = file( __DIR__ . "/sql/database.sql");
            $sql_dump = str_replace("<DB_PREFIX>", $table_prefix, $sql_dump);
            $sql_dump = str_replace("<PROJECTSECURITY_PATH>", $projectsecurity_path, $sql_dump);

            foreach ($sql_dump as $line) {
                $startWith = substr(trim($line), 0, 2);
                $endWith = substr(trim($line), -1, 1);

                if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
                    continue;
                }

                $query .= $line;
                if ($endWith == ';') {
                    mysqli_query($db, $query) or die('Problem in executing the SQL query <b>' . $query . '</b>');
                    $query = '';
                }
            }

            // Config file creating and writing information
            $config_file = file_get_contents(CONFIG_FILE_TEMPLATE);
            $config_file = str_replace("<DB_HOST>", $database_host, $config_file);
            $config_file = str_replace("<DB_NAME>", $database_name, $config_file);
            $config_file = str_replace("<DB_USER>", $database_username, $config_file);
            $config_file = str_replace("<DB_PASSWORD>", $database_password, $config_file);
            $config_file = str_replace("<DB_PREFIX>", $table_prefix, $config_file);
            $config_file = str_replace("<PROJECTSECURITY_PATH>", $projectsecurity_path, $config_file);
            $config_file = str_replace("<SITE_URL>", $site_url, $config_file);

            $link = new mysqli($database_host, $database_username, $database_password, $database_name);
            $table = $table_prefix . 'users';
            $query = mysqli_query($link, "INSERT INTO `{$table}` (id, username, password) VALUES ('1', '{$username}', '{$password}')");

            @chmod(CONFIG_FILE_PATH, 0777);
            @$f = fopen(CONFIG_FILE_PATH, "w+");
            if (!fwrite($f, $config_file) > 0) {
                echo 'Cannot open the configuration file to save the information';
            }
            fclose($f);

        } else {
            echo _lang("error_check_db_connection");
        }
        require('steps/done.php');
        break;
    default :
        require('steps/language.php');
        break;
}
$content = ob_get_clean();
require('layout.php');
