<?php

/*
 *  Copyright (c) 2012  Rasmus Fuhse <fuhse@data-quest.de>
 * 
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

class AdminerAutologin {
    protected $server;
    protected $username;
    protected $database;
    protected $password;
    
	function credentials() {
        // server, username and password for connecting to database 
        $this->fetchVars();
        return array(
            $this->server,
            $this->username,
            $this->password
        );
    }
    
    /*function database() {
        // database name, will be escaped by Adminer
        $this->fetchVars();
        return $this->database;
    }*/
    
    protected function fetchVars() {
        if (file_exists(dirname(__file__)."/../config.php")) {
            include dirname(__file__)."/../config.php";
            unlink(dirname(__file__)."/../config.php");
            $this->server = $STUDIP_DB_SERVER;
            $this->username = $STUDIP_DB_USER;
            $this->database = $STUDIP_DB_NAME;
            $this->password = $STUDIP_DB_PASSWORD;
        }
    }
	
}

