<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DatabaseParser;

/**
 * Description of Logger
 *
 * @author Alejandro Peña Florentín (alejandropenaflorentin@gmail.com)
 */
class Logger
{

    public function __construct() {
        
    }
    public function writeln($str){
        $this->write($str."\r\n");
    }
    public function write($str) {
        echo $str;
    }

}
