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
 * Description of ClassBuilder
 *
 * @author Alejandro Peña Florentín (alejandropenaflorentin@gmail.com)
 */
class ClassBuilder
{

    private $fields = [];

    public function __construct($fields) {
        $this->setFields($fields);
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

}
