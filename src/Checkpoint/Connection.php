<?php

/**
 * This file is part of the General utility.
 *
 * (c) Sankar suda <sankar.suda@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Checkpoint;

use Checkpoint\Util\Log;

/**
 * @author sankar <sankar.suda@gmail.com>
 */

class Connection
{
    private static $db = null;

    private static $config = array();

    private static $instance = null;

    public static function instance($config = array())
    {
        self::$config = $config;
        if(self::$instance == null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    function getConnection($config = array())
    {
        if(is_null(self::$db) && !empty($config)) {
            $this->connect($config);
        }
        return self::$db;
    }

    function connect($config = array())
    {
        if(!empty($config)) {
            self::$config = $config;
        }

        if(self::$config['persistent'])
            self::$db  = mysqli_connect('p:'.self::$config['host'],self::$config['username'],self::$config['password']);
        else
            self::$db  = mysqli_connect(self::$config['host'],self::$config['username'],self::$config['password']);

        if(!self::$db) {
            Log::critical('Unable to connect to the database');
            return false;
        }

        if(!mysqli_select_db(self::$db,self::$config['database'])){
            Log::critical('Unable to select the database');
            return false;          
        }

        return true;
    }

    function disconnect()
    {
        if(is_resource(self::$db))
            return mysqli_close(self::$db);
        
        return true;
    }

    function reConnect()
    {
        //return true;
        if(is_null(self::$db) 
            //|| !is_resource(self::$db)
            || !mysqli_ping(self::$db)
            ) {
            Log::info(date('Y-m-d h:iA').': Lost connection, with error: '.mysqli_error(self::$db).',connecting..');
            $this->disconnect();
            return $this->connect();
        }

        return true;
    }

    function escape($string)
    {
        return mysqli_real_escape_string(self::$db,trim($string));
    }

    function query($sql,$id=false)
    {
        $res = mysqli_query(self::$db,$sql);
        if (!$res) {
            Log::critical(mysqli_error(self::$db));
            exit(0);
            return false;
        }
        if($id) {
            return mysqli_insert_id(self::$db);
        }
       
     return $res;
    }

    function lastInsertId()
    {
        return mysqli_insert_id(self::$db);
    }

    function fetchAll($sql)
    {
        
        $res = $this->query($sql);
        $rows = array();

        while ($row = mysqli_fetch_assoc($res)) {
            $rows[] = $row;
        }
        mysqli_free_result($res);

        return $rows;
    }

    function fetchAssoc($sql)
    {
        $res = $this->query($sql);
        $row = mysqli_fetch_assoc($res);
        mysqli_free_result($res);
        return $row;
    }

    function save($table,$save = array()){
    
        $k = array();
        $v = array();
        if(is_array($save[0])){
            foreach($save as $key=>$val){
                $v[] = "('".implode("','",$val)."')";
            }
            $k = array_keys($save[0]);
        }else{
            $k = array_keys($save);
            $v = array("('".implode("','",$save)."')");
        }
    
        $q = "INSERT INTO ".$table." (`".implode('`,`',$k)."`) VALUES ".implode(',',$v)."";
        return $this->query($q);                                         
    }   
}
