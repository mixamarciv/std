<?php
include_once(dirname(__FILE__)."/strFunc.php");
include_once(dirname(__FILE__)."/paramParser.php");
include_once(dirname(__FILE__)."/class_myDb.php");
include_once(dirname(__FILE__)."/myFile.php");
//-----------------------------------------------------------------------------

function connect_app_param_db($param_parser)
{
    $app_database = $param_parser->fvar("app_database");
    if(strlen($app_database)<1){
      $msg = tr("подключения к БД приложения НЕ ВОЗМОЖНО!, переменная/параметр \"app_database\" незадана\n");
      die($msg);
    }
    $app_host = $param_parser->fvar("app_host");
    //if($app_host!="127.0.0.1")
      $app_database = $app_host.":".$app_database;
    $app_user_name = $param_parser->fvar("app_user_name");
    $app_user_password = $param_parser->fvar("app_user_password");
    
    $app_driver = strtolower(trim($param_parser->fvar("app_driver")));
    if($app_driver=="qibase" || $app_driver=="ibase"){
      $app_driver = "ibase";
    }else{
      //$app_driver = "ibase";
      $msg = tr("данный php скрипт не настроен для подключения к бд приложения через драйвер \"".$app_driver."\" (возможен тока драйвер \"qibase\")\n");
      die($msg);
    }
    
    $db = new myDB();
    
    
    $b = $db->connect($app_driver,$app_database,$app_user_name,$app_user_password,"WIN1251");
    if(!$b){
      $msg = tr("подключения к БД приложения НЕ УСТАНОВЛЕНО!\n".$db->last_error());
      die($msg);
    }
    echo tr("подключение к БД приложения успешно установлено!\n");
     
    return $db;
}

