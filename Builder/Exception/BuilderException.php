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

namespace Builder\Exception;

/**
 * Description of BuilderException
 *
 * @author Alejandro Peña Florentín (alejandropenaflorentin@gmail.com)
 */
class BuilderException extends \Exception
{
    const DATABASE_NOT_FOUND = 1404;
    
    public function __construct($message = "", $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    public static function create($code,$extra){
        $output = null;
        switch ($code){
            case self::DATABASE_NOT_FOUND:
                $output = new self("Database '".$extra."' not found",$code,null);
                break;
        }
        return $output;
    }
}
