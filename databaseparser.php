<?php

require_once 'config.php';
echo "\r\n";
if (isset($argv) && isset($argc)) {
    $config = new DatabaseParser\Builder\BuilderConfig;
    $connectionConfig = [
        "host" => "localhost",
        "user" => "",
        "password" => "",
        "port" => 3306
    ];
    $database = "";
    for ($index = 0; $index < $argc; $index++) {
        $arg = $argv[$index];
        switch (strtolower($arg)) {
            case "--help":
            case "-?":
            case "?":
                echo "Options:\r\n";
                echo "--dest {value}\r\n-d {value} \tOutput directory\r\n";
                echo "\r\n";
                echo "--namespace {value}\r\n-ns {value} \tNamespace for the generated classes\r\n";
                echo "\r\n";
                echo "--host {value}\r\n-h {value} \tMySQL host name\r\n";
                echo "\r\n";
                echo "--user {value}\r\n-u {value} \tMySQL user name\r\n";
                echo "\r\n";
                echo "--password {value}\r\n-p {value} \tMySQL password\r\n";
                echo "\r\n";
                echo "--port {value}\tMySQL port\r\n";
                echo "\r\n";
                echo "--database {value}\r\n-db {value} \tMySQL database name to generate classes\r\n";
                die();
                break;
            case "--dest":
            //nobreak
            case "-d":
                $index++;
                if (isset($argv[$index])) {
                    $config->dest = $argv[$index];
                } else {
                    echo "Error: Argument $arg requires a value";
                    die();
                }
                break;
            case "--namespace":
            //nobreak
            case "-ns":
                $index++;
                if (isset($argv[$index])) {
                    $config->namespace = $argv[$index];
                } else {
                    echo "Error: Argument $arg requires a value";
                    die();
                }
                break;
            case "--host":
            case "-h":
                $index++;
                if (isset($argv[$index])) {
                    $connectionConfig["host"] = $argv[$index];
                } else {
                    echo "Error: Argument $arg requires a value";
                    die();
                }
                break;
            case "--user":
            case "-u":
                $index++;
                if (isset($argv[$index])) {
                    $connectionConfig["user"] = $argv[$index];
                } else {
                    echo "Error: Argument $arg requires a value";
                    die();
                }
                break;
            case "--password":
            case "-p":
                $index++;
                if (isset($argv[$index])) {
                    $connectionConfig["password"] = $argv[$index];
                } else {
                    echo "Error: Argument $arg requires a value";
                    die();
                }
                break;
            case "--port":
                $index++;
                if (isset($argv[$index])) {
                    $connectionConfig["port"] = $argv[$index];
                } else {
                    echo "Error: Argument $arg requires a value";
                    die();
                }
                break;
            case "--database":
            case "-db":
                $index++;
                if (isset($argv[$index])) {
                    $database = $argv[$index];
                } else {
                    echo "Error: Argument $arg requires a comma-delimited value";
                    die();
                }
        }
    }
    $connection = @new mysqli($connectionConfig["host"],$connectionConfig["user"],$connectionConfig["password"]);
    $builder = new DatabaseParser\Builder\Builder($connection, $config);
    $builder->execute($database);
} else {
    echo "Error: DatabaseParser can only be run via command line interface";
}