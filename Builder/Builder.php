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

namespace Builder;

/**
 * Description of Builder
 *
 * @author Alejandro Peña Florentín (alejandropenaflorentin@gmail.com)
 */
class Builder
{

    private $connection;
    private $dest;

    public function __construct($connection, $dest = '') {
        $this->connection = $connection;
        $this->setDest($dest);
    }

    public function getDest() {
        return $this->dest;
    }

    public function setDest($dest) {
        $this->dest = $dest;
        return $this;
    }
    private function executeQueryFirstField($query){
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
        return $this->executeQueryFirstField("show tables from ".$database);
    }

    private function getFields($database, $table) {
        return $this->executeQueryFirstField("describe ".$database.".".$table);
    }

    public function execute($database) {
        $availableDatabases = $this->getDatabases();
        if (in_array($database, $availableDatabases)) {
            $tables = $this->getTables($database);
            // Crea tantas clases como tablas
            for ($index = 0; $index < count($tables); $index++) {
                $fields = $this->getFields($database, $tables[$index]);
                
            }
        } else {
            throw \Builder\Exception\BuilderException::create(Exception\BuilderException::DATABASE_NOT_FOUND, $database);
        }
    }

}
