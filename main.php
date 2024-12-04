<?php
//Your Settings can be read here: settings::read('myArray/settingName') = $settingValue;
//Your Settings can be saved here: settings::set('myArray/settingName',$settingValue,$overwrite = true/false);
class mysql_client{
    //public static function command($line):void{}//Run when base command is class name, $line is anything after base command (string). e.g. > [base command] [$line]
    //public static function init():void{}//Run at startup
    public static function createDatabase(int $connectionNumber, string $databaseName):bool{
        if(self::connectionNumberExists($connectionNumber)){
            $conn = self::connectToServer($connectionNumber);
            if($conn !== false){
                if(preg_match("/^[a-zA-Z0-9_]+$/",$databaseName) === 1){
                    return mysqli_query($conn, "CREATE DATABASE " . $databaseName);
                }
            }
        }
        return false;
    }
    public static function connectToDatabase(int $connectionNumber, string $databaseName):object|bool{
        $connData = self::getConnData($connectionNumber);
        if($connData !== false){
            $password = null;
            if($connData['password'] !== false){
                $password = base64_decode($connData['password']);
            }
            return mysqli_connect($connData['hostname'],$connData['username'],$password,$databaseName,$connData['port']);
        }
        else{
            return false;
        }
    }
    public static function connectToServer(int $connectionNumber):object|bool{
        $connData = self::getConnData($connectionNumber);
        if($connData !== false){
            $password = null;
            if($connData['password'] !== false){
                $password = base64_decode($connData['password']);
            }
            return mysqli_connect($connData['hostname'],$connData['username'],$password,null,$connData['port']);
        }
        else{
            return false;
        }
    }
    public static function connectionNumberExists(int $connectionNumber):bool{
        if(self::getConnData($connectionNumber) !== false){
            return true;
        }
        return false;
    }
    public static function runQuery(int $connectionNumber, string $database, string $query):bool{
        $conn = self::connectToDatabase($connectionNumber, $database);
        return mysqli_query($conn, $query);
    }
    public static function getConnData(int $connectionNumber){
        $connData = false;
        $settings = json_decode(json_encode($GLOBALS['settings']),true);
        if(php_sapi_name() === "cli"){
            if(isset($settings['mysql']['connections'][$connectionNumber])){
                $connData = json_decode(json_encode($settings['mysql']['connections'][$connectionNumber]),true);
            }
        }
        else{
            if(isset($GLOBALS['globalSettings']['mysqlConn'][$connectionNumber])){
                $connData = $GLOBALS['globalSettings']['mysqlConn'][$connectionNumber];
            }
        }
        return $connData;
    }
}