<?php
include_once(dirname(__FILE__)."/strFunc.php");
include_once(dirname(__FILE__)."/paramParser.php");
include_once(dirname(__FILE__)."/class_myDb.php");
include_once(dirname(__FILE__)."/myFile.php");
//-----------------------------------------------------------------------------

function connect_php_param_db($param_parser)
{
    if(is_null($param_parser))
    {
        echo tr("ошибка вызова функции: connect_php_param_db, переменная param_parser незадана!");
        die("");
    }
    $php_database = $param_parser->fvar("php_database");
    if(strlen($php_database)<1)
    {
      $msg = tr("подключения к БД из php скрипта НЕ ВОЗМОЖНО!, переменная/параметр \"php_database\" незадана\n");
      die($msg);
    }

    $php_user_name = $param_parser->fvar("php_db_user");
    $php_user_password = $param_parser->fvar("php_db_password");
    
    $php_driver = $param_parser->fvar("php_db_driver");
    
    $php_db_codepage = $param_parser->fvar("php_db_codepage");
    $php_db_role = $param_parser->fvar("php_db_role");

    $db = new myDB();
    
    $b = $db->connect($php_driver,$php_database,$php_user_name,$php_user_password,$php_db_codepage,$php_db_role);
    //my_writeToFile("!!connect_log.txt","a","$php_driver,$php_database,$php_user_name,$php_user_password,$php_db_codepage,$php_db_role\n");
    if(!$b)
    {
      $msg = tr("подключения к БД(\"$php_driver\") из php скрипта НЕ УСТАНОВЛЕНО!\n".$db->last_error());
      $msg.= "\ndb_info:\n".$db->info();
      die($msg);
    }
    echo tr("подключение к БД(\"$php_driver\") из php скрипта успешно установлено!\n");
     
    return $db;
}


?>