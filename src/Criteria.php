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
 * Description of Criteria
 *
 * @author Alejandro Peña Florentín (alejandropenaflorentin@gmail.com)
 */
class Criteria
{
    const NONE = null;
    const _AND = "and";
    const _OR = "or";
    const _LIKE = "like";
    const EQUALS = 0;
    const GREATHER_THAN = 1;
    const LOWER_THAN = -1;
    const LIKE = 10;
}
