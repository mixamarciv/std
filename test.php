<?php

include_once(dirname(__FILE__)."/class_myDb.php");

$driver         = "ibase";
$database       = "192.168.1.5:e:/_db/data/altdbclient_app_data.fdb";
$user_name      = "sysdba";
$user_password  = "cde3vfr4";
$db_codepage    = NULL;//"WIN1251";
$db_role        = NULL;//"test";

$db = new myDB();

$b = $db->connect($driver,$database,$user_name,$user_password,$db_codepage,$db_role);

if(!$b) die($msg = tr("подключения к БД НЕ УСТАНОВЛЕНО!\n").$db->last_error());
echo tr("подключения к БД УСПЕШНО УСТАНОВЛЕНО!\n");

$query_text = "SELECT id_db_user,sys_user_name FROM db_user";
$q = $db->query();
$b = $q->exec($query_text);

if(!$b) die(tr("запрос к БД НЕ ВЫПОЛНЕН!\n").$q->last_error());
echo tr("запрос к БД УСПЕШНО ВЫПОЛНЕН!\n");

$cnt_fields = $q->fields_count();
echo tr("вывод данных $cnt_fields полей:\n");
for($i=0;$i<$cnt_fields;$i++)
{
    $field = $q->field_info($i);
    echo $field['name']."\t";
}

while($row = $q->fetch_row())
{
    echo "\n";
    for($i=0;$i<$cnt_fields;$i++)
        echo $row[$i]."\t\t";
}
?>