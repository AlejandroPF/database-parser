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
 * Description of ClassBuilder
 *
 * @author Alejandro Peña Florentín (alejandropenaflorentin@gmail.com)
 */
class ClassBuilder
{

    private $database;
    private $table;
    private $fields = [];
    private $primaryKeys = [];

    /**
     * @var \DatabaseParser\Builder\BuilderConfig Config
     */
    private $config;

    public function __construct($database, $table, $fields, \DatabaseParser\Builder\BuilderConfig $config) {
        Builder::$logger->write("Building class for '".$database.".".$table."...");
        $this->setDatabase($database);
        $this->setTable($table);
        $this->setFields($fields);
        $this->config = $config;
    }

    public function getDatabase() {
        return $this->database;
    }

    public function getTable() {
        return $this->table;
    }

    public function setDatabase($database) {
        $this->database = $database;
        return $this;
    }

    public function setTable($table) {
        $this->table = $table;
        return $this;
    }

    public function getFields() {
        return $this->fields;
    }

    public function getField($index) {
        if (isset($this->fields[$index])) {
            return $this->fields[$index];
        } else {
            return null;
        }
    }

    public function setFields($fields) {
        $this->fields = $fields;
        return $this;
    }

    public function getPrimaryKeys() {
        return $this->primaryKeys;
    }

    public function setPrimaryKeys($primaryKeys) {
        $this->primaryKeys = $primaryKeys;
        return $this;
    }

    public function getPrimaryKeysAsPhpCode() {
        return "[\"" . implode("\",\"", $this->getPrimaryKeys()) . "\"]";
    }

    public function getClassName() {
        return ucwords($this->table);
    }

    private function createLicenseHeader() {
        return "/*
 * Copyright (C) " . date("Y") . " Alejandro Peña Florentín (alejandropenaflorentin@gmail.com)
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
 */\r\n";
    }

    private function createHeader() {
        $output = "<?php\r\n";
        $output .= $this->createLicenseHeader();
        if (null !== $this->config->namespace) {
            $output .= "namespace " . $this->config->namespace . ";";
            $output .= "\r\n";
        }
        $output .= "\r\n"
                . "/**\r\n"
                . " * Description of " . $this->getClassName() . "\r\n"
                . " *\r\n"
                . " */\r\n"
                . "class " . $this->getClassName() . " extends \DatabaseParser\AbstractElement\r\n"
                . "{\r\n"
                . "\r\n";
        return $output;
    }

    private function createAttributes() {
        $output = "";
        $size = count($this->getFields());
        for ($index = 0; $index < $size; $index++) {
            $field = $this->getField($index);
            $output .= "\tprivate \$" . $field . ";\r\n";
        }
        $output .= "\r\n";
        return $output;
    }

    private function createConstructor() {
        $output = "\tpublic function __construct(array \$data){\r\n"
                . "\t\tparent::__construct(\$data,\"" . $this->getDatabase() . "\",\"" . $this->getTable() . "\"," . $this->getPrimaryKeysAsPhpCode() . ");\r\n"
                . "\t}\r\n"
                . "\r\n";
        return $output;
    }

    public function createSetters() {
        $output = "";
        $size = count($this->getFields());
        for ($index = 0; $index < $size; $index++) {
            $field = $this->getField($index);
            $output .= "\tpublic function set" . ucwords($field) . "(\$value){\r\n"
                    . "\t\t\$this->" . $field . " = \$value;\r\n"
                    . "\t\treturn \$this;\r\n"
                    . "\t}\r\n"
                    . "\r\n";
        }
        return $output;
    }

    public function createGetters() {
        $output = "";
        $size = count($this->getFields());
        for ($index = 0; $index < $size; $index++) {
            $field = $this->getField($index);
            $output .= "\tpublic function get" . ucwords($field) . "(){\r\n"
                    . "\t\treturn \$this->" . $field . ";\r\n"
                    . "\t}\r\n"
                    . "\r\n";
        }
        return $output;
    }

    public function createFooter() {
        return "\r\n"
                . "}\r\n";
    }

    public function build() {
        $output = $this->createHeader();
        $output .= $this->createAttributes();
        $output .= $this->createConstructor();
        $output .= $this->createSetters();
        $output .= $this->createGetters();
        $output .= $this->createFooter();
        Builder::$logger->writeln("OK");
        return $output;
    }

}
