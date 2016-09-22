<?php

/*
 * Copyright (C) 2016 Alejandro Peña Florentín (alejandropenaflorentin@gmail.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace DatabaseParser\Builder;

/**
 * Description of Builder
 *
 * @author Alejandro Peña Florentín (alejandropenaflorentin@gmail.com)
 */
class Builder
{

    /**
     * @var \mysqli Connection
     */
    private $connection;
    private $config;

    /**
     * @var \DatabaseParser\Logger Logger
     */
    public static $logger;

    public function __construct($connection, \DatabaseParser\Builder\BuilderConfig $config) {
        self::$logger = new \DatabaseParser\Logger;
        $this->connection = $connection;
        self::$logger->write("Connecting mysql...");
        if (@$this->connection->ping()) {
            self::$logger->writeln("OK");
        } else {
            self::$logger->writeln("FAIL");
            throw Exception\BuilderException::create(Exception\BuilderException::CONNECTION_FAILURE);
        }
        $this->config = $config;
        $this->setDest($config->dest);
    }

    public function getDest() {
        return $this->dest;
    }

    public function setDest($dest) {
        if (is_dir($dest)) {
            self::$logger->writeln("Set '$dest' as output path.");
            $this->dest = $dest;
        } else {
            if (@mkdir($dest, 0777, true)) {
                self::$logger->writeln("Created '$dest' as output path.");
                $this->dest = $dest;
            } else {
                throw Exception\BuilderException::create(Exception\BuilderException::CANT_CREATE_DIRECTORY, $dest);
            }
        }
        return $this;
    }

    private function executeQueryFirstField($query) {
        $output = [];
        $sql = $this->connection->query($query);
        if ($sql && $sql->num_rows > 0) {
            for ($index = 0; $index < $sql->num_rows; $index++) {
                $output[$index] = $sql->fetch_row()[0];
            }
        }
        return $output;
    }

    private function getDatabases() {
        return $this->executeQueryFirstField("show databases");
    }

    private function getTables($database) {
        return $this->executeQueryFirstField("show tables from " . $database);
    }

    private function getFields($database, $table) {
        return $this->executeQueryFirstField("describe " . $database . "." . $table);
    }

    private function getPrimaryKeys($database, $table) {
        $output = [];
        $sql = $this->connection->query("describe " . $database . "." . $table);
        $index = 0;
        while ($index < $sql->num_rows) {
            $row = $sql->fetch_assoc();
            if ($row["Key"] == "PRI") {
                array_push($output, $row['Field']);
            }
            $index++;
        }
        return $output;
    }

    public function execute($database) {
        self::$logger->write("Fetching databases...");
        $availableDatabases = $this->getDatabases();
        self::$logger->writeln("OK");
        if (in_array($database, $availableDatabases)) {
            self::$logger->write("Fetching tables from database '$database'...");
            $tables = $this->getTables($database);
            self::$logger->writeln("OK");
            // Crea tantas clases como tablas
            for ($index = 0; $index < count($tables); $index++) {
                self::$logger->write("Fetching fields from table '".$database.".".$tables[$index]."'...");
                $fields = $this->getFields($database, $tables[$index]);
                self::$logger->writeln("OK");
                
                $classBuilder = new ClassBuilder($database, $tables[$index], $fields, $this->config);
                $pk = $this->getPrimaryKeys($database, $tables[$index]);
                $classBuilder->setPrimaryKeys($pk);
                $fileName = $classBuilder->getClassName() . ".php";
                file_put_contents($this->dest . $fileName, $classBuilder->build());
            }
        } else {
            throw Exception\BuilderException::create(Exception\BuilderException::DATABASE_NOT_FOUND, $database);
        }
        self::$logger->writeln("All work done!");
        self::$logger->writeln("");
    }

}
