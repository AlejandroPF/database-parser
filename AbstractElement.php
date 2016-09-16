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

/**
 * Description of AbstractElement
 *
 * @author Alejandro Peña Florentín (alejandropenaflorentin@gmail.com)
 */
abstract class AbstractElement
{

    private $connection;
    private $databaseName;
    private $tableName;

    public function __construct(\mysqli $connection, $databaseName, $tableName) {
        $this->connection = $connection;
        $this->databaseName = $databaseName;
        $this->tableName = $tableName;
    }

    public function findBy($field, $comp = null) {
        
    }

    public function getFields() {
        $output = [];
        $sql = $this->connection->query("describe " . $this->databaseName . "." . $this->tableName);
        if ($sql && $sql->num_rows > 0) {
            for ($index = 0; $index < $sql->num_rows; $index++) {
                $output[$index] = $sql->fetch_row()[0];
            }
        }
        return $output;
    }
    public function save() {
        
    }

}
