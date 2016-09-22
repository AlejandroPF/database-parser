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
 * Description of Filter
 *
 * @author Alejandro Peña Florentín (alejandropenaflorentin@gmail.com)
 */
class Filter
{

    private $field;
    private $content;
    private $comparator;
    private $operator = Criteria::NONE;

    public function __construct($field, $content, $comparator = Criteria::EQUALS) {
        $this->setField($field);
        $this->setContent($content);
        $this->setComparator($comparator);
    }

    public function getOperator() {
        return $this->operator;
    }

    public function setOperator($operator) {
        $this->operator = $operator;
        return $this;
    }

    public function getField() {
        return $this->field;
    }

    public function getContent() {
        return $this->content;
    }

    public function getComparator() {
        return $this->comparator;
    }

    public function setField($field) {
        $this->field = $field;
        return $this;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function setComparator($comparator) {
        $this->comparator = $comparator;
        return $this;
    }

}
