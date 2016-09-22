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
 * ElementQuery
 *
 * @author Alejandro Peña Florentín (alejandropenaflorentin@gmail.com)
 */
class ElementQuery
{

    /**
     * @var \mysqli MySQL connection
     */
    private $connection;

    /**
     * @var string Database name
     */
    private $database;

    /**
     * @var string Table name
     */
    private $table;

    /**
     * @var array Array of filters
     */
    private $filters;
    /**
     * @var array 'order by' fields
     */
    private $order;
    
    /**
     * @var string 'limit' string
     */
    private $limit;
    
    /**
     * @var string 'group by' string
     */
    private $groupBy;
    private function __construct($connection, $database, $table) {
        $this->setConnection($connection);
        $this->database = $database;
        $this->table = $table;
    }

    /**
     * Sets connection
     * @param \mysqli $connection MySQL connection
     * @return \DatabaseParser\ElementQuery Fluent setter
     */
    public function setConnection($connection) {
        $this->connection = $connection;
        return $this;
    }

    /**
     * Creates a new query
     * @param \mysqli $connection MySQL connection
     * @param string $database Database name
     * @param string $table Table name
     * @return \DatabaseParser\ElementQuery ElementQuery instance
     */
    public static function create($connection, $database, $table) {
        return new ElementQuery($connection, $database, $table);
    }

    /**
     * Adds a new filter
     * @param string $field Field name of the table
     * @param string $content String to compare
     * @param int $criteria Criteria
     * @return \DatabaseParser\ElementQuery Fluent method
     * @see Criteria::EQUALS
     * @see Criteria::GREATHER_THAN
     * @see Criteria::LOWER_THAN
     * @see Criteria::LIKE
     */
    public function filterBy($field, $content, $criteria = Criteria::EQUALS) {
        $this->filters[] = new Filter($field, $content, $criteria);
        return $this;
    }

    /**
     * Sets operator to the last filter (if any)
     * @param string $operator Operator (and, or, like...)
     * @return \DatabaseParser\ElementQuery Fluent setter
     */
    public function setOperator($operator) {
        if (count($this->filters) > 0) {
            $filter = $this->filters[count($this->filters) - 1];
            $filter->setOperator($operator);
        }
        return $this;
    }
    public function orderBy($orderBy){
        $this->order = $orderBy;
        return $this;
    }
    public function limit($limit){
        $this->limit = $limit;
        return $this;
    }
    public function groupBy($groupBy){
        $this->groupBy = $groupBy;
        return $this;
    }
    /**
     * Adds an 'or' operator to add more filters
     * @return \DatabaseParser\ElementQuery Fluent method 
     */
    public function _or() {
        return $this->setOperator(Criteria::_OR);
    }

    /**
     * Adds an 'and' operator to add more filters
     * @return \DatabaseParser\ElementQuery Fluent method 
     */
    public function _and() {
        return $this->setOperator(Criteria::_AND);
    }
    /**
     * Executes the query
     * @return array Array with the results (can be an empty array)
     */
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
        }
        if("" != trim($this->groupBy)){
            $str .= " group by ".$this->groupBy;
        }
        if("" != trim($this->order)){
            $str .= " order by ".$this->order;
        }
        if("" != trim($this->limit)){
            $str .= " limit ".$this->limit;
        }
        $sql = $this->connection->query($str);
        if ($sql && $sql->num_rows > 0) {
            for ($index = 0; $index < $sql->num_rows; $index++) {
                $output[$index] = $sql->fetch_assoc();
            }
        }
        return $output;
    }
    /**
     * Saves an instance of class \DatabaseParser\AbstractElement to its database.table
     * @param \mysqli $connection MySQL connection
     * @param \DatabaseParser\AbstractElement $element Object to be saved
     * @return boolean TRUE on success, FALSE otherwise
     * @throws \DatabaseParser\Builder\Exception\BuilderException If primary key is empty
     */
    public static function save($connection, AbstractElement $element) {
        $fields = $element->getFields();
        $size = count($fields);
        $updArr = [];
        for ($index = 0; $index < $size; $index++) {
            $fn = "get" . ucwords($fields[$index]);
            $value = $element->$fn();
            if (strlen($value) == 0 || "" == $value) {
                $value = "null";
            } else {
                $value = "\"$value\"";
            }
            $updArr[$index] = $fields[$index] . "=" . $value;
        }
        $primaryKeys = $element->getPrimaryKeys();
        $whereArr = [];
        for ($index = 0; $index < count($primaryKeys); $index++) {
            $fn = "get" . ucwords($primaryKeys[$index]);
            $value = $element->$fn();
            if (empty($value)) {
                throw Builder\Exception\BuilderException::create(Builder\Exception\BuilderException::PRIMARY_KEY_IS_EMPTY, $primaryKeys[$index]);
                return false;
            }
            $whereArr[] = $primaryKeys[$index] . "=\"" . $value . "\"";
        }
        $where = " where " . implode(" and ", $whereArr);
        $updStr = "UPDATE " . $element->getDatabaseName() . "." . $element->getTableName() . " SET " . implode(", ", $updArr) . $where;
        $sql = $connection->query($updStr);
        if ($sql && $connection->affected_rows == 1) {
            return true;
        } else {
            return false;
        }
    }

}
