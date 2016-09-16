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
 * Description of ElementQuery
 *
 * @author Alejandro Peña Florentín (alejandropenaflorentin@gmail.com)
 */
class ElementQuery
{

    private $connection;
    private $database;
    private $table;
    private $filters;

    private function __construct($connection, $database, $table) {
        $this->setConnection($connection);
        $this->database = $database;
        $this->table = $table;
    }

    public function setConnection($connection) {
        $this->connection = $connection;
        return $this;
    }

    public static function create($connection, $database, $table) {
        return new ElementQuery($connection, $database, $table);
    }

    public function filterBy($field, $content, $criteria = Criteria::EQUALS) {
        $this->filters[] = new Filter($field, $content, $criteria);
        return $this;
    }

    public function setOperator($operator) {
        if (count($this->filters) > 0) {
            $filter = $this->filters[count($this->filters) - 1];
            $filter->setOperator($operator);
        }
        return $this;
    }

    public function _or() {
        return $this->setOperator(Criteria::_OR);
    }

    public function _and() {
        return $this->setOperator(Criteria::_AND);
    }

    public function find() {
        $output = [];
        $str = "select * from " . $this->database . "." . $this->table;
        if (count($this->filters) > 0) {
            $str .= " where ";
            foreach ($this->filters as $filter) {
                $comp = "";
                switch ($filter->getComparator()) {
                    case Criteria::EQUALS:
                        $comp = "=";
                        break;
                    case Criteria::GREATHER_THAN:
                        $comp = ">";
                        break;
                    case Criteria::LOWER_THAN:
                        $comp = "<";
                    case Criteria::LIKE:
                        $comp = "like";
                        break;
                }
                $str .= $filter->getField() . " " . $comp . "\"" . $filter->getContent() . "\"";
                if ($filter->getOperator() != Criteria::NONE) {
                    $str .= " " . $filter->getOperator() . " ";
                }
            }
            var_dump($str);
            $sql = $this->connection->query($str);
            if ($sql && $sql->num_rows > 0) {
                for ($index = 0; $index < $sql->num_rows; $index++) {
                    $output[$index] = $sql->fetch_assoc();
                }
            }
        }
        return $output;
    }

}
