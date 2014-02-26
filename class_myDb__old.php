<?php
//Marciv M.N.(c) mail:mixamarciv@ya.ru
include_once(dirname(__FILE__)."/strFunc.php");
include_once(dirname(__FILE__)."/myFile.php");
//-----------------------------------------------------------------------------
class myDB
{//класс для работы с БД,
 //велосипед предназначен для того чтобы при коннекте просто указать название драйвера!
 //а не вспоминать/искать каждый раз как называется та или иная функция и как она работает и как получить код ошибки при её выполнении
    var $m_connect;
    var $m_db_driver;
        
    var $m_dataBase;
    var $m_user;
    var $m_pass;
    var $m_options;
    var $m_role;
        
    var $m_last_error;
    
    function driver() { return $this->m_db_driver; }
    
    function info(){
        $str = "\"".$this->m_db_driver."\" open:";
        if($this->m_connect)
          $str .= 1;
        else
          $str .= 0;
        
        $str .= " db:\"".$this->m_dataBase."\" user:\"".$this->m_user."\"";
        if(strlen($this->m_role)>0)
          $str .=" role:\"".$this->m_role."\"";
        return $str;
    }
    
    private function get_last_error(){
        $this->m_last_error = NULL;
        switch ($this->m_db_driver)
        {
            case "ibase":
                $t = ibase_errcode();
                if( !empty($t) ) $this->m_last_error = "myDB ibase ERROR [".$t."] ".tr(ibase_errmsg(),"CP1251");
                break;
            case "odbc":
                $t = NULL;
                if($this->m_connect == 0)  $t = odbc_error();
                else                       $t = odbc_error($this->m_connect);
                if( !empty($t) )
                {
                    $msg = NULL;
                    if($this->m_connect == 0)  $msg = odbc_errormsg();
                    else                       $msg = odbc_errormsg($this->m_connect);
                    $this->m_last_error = "myDB ODBC ERROR [".$t."] ".tr($msg,"CP1251");
                }
                break;
            case "oracle":
                $t = NULL;
                if($this->m_connect == 0)  $t = OCIError();
                else                       $t = OCIError($this->m_connect);
                if( !empty($t) ) $this->m_last_error = "myDB oracle ERROR [".$t['code']."] ".tr($t['message'],"CP1251");
                break;
            case "oci8":
                $t = NULL;
                if($this->m_connect == 0)  $t = oci_error();
                else                       $t = oci_error($this->m_connect);
                if( !empty($t) ) $this->m_last_error = "myDB oracle ERROR [".$t['code']."] ".tr($t['message'],"CP1251");
                break;
            default:
                $this->m_last_error = "myDB ERROR driver \"".$this->m_db_driver."\" not defined!";
        }
        return $this->m_last_error;
    }
    
    function last_error() { return $this->m_last_error; }
    
    function connect($db_driver,$dataBase,$user,$pass,$options=NULL,$role=NULL){
        $this->m_last_error = NULL;
        $this->m_db_driver = strtolower($db_driver);
        $this->m_connect = 0;
        $this->m_dataBase = $dataBase;
        $this->m_user = $user;
        $this->m_pass = $pass;
        $this->m_options = $options;
        $this->m_role = $role;
        switch ($this->m_db_driver){
            case "qibase":
                $this->m_db_driver = "ibase";
            case "ibase":
                try{
                    //echo tr("ibase_connect($dataBase, $user, $pass, $options,0,3,$role);");
                    $this->m_connect = ibase_connect($dataBase, $user, $pass, $options,0,3,$role);
                }catch(Exception $e) {
                    echo 'exception: ',  $e->getMessage(), "\n";
                    $this->get_last_error();
                }
                
                if($this->m_connect == 0) $this->get_last_error();
                break;
            
            case "qodbc3":
            case "qodbc":
                $this->m_db_driver = "odbc";
            case "odbc":
                $this->m_connect = odbc_connect($dataBase, $user, $pass);
                if($this->m_connect == 0) $this->get_last_error();
                break;
            case "qoci":
                $this->m_db_driver = "oracle";
            case "oracle":
                $this->m_connect = OCILogon($user, $pass, $dataBase);
                if($this->m_connect == 0) $this->get_last_error();
                break;
            case "oci8":
                $this->m_connect = oci_connect($user, $pass, $dataBase, $options);
                if($this->m_connect == 0) $this->get_last_error();
                break;
            default:
                $this->m_last_error = "myDB ERROR driver \"".$this->m_db_driver."\" not defined";
        }
        return $this->m_connect;
    }
    
    function set_connect($db_driver,$connect){
      $this->m_connect = $connect;
      $this->m_db_driver = strtolower($db_driver);
    }
    
    function isOpen() {return (bool)$this->m_connect;}
    
    function disconnect(){
        $this->m_last_error = NULL;
        if($this->m_connect == 0){
            $this->m_last_error = tr("myDB ".$this->m_db_driver." not connected!");
            return 0;
        }
        
        $t_disconnect = 0;
        switch ($this->m_db_driver){
            case "ibase":
                $t_disconnect = ibase_close($this->m_connect);
                break;
            case "odbc":
                $t_disconnect = 1;
                odbc_close($this->m_connect);
                break;
            case "oracle":
                $t_disconnect = OCILogoff($this->m_connect);
                break;
            case "oci8":
                oci_close($this->m_connect);
                break;
            default:
                $this->m_last_error = "myDB ERROR driver \"".$this->m_db_driver."\" not defined";
        }
        if($t_disconnect) $this->m_connect = NULL;
        else              $this->get_last_error();
        return $t_disconnect;
    }
    
    function close(){ return $this->disconnect(); }
    
    function query(){
        $query = new myDBQuery();
        $query->m_myDB = $this;
        return $query;
    }
    
    function exec($str_query)
    {
        $query = new myDBQuery();
        $query->m_myDB = $this;
        $b = $query->exec($str_query);
        if($b == 0){
            $this->m_last_error = $query->last_error();
            return 0;
        }
        return $query;
    }
};

class myDBQuery
{//класс для работы с запросами к myDB
    var $m_myDB;
    var $m_result;  //query result
    
    var $m_last_error;
    var $m_last_query;
    
    function get_last_error(){
        my_writeToFile(__FILE__.".dbg","ab","\n".$this->m_last_query.
        "\n===========================================================================");
        if( $this->m_myDB == NULL || $this->m_myDB->m_connect == NULL )
        {
            $this->m_last_error = "myDBQuery ".$this->m_myDB->m_db_driver." ERROR no connect!";
            return $this->m_last_error;
        }else{
            $this->m_last_error = NULL;
            switch ($this->m_myDB->m_db_driver)
            {
                case "ibase":
                    $t = ibase_errcode();
                    if( !empty($t) ) $this->m_last_error = "myDBQuery ibase ERROR [".$t."] ".tr(ibase_errmsg(),"CP1251","IBM866");
                    break;
                case "odbc":
                    $t = odbc_error($this->m_myDB->m_connect);
                    if( !empty($t) )
                    {
                        $msg = odbc_errormsg($this->m_myDB->m_connect);
                        $this->m_last_error = "myDBQuery ODBC ERROR [".$t."] ".tr($msg,"CP1251");
                    }
                    break;
                case "oracle":
                    $t = OCIError($this->m_result);
                    if( !empty($t) )
                      $this->m_last_error = "myDBQuery oracle ERROR [".$t['code']."] ".tr($t['message'],"CP1251");
                    break;
                case "oci8":
                    $t = oci_error($this->m_result);
                    if( !empty($t) )
                      $this->m_last_error = "myDBQuery oracle ERROR [".$t['code']."] ".tr($t['message'],"CP1251");
                    break;
                default:
                    $this->m_last_error = "myDBQuery ERROR driver \"".$this->m_myDB->m_db_driver."\" not defined";
            }
        }
        return $this->m_last_error;
    }
    
    function last_error() { return $this->m_last_error; }
    function last_query() { return $this->m_last_query; }

    function exec($str_query){
        $this->m_result = NULL;  //$this->clear();
        $this->m_last_error = NULL;
        $this->m_count_fields = NULL;
        $this->m_last_query = $str_query;
        if( empty($this->m_myDB) )
        {
            $this->get_last_error();
            return 0;
        }
        


        switch($this->m_myDB->m_db_driver){
            case "ibase":
                //try{
                  $this->m_result = ibase_query( $this->m_myDB->m_connect, $str_query);
                /*
                }catch(Exception $e){
                  echo "Выброшено исключение: ",  $e->getMessage(), "\n";
                  echo "<pre>";
                  var_dump(vardebug_backtrace());
                  echo "</pre>";
                }
                */
                if($this->m_result == 0){
                    $this->get_last_error();
                    return 0;
                }
                break;
            case "odbc":
                $this->m_result = @odbc_exec( $this->m_myDB->m_connect, $str_query);
                if($this->m_result == 0){
                    $this->get_last_error();
                    return 0;
                }
                break;
            case "oracle":
                $this->m_result = OCIParse( $this->m_myDB->m_connect, $str_query);
                if($this->m_result == 0){
                    $this->get_last_error();
                    return 0;
                }
                $b = @OCIExecute($this->m_result, OCI_DEFAULT);
                if($b == 0){
                    $this->get_last_error();
                    return 0;
                }
                break;
            case "oci8":
                $this->m_result = oci_parse( $this->m_myDB->m_connect, $str_query);
                if($this->m_result == 0){
                    $this->get_last_error();
                    return 0;
                }
                $b = @oci_execute($this->m_result, OCI_DEFAULT);
                if($b == 0)
                {
                    $this->get_last_error();
                    return 0;
                }
                break;
            default:
                $this->m_last_error = "myDBQuery ERROR driver \"".$this->m_myDB->m_db_driver."\" not defined";
        }
        return 1;
    }
    
    
    function fetch_row()
    {//загрузка следующей строки запроса,
        //в если строка успешно загружена возврашается массив с данными
        //в противном случае если достигнут конец строк и в случае ошибки возвращается 0
        if( empty($this->m_myDB) ){
            $this->get_last_error();
            return 0;
        }
        if( $this->m_result == 0 ){
            $this->m_last_error = "myDBQuery ERROR query not executed";
            return 0; 
        }
        $this->m_last_error = NULL;

        switch($this->m_myDB->m_db_driver){
            case "ibase":
                return ibase_fetch_row ($this->m_result);
                break;
            case "odbc":
                $tmp = odbc_fetch_array($this->m_result);
                if($tmp != 0)
                {
                    $ret_data = NULL;
                    $i = 0;
                    foreach($tmp as $value)
                        $ret_data[$i++] = $value;
                    return $ret_data;
                }else{
                    return 0;
                }
                break;
            case "oci8":
            case "oracle":
                //если ругается на "oci_fetch_row(): ORA-24338:" - то проверь, был ли вообще задан запрос!!
                $ret_data = oci_fetch_row ($this->m_result);
                //$ret_data = OCIFetch($this->m_result);
                return $ret_data;
                break;
            default:
                $this->m_last_error = "myDBQuery ERROR driver \"".$this->m_myDB->m_db_driver."\" not defined";
        }
        return 0;
    }
    
    function clear()
    {
        $this->m_last_query = null;
        if( empty($this->m_myDB) ){
            $this->get_last_error();
            return 0;
        }
        if( $this->m_result == 0 ){
            $this->m_last_error = "myDBQuery ERROR query not executed";
            return 0; 
        }
        $this->m_last_error = NULL;
        $b = 0;

        switch($this->m_myDB->m_db_driver)
        {
            case "ibase":
                $b = ibase_free_result ($this->m_result);
                if($b == 1) $this->m_result = NULL;
                else        $this->get_last_error();
                break;
            case "odbc":
                $b = odbc_free_result ($this->m_result);
                if($b == 1) $this->m_result = NULL;
                else        $this->get_last_error();
                break;
            case "oci8":
            case "oracle":
                $b = 1;
                $this->m_result = NULL;
                break;
            default:
                $this->m_last_error = "myDBQuery ERROR driver \"".$this->m_myDB->m_db_driver."\" not defined";
        }
        return $b;
    }
    
    function free() { return $this->clear(); }
    
    function field_info($n_field)
    {
        if( empty($this->m_myDB) )
        {
            $this->get_last_error();
            return 0;
        }
        if( $this->m_result == 0 )
        {
            $this->m_last_error = "myDBQuery ERROR query not executed";
            return 0; 
        }
        $this->m_last_error = NULL;
        //$field_info['name'] = NULL;
        //$field_info['type'] = NULL;
        //$field_info['size'] = NULL;
        //$field_info['precision'] = NULL;
        
        switch($this->m_myDB->m_db_driver)
        {
            case "ibase":
                $tmp = ibase_field_info ($this->m_result,$n_field);
                if($tmp == 0) return 0;
                $field_info['name']      = $tmp['alias']; /*$tmp['name'];*/
                $field_info['type']      = $tmp['type'];
                $field_info['size']      = $tmp['length'];
                $field_info['precision'] = NULL;
                return $field_info;
                break;
            case "odbc":
                $n_field++; //в ODBC номера полей начинаются с 1
                $field_info['name']      = odbc_field_name ($this->m_result,$n_field);
                $field_info['type']      = odbc_field_type ($this->m_result,$n_field);
                $field_info['size']      = odbc_field_len  ($this->m_result,$n_field);
                $field_info['precision'] = odbc_field_scale($this->m_result,$n_field);
                return $field_info;
                break;
            case "oci8":
            case "oracle":
                $n_field++;
                $field_info['name']      = oci_field_name($this->m_result, $n_field);
                $field_info['type']      = oci_field_type($this->m_result, $n_field);
                $field_info['size']      = oci_field_size($this->m_result, $n_field);
                $field_info['precision'] = oci_field_precision($this->m_result, $n_field);
                return $field_info;
                break;
            default:
                $this->m_last_error = "myDBQuery ERROR driver \"".$this->m_myDB->m_db_driver."\" not defined";
        }
        return 0;
    }
    
    function fields_names()
    {//список имен всех полей
     //пока такой вариант, в будущем исправлю
        if( empty($this->m_myDB) )
        {
            $this->get_last_error();
            return 0;
        }
        if( $this->m_result == 0 )
        {
            $this->m_last_error = "myDBQuery ERROR query not executed";
            return 0; 
        }
        $this->m_last_error = NULL;
        //$field_info['name'] = NULL;
        //$field_info['type'] = NULL;
        //$field_info['size'] = NULL;
        //$field_info['precision'] = NULL;
        $fields_names = NULL;
        for($i=0;$i<$this->fields_count();$i++){
            $finfo = $this->field_info($i);
            $fields_names[$i]= $finfo['name'];
        }
        
        return $fields_names;
    }
    
    function names_fields()
    {//список имен всех полей
        return $this->fields_names();
    }
    
    function simple_field_type($n_field)
    {
      $field_info = $this->field_info($n_field);
      if($field_info==0) return 0;
      $field_type = substr(strtolower(trim($field_info['type'])),0,4);
      switch($field_type)
      {
        case "varc": return "string";   //varchar
        case "char": return "string";   //char,character
        case "nume": return "double";   //numeric(10,2)
        case "smal": return "integer";  //smallint
      }
      return strtolower(trim($field_info['type']));
    }
    
    
    function fields_count()
    {
        if( empty($this->m_myDB) )
        {
            $this->get_last_error();
            return 0;
        }
        if( $this->m_result == 0 )
        {
            $this->m_last_error = "myDBQuery ERROR query not executed";
            return 0; 
        }
        $this->m_last_error = NULL;
       
        $cnt = 0;
        switch($this->m_myDB->m_db_driver)
        {
            case "ibase":
                $cnt = ibase_num_fields ($this->m_result);
                break;
            case "odbc":
                $cnt = odbc_num_fields ($this->m_result);
                if($cnt==-1) $cnt = 0;
                break;
            case "oci8":
            case "oracle":
                $cnt = oci_num_fields($this->m_result);
                break;
            default:
                $this->m_last_error = "myDBQuery ERROR driver \"".$this->m_myDB->m_db_driver."\" not defined";
        }
        return $cnt;
    }
};
//-----------------------------------------------------------------------------
function test_db($db_driver,$dataBase,$user,$pass,$options=NULL,$role=NULL,$str_query="SELECT * FROM dual",$out_file=NULL)
{//типа тест для предыдущих 2 классов
  $test = new myDB();
  echo $db_driver;
  
  echo "\n--connect-------------------------------------------------------------\n";
  $b = $test->connect($db_driver,$dataBase,$user,$pass,$options,$role);
  if(!$b) return;
  echo $b;
  echo "\n--err-----------------------------------------------------------------\n";
  echo $test->last_error();
  
  echo "\n--exec-----------------------------------------------------------------\n";
  $query = $test->exec($str_query);
  echo $query;
  echo "\n--err------------------------------------------------------------------\n";
  echo $test->last_error();
  
  if($query)
  {
  echo "\n--query----------------------------------------------------------------\n";
  $row = $query->fetch_row();
  if($row!=0)
  {
    echo var_dump($row);
  }
  echo "\n----------------------------------------------------------------------\n";
  
  echo "\n--field---------------------------------------------------------------\n";
  $field = $query->field_info(0);
  echo var_dump($field);
  echo "\n----------------------------------------------------------------------\n";
  
  echo "\n--query clear---------------------------------------------------------\n";
  echo $query->clear();
  echo "\n--query err-----------------------------------------------------------\n";
  echo $query->last_error();
  }
  
  echo "\n--disconnect----------------------------------------------------------\n";
  echo $test->disconnect();
  echo "\n--err-----------------------------------------------------------------\n";
  echo $test->last_error();
  
  echo "\n======================================================================\n";
}
//test_db("ibase","D:\\_db\\data\\APP_DATA.FDB","SYSDBA","masterkey","WIN1251","test","SELECT * FROM ui_table;");
//test_db("ODBC","Driver=Firebird/InterBase(r) driver;Uid=SYSDBA; DbName=192.168.1.5:E:\\_db_web\\0001.fdb;CHARSET=win1251;",NULL,NULL,NULL,NULL,"SELECT * FROM company_name;");
//test_db("ODBC","DRIVER={Microsoft ODBC for Oracle};UID=aga;PWD=xer_tebe;SERVER=kakayatobaza;",NULL,NULL,NULL,NULL,"SELECT * FROM dictionary");
//test_db("oracle","kakayatobaza","xer_tebe","xer_tebe",NULL,NULL,"SELECT * FROM dictionary");
//test_db("oci8","192.168.1.3/baprod.center","user","pass",NULL,NULL,"SELECT * FROM dictionary");

function export_table_to_CSV($db,$table,$out_csv_file_name){
  $q = $db->exec("SELECT * FROM $table");
  $names = $q->fields_names();
  $fields_cnt = count($names);
  $buff = "";
  for($i=0;$i<$fields_cnt;$i++){
    $buff.=$names[$i].";";
  }
  $buff.="\n";
  my_writeToFile($out_csv_file_name, "wb", $buff);
  while($row = $q->fetch_row()){
    $buff = "";
    for($i=0;$i<$fields_cnt;$i++){
      $buff.=$row[$i].";";
    }
    $buff.="\n";
    my_writeToFile($out_csv_file_name, "ab", $buff);
  }
}


