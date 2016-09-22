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

namespace DatabaseParser;

/**
 * Description of AbstractElement
 *
 * @author Alejandro Peña Florentín (alejandropenaflorentin@gmail.com)
 */
abstract class AbstractElement
{

    private $databaseName;
    private $tableName;
    private $fields;
    private $primaryKeys;

    public function __construct(array $data, $databaseName, $tableName, $primaryKeys) {
        $this->setDatabaseName($databaseName);
        $this->setTableName($tableName);
        $this->setFields(array_keys($data));
        $this->setPrimaryKeys($primaryKeys);
        $this->fillData($data);
    }

    private function fillData($data) {
        foreach ($data as $key => $value) {
            if (null !== $value) {
                $fn = "set" . ucwords($key);
                $this->$fn($value);
            }
        }
    }

    public function getPrimaryKeys() {
        return $this->primaryKeys;
    }

    public function setPrimaryKeys($primaryKeys) {
        $this->primaryKeys = $primaryKeys;
        return $this;
    }

    public function getDatabaseName() {
        return $this->databaseName;
    }

    public function getTableName() {
        return $this->tableName;
    }

    public function getFields() {
        return $this->fields;
    }

    public function setFields(array $value) {
        $this->fields = $value;
        return $this;
    }

    public function setDatabaseName($databaseName) {
        $this->databaseName = $databaseName;
        return $this;
    }

    public function setTableName($tableName) {
        $this->tableName = $tableName;
        return $this;
    }
    public function get($fieldName){
        if(method_exists($this, "get".  ucwords($fieldName))){
            $fn = "get".ucwords($fieldName);
            return $this->$fn();
        } else {
            return false;
        }
        
    }
}
