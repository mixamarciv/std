<?php
//Marciv M.N.(c) mail:mixamarciv@ya.ru
include_once(dirname(__FILE__)."/strFunc.php");
include_once(dirname(__FILE__)."/myFile.php");
//-----------------------------------------------------------------------------
//для подключения к бд mysql используем строку host=127.0.0.1;$database; - если хост не ==localhost
class myDB{
 //класс для работы с БД,
 //велосипед предназначен толоько для того чтобы при коннекте просто указать название драйвера и параметры подключения(не вспоминая названия функций выбранной бд)!
    var $m_myDB_class;  //класс используемый для работы с выбраным драйвером бд
    var $m_db_driver;   //строка с названием используемого драйвера для подключения к выбранно бд
    
    function __construct(){
        $this->m_myDB_class = NULL;
        $this->m_db_driver = NULL;
    }
    
    function driver() { return $this->m_db_driver; }
    function myDB_class() { return $this->m_myDB_class; }
    function driver_class() { return $this->m_myDB_class; }
    
    function info(){
        $str = "myDB: ";
        if($this->m_myDB_class==NULL){
            $str .= "myDB_class==NULL";
            return $str;
        }
        $str .= $this->m_myDB_class->info();
        return $str;
    }
    
    function m_get_last_error(){
        if($this->m_db_driver==NULL){
            return ($this->m_last_error="myDB ERROR: driver not defined");
        }
        if($this->m_myDB_class==NULL){
            return ($this->m_last_error="myDB ERROR: myDB_".$this->m_db_driver." not defined(driver:".$this->m_db_driver.")");
        }
        $this->m_last_error = $this->m_myDB_class->last_error();
        return $this->m_last_error;
    }
    
    function last_error() { return $this->m_last_error; }
    
    function connect($db_driver,$dataBase,$user,$pass,$options=NULL,$role=NULL){
        $this->disconnect();
        $this->m_last_error = NULL;
        $this->m_myDB_class = NULL;
        $this->m_db_driver = NULL;
        $db_driver = strtolower(trim($db_driver));
        switch ($db_driver){
            case "qibase":
            case "ibase":
            case "firebird":
            case "interbase":
                $this->m_db_driver = "ibase";
                break;
            case "qodbc3":
            case "qodbc":
            case "odbc":
                $this->m_db_driver = "odbc";
                break;
            case "qoci":
            case "oci8":
            case "oci":
            case "oracle":
                $this->m_db_driver = "oci8";
                //$this->m_connect = oci_connect($user, $pass, $dataBase, $options);
                break;
            default:
                $this->m_db_driver = $db_driver;
                //$this->m_last_error = "myDB ERROR: driver \"".$db_driver."\" not defined!";
        }
        $class_name = "myDB_".$this->m_db_driver;
        if(!class_exists($class_name)){
            $this->m_last_error="myDB ERROR: class myDB_".$this->m_db_driver." not defined(driver:".$this->m_db_driver.")";
            return 0;
        }
        $this->m_myDB_class = new $class_name();
        $b = $this->m_myDB_class->connect($dataBase,$user,$pass,$options,$role);
        return $b;
    }
    
    function isOpen() {
        if($this->m_db_driver==NULL || $this->m_myDB_class==NULL){
            $this->m_get_last_error();
            return 0;
        }
        return $this->m_myDB_class->isOpen();
    }
    
    function disconnect(){
        if($this->m_db_driver==NULL || $this->m_myDB_class==NULL){
            $this->m_get_last_error();
            return 0;
        }
        return $this->m_myDB_class->disconnect();
    }
    
    function close(){ return $this->disconnect(); }
    
    function query(){
        $query = new myDBQuery($this);
        return $query;
    }
    
    function exec($str_query,$options=array()){
        if($this->m_db_driver==NULL || $this->m_myDB_class==NULL){
            $this->m_get_last_error();
            return 0;
        }
        $query = new myDBQuery($this);
        $b = $query->exec($str_query,$options);
        if($b == 0){
            $this->m_last_error = $query->last_error();
            return 0;
        }
        return $query;
    }
};

class myDBQuery{
  //класс для работы с запросами к myDB
    var $m_myDB;             
    var $m_myDBQuery_class;  //экземпляр класса myDBQuery_ibase или myDBQuery_odbc
    var $m_fields_info;      //информация по полям загруженного запроса
    
    function __construct($p_myDB){
        $this->m_myDB = $p_myDB;
        $this->m_fields_info = 0;
        $driver = $this->m_myDB->driver();
        $query_class = "myDBQuery_".$driver;
        if(class_exists($query_class)){
            $this->m_myDBQuery_class = new $query_class($this->m_myDB->driver_class());
        }else{
            $this->m_myDBQuery_class = NULL;
            $this->m_get_last_error();
        }
    }
    
    function m_get_last_error(){
        if( $this->m_myDB == NULL){
            return "myDBQuery ERROR: no connect (myDB==NULL)!";
        }elseif( $this->m_myDB->isOpen() == 0 ){
            return "myDBQuery ERROR: (drv:".$this->m_myDB->driver().") no connect (myDB->isOpen()==0)!";
        }elseif($this->m_myDBQuery_class == NULL){
            return "myDBQuery ERROR: (drv:".$this->m_myDB->driver().") no query_class (myDBQuery_".$this->m_myDB->driver().")";
        }
        return $this->m_myDBQuery_class->last_error();
    }
    
    function last_error(){
        $err_msg = $this->m_get_last_error();
        if($err_msg==""){
            $err_msg = $this->m_myDBQuery_class->m_get_last_error();
        }
        return $err_msg;
    }
    
    function last_query() {
      if( $this->m_myDBQuery_class==NULL ) return 0;
      return $this->m_myDBQuery_class->last_query();
    }

    function exec($str_query,$options=array()){
        $this->m_fields_info = 0;
        if( $this->m_myDBQuery_class==NULL ){
            return 0;
        }
        return $this->m_myDBQuery_class->exec($str_query,$options);
    }
    
    function fetch_row($options=0){
      //загрузка следующей строки запроса,
      //если строка успешно загружена возврашается массив с данными
      //противном случае если достигнут конец строк и в случае ошибки возвращается 0
        if( $this->m_myDBQuery_class==NULL ) return 0;
        $row = $this->m_myDBQuery_class->fetch_row();
        if($options==0 || !is_array($options)){
          return $row;
        }
        if(isset($options['index_type']) && $row!=0){
          $type = strtolower(trim($options['index_type']));
          if($type=="assoc"){
            $row_assoc  = $this->get_assoc_row($row);
            return $row_assoc;
          }elseif($type=="both"){
            $row       += $this->get_assoc_row($row);
            //my_var_dump_html2("\$row",$row);
            return $row;
          }elseif($type=="num"){
            return $row;
          }else{
            return "ERRROR: UNDEFINED \"index_type\": \"{$options['index_type']}\"; ";
          }
        }
        return $row;
    }
    
    function get_assoc_row($row){
      $row_assoc = array();
      for($i=0;$i<count($row);$i++){
        $finfo = $this->field_info($i);
        $name = $finfo['name'];
        $row_assoc[$name] = $row[$i];
      }
      return $row_assoc;
    }
    
    function clear(){
        $this->m_fields_info = 0;
        if( $this->m_myDBQuery_class==NULL ) return 0;
        return $this->m_myDBQuery_class->clear();
    }
    function free() { return $this->clear(); }
    
    function fields_count(){
      //возвращает количество полей
        if( $this->m_myDBQuery_class==NULL ) return 0;
        return $this->m_myDBQuery_class->fields_count();
    }
    
    function field_info($n_field){
      //возвращает метаданные указанного поля
      //$field_info['name'] = NULL;
      //$field_info['type'] = NULL;
      //$field_info['size'] = NULL;
      //$field_info['precision'] = NULL;
        if( $this->m_myDBQuery_class==NULL ) return 0;
        if( $this->m_fields_info == 0 ) $this->m_fields_info = array();
        if( !isset($this->m_fields_info[$n_field]) ){
          $this->m_fields_info[$n_field] = $this->m_myDBQuery_class->field_info($n_field);
        }
        return $this->m_fields_info[$n_field];
    }
    
    function fields_names(){
      //возвращает список имен всех полей
      //пока такой вариант, в будущем исправлю
        if( $this->m_myDBQuery_class==NULL ) return 0;
        $fields_names = NULL;
        for($i=0;$i<$this->fields_count();$i++){
            $finfo = $this->m_myDBQuery_class->field_info($i);
            $fields_names[$i]= $finfo['name'];
        }
        return $fields_names;
    }
    
    function names_fields(){
      //возвращает список имен всех полей (тоже самое что и fields_names())
        return $this->fields_names();
    }
    
    function simple_field_type($n_field){
      //возвращает простое название типа данных поля
      $field_info = $this->field_info($n_field);
      if($field_info==0) return 0;
      $field_type = substr(strtolower(trim($field_info['type'])),0,4);
      switch($field_type){
        case "varc": return "string";   //varchar
        case "blob": return "string";   
        case "char": return "string";   //char,character
        case "nume": return "double";   //numeric(10,2)
        case "int" : return "integer";  
        case "smal": return "integer";  //smallint
        case "time": return "date";     //timestamp
        case "date": return "date";     //date
      }
      return strtolower(trim($field_info['type']));
    }
    
};
//-----------------------------------------------------------------------------
class myDB__base{
 //общий-класс для работы с БД
    var $m_db_driver; //строка - название драйвера бд
    var $m_connect;   //идентификатор подключения к бд
    var $m_last_error; //код и текст последней ошибки
    
    function driver() { return $this->m_db_driver; }
    function connect_resource() {return $this->m_connect;}
    function info(){
        //возвращает информацию о параметрах подключенияс в виде строки
        //переопределяется пользователем
        $str = $this->m_db_driver." open:";
        if($this->m_connect) $str .= 1;
        else                 $str .= 0;
        return $str;
    }
    function m_get_last_error(){
        //переопределяется пользователем
        $this->m_last_error = NULL;
        if(trim($this->m_db_driver)==""){
            return ($this->m_last_error = "myDB__base ERROR: driver not defined(m_db_driver==NULL)");
        }
        return $this->m_last_error;
    }
    function last_error() { return $this->m_last_error; }
    function connect($dataBase,$user,$pass,$options=NULL,$role=NULL){
        //переопределяется пользователем (обязательно задать $this->m_db_driver="твое название")
        $this->m_last_error = NULL;
        $this->m_connect = NULL;
        $this->m_db_driver = NULL;
        return 0;
    }
    function set_connect_resource($connect){ $this->m_connect = $connect; }
    function isOpen() {return (bool)$this->m_connect;}
    function disconnect(){
        //переопределяется пользователем
        $this->m_last_error = NULL;
        if($this->m_connect == 0){
            $this->m_last_error = tr("myDB__base not connected!");
            return 0;
        }
        return 1;
    }
};

class myDBQuery__base{
  //общий класс-шаблон для работы с запросами
    var $m_myDB_class;  //экземпляр класса-наследника myDB__base
    var $m_result;      //query result
    
    var $m_last_error;
    var $m_last_query;
    
    function __construct($p_myDB_class){
        $this->m_myDB_class = $p_myDB_class;
        $this->m_result = NULL;
        $this->m_last_error = NULL;
        $this->m_last_query = NULL;
    }
    
    function m_get_last_error(){
        //переопределяется пользователем
        if( $this->m_myDB_class == NULL ){
            $this->m_last_error = "myDBQuery__base ERROR: no connect (myDB_class==NULL)!";
        }elseif( $this->m_myDB_class->isOpen() == 0 ){
            $this->m_last_error = "myDBQuery__base ERROR: db is not open!";
        }else{
            //тут получаем код ошибки для выбранной бд
            $this->m_last_error = "myDBQuery__base ERROR: undefined error";
        }
        return $this->m_last_error;
    }
    
    function m_check_query_result(){
        //проверка выполнен ли запрос и доступен ли его ресурс
        if( empty($this->m_myDB_class) ){
            $this->m_last_error = "myDBQuery__base ERROR: no connect (myDB_class==NULL)";
            return 0;
        }
        if( $this->m_result == 0 ){
            $this->m_last_error = "myDBQuery__base ERROR: query not executed";
            return 0; 
        }
        return 1; 
    }
    
    function last_error() { return $this->m_last_error; }
    function last_query() { return $this->m_last_query; }

    function exec($str_query,$options){
        //переопределяется пользователем
        $this->m_result = NULL;  //$this->clear();
        $this->m_last_error = NULL;
        $this->m_last_query = NULL;
        if( empty($this->m_myDB_class) ){
            $this->m_get_last_error();
            return 0;
        }
        //тут пользовательский код для выбранной бд
        return 0;
    }
    
    function fetch_row(){
        //переопределяется пользователем
        //загрузка следующей строки запроса,
        //в если строка успешно загружена возврашается массив с данными
        //в противном случае если достигнут конец строк и в случае ошибки возвращается 0
        //переопределяется пользователем
        if( $this->m_check_query_result()==0 ) return 0;
        //тут пользовательский код
        return 0;
    }
    
    function clear(){
        //переопределяется пользователем
        $this->m_last_query = null;
        if( $this->m_check_query_result()==0 ) return 0;
        $this->m_last_error = NULL;
        //тут пользовательский код
        return 0;
    }
    
    function fields_count(){
        //переопределяется пользователем
        if( $this->m_check_query_result()==0 ) return 0;
        //тут пользовательский код
        return 0;
    }
    
    function field_info($n_field){
        //переопределяется пользователем
        //возвращает метаданные поля
        if( $this->m_check_query_result()==0 ) return 0;
        $this->m_last_error = NULL;
        //тут пользовательский код
        return 0;
    }
    
};
//-----------------------------------------------------------------------------
class myDB_ibase extends myDB__base{
 //класс для работы с БД ibase
    var $m_dataBase,
        $m_user,
        $m_pass,
        $m_options,
        $m_role;
    
    function info(){
        //возвращает информацию о параметрах подключенияс в виде строки
        $str = "ibase open:";
        if($this->m_connect) $str .= 1;
        else                 $str .= 0;
        
        $str .= "; db:\"".$this->m_dataBase."\" user:\"".$this->m_user."\"";
        if(strlen($this->m_role)>0)
          $str .=" role:\"".$this->m_role."\"";
        if(strlen($this->m_options)>0)
          $str .=" options:\"".$this->m_options."\"";
        return $str;
    }
    function m_get_last_error(){
        $this->m_last_error = NULL;
        $t = ibase_errcode();
        if( !empty($t) ) $this->m_last_error = "myDB_ibase ERROR: [".$t."] ".tr(ibase_errmsg(),"CP1251");
        return $this->m_last_error;
    }
    function connect($dataBase,$user,$pass,$options=NULL,$role=NULL){
        $this->m_db_driver = "ibase";
        $this->m_last_error = NULL;
        $this->m_connect = 0;
        $this->m_dataBase = $dataBase;
        $this->m_user = $user;
        $this->m_pass = $pass;
        $this->m_options = $options;
        $this->m_role = $role;
        $this->m_connect = ibase_connect($dataBase, $user, $pass, $options,0,3,$role);
        if($this->m_connect == 0) $this->m_get_last_error();
        return $this->m_connect;
    }
    function disconnect(){
        $this->m_last_error = NULL;
        if($this->m_connect == 0){
            $this->m_last_error = tr("myDB_ibase not connected!");
            return 0;
        }
        $t_disconnect = ibase_close($this->m_connect);
        if($t_disconnect) $this->m_connect = NULL;
        else              $this->m_get_last_error();
        return $t_disconnect;
    }
};

class myDBQuery_ibase extends myDBQuery__base{
  //класс для работы с запросами к myDB_ibase
    
    function m_get_last_error(){
        if( $this->m_myDB_class == NULL ){
            return ($this->m_last_error = "myDBQuery_ibase ERROR: no connect (myDB_class==NULL)!");
        }elseif( $this->m_myDB_class->isOpen() == 0 ){
            return ($this->m_last_error = "myDBQuery_ibase ERROR: db is not open!");
        }
        $this->m_last_error = NULL;
        $t = ibase_errcode();
        if( !empty($t) ) $this->m_last_error = "myDBQuery_ibase ERROR [".$t."] ".tr(ibase_errmsg(),"CP1251","IBM866");
        return $this->m_last_error;
    }
    
    function exec($str_query,$options){
        $this->m_result = NULL;  //$this->clear();
        $this->m_last_error = NULL;
        $this->m_last_query = $str_query;
        if( empty($this->m_myDB_class) ){
            $this->m_get_last_error();
            return 0;
        }
        $this->m_result = ibase_query( $this->m_myDB_class->connect_resource(), $str_query);
        if($this->m_result === false){
          $this->m_get_last_error();
          return 0;
        }
        return 1;
    }
    
    function fetch_row(){
        //загрузка следующей строки запроса,
        //в если строка успешно загружена возврашается массив с данными
        //в противном случае если достигнут конец строк и в случае ошибки возвращается 0
        if( $this->m_check_query_result()==0 ) return 0;
        $this->m_last_error = NULL;
        return ibase_fetch_row ($this->m_result);
    }
    
    function clear(){
        $this->m_last_query = null;
        if( $this->m_check_query_result()==0 ) return 0;
        $this->m_last_error = NULL;
        $b = ibase_free_result ($this->m_result);
        if($b == 1) $this->m_result = NULL;
        else        $this->m_get_last_error();
        return $b;
    }
    
    function fields_count(){
        if( $this->m_check_query_result()==0 ) return 0;
        $this->m_last_error = NULL;
        $cnt = ibase_num_fields ($this->m_result);
        return $cnt;
    }
    
    function field_info($n_field){
        //возвращает метаданные поля
        if( $this->m_check_query_result()==0 ) return 0;
        $this->m_last_error = NULL;
        //$field_info['name'] = NULL;
        //$field_info['type'] = NULL;
        //$field_info['size'] = NULL;
        //$field_info['precision'] = NULL;
        $tmp = ibase_field_info($this->m_result,$n_field);
        if($tmp == 0) return 0;
        $field_info['name']      = $tmp['alias']; /*$tmp['name'];*/
        $field_info['type']      = $tmp['type'];
        $field_info['size']      = $tmp['length'];
        $field_info['precision'] = NULL;
        return $field_info;
    }
    
};
//-----------------------------------------------------------------------------
class myDB_mysql extends myDB__base{
  //класс для работы с БД ibase
    var $m_host,
        $m_dataBase,
        $m_user,
        $m_pass,
        $m_options;
        //$m_role;
    
    function info(){
        //возвращает информацию о параметрах подключенияс в виде строки
        $str = "mysql open:";
        if($this->m_connect) $str .= 1;
        else                 $str .= 0;
        
        $str .= "; host:".$this->m_host." db:\"".$this->m_dataBase."\" user:\"".$this->m_user."\"";
        if(strlen($this->m_options)>0)
          $str .=" options:\"".$this->m_options."\"";
        return $str;
    }
    function m_get_last_error(){
        $this->m_last_error = NULL;
        if($this->m_connect){
          $err_n = mysql_errno($this->m_connect);
          $err_msg = mysql_error($this->m_connect);
        }else{
          $err_n = mysql_errno();
          $err_msg = mysql_error();
        }
        if($err_n!=0){
          $this->m_last_error = "myDB_".$this->m_db_driver." ERROR: [{$err_n}] ".tr($err_msg,"CP1251");
        }
        return $this->m_last_error;
    }
    function connect($dataBase,$user,$pass,$options=NULL,$role=NULL){
        //для подключения к бд mysql используем строку host=127.0.0.1;$database; - если хост не ==localhost
        {//разбираем строку $database и задаем переменные m_host и m_dataBase
          $dataBase = trim($dataBase);
          $len = strlen($dataBase);
          $tmp = strtolower($dataBase);
          $pos = strpos($tmp,"host=");
          if($pos===false){
              $this->m_host = "localhost";  //default host
          }else{
              $pos_end = strpos($dataBase,";",$pos+5);
              if($pos_end===false) $pos_end = $len;
              $this->m_host = substr($dataBase,$pos+5,$pos_end-$pos-5);
              $dataBase = substr_replace($dataBase,"",$pos,$pos_end-$pos+1);
              $len = strlen($dataBase);
          }
          if($dataBase[$len-1]==";"){
              $dataBase = substr($dataBase,0,$len-1);
              $len--;
          }
        }
        $this->m_db_driver = "mysql";
        $this->m_last_error = NULL;
        $this->m_connect = 0;
        $this->m_dataBase = $dataBase;
        $this->m_user = $user;
        $this->m_pass = $pass;
        $this->m_options = $options;
        //$this->m_role = $role;
        $this->m_connect = mysql_connect ($this->m_host,$user,$pass,true/*,$options*/ );
        //my_var_dump_html2("m_host1",$this->m_host);
        if($this->m_connect == 0){
            $this->m_get_last_error();
            //my_var_dump_html2("ERROR",$this->m_last_error);
            return 0;
        }
        //my_var_dump_html2("m_dataBase",$this->m_dataBase);
        $b = mysql_select_db ( $this->m_dataBase, $this->m_connect );
        if($b==0) $this->m_get_last_error();
        return $b;
    }
    function disconnect(){
        $this->m_last_error = NULL;
        if($this->m_connect == 0){
            $this->m_last_error = tr("myDB_".$this->m_db_driver." not connected!");
            return 0;
        }
        $t_disconnect = mysql_close($this->m_connect);
        if($t_disconnect) $this->m_connect = NULL;
        else              $this->m_get_last_error();
        return $t_disconnect;
    }
};

class myDBQuery_mysql extends myDBQuery__base{
  //класс для работы с запросами к myDB_ibase
    
    function m_get_last_error(){
        if( $this->m_myDB_class == NULL ){
            return ($this->m_last_error = "myDBQuery_mysql ERROR: no connect (myDB_class==NULL)!");
        }elseif( $this->m_myDB_class->isOpen() == 0 ){
            return ($this->m_last_error = "myDBQuery_mysql ERROR: db is not open!");
        }
        $this->m_last_error = $this->m_myDB_class->m_get_last_error();
        return $this->m_last_error;
    }
    
    function exec($str_query,$options){
        $this->m_result = NULL;  //$this->clear();
        $this->m_last_error = NULL;
        $this->m_last_query = $str_query;
        if( empty($this->m_myDB_class) ){
            $this->m_get_last_error();
            return 0;
        }
        //my_var_dump_html2("this->m_myDB_class->m_dataBase",$this->m_myDB_class->m_dataBase);
        $b = mysql_select_db( $this->m_myDB_class->m_dataBase, $this->m_myDB_class->connect_resource() );
        if($b==0){
            $this->m_get_last_error();
            return 0;
        }
        
        $this->m_result = mysql_query( $str_query, $this->m_myDB_class->connect_resource());
        if($this->m_result == 0){
          $this->m_get_last_error();
          return 0;
        }
        return 1;
    }
    
    function fetch_row(){
        //загрузка следующей строки запроса,
        //в если строка успешно загружена возврашается массив с данными
        //в противном случае если достигнут конец строк и в случае ошибки возвращается 0
        if( $this->m_check_query_result()==0 ) return 0;
        $this->m_last_error = NULL;
        return mysql_fetch_array($this->m_result, MYSQL_NUM);
    }
    
    function clear(){
        $this->m_last_query = null;
        if( $this->m_check_query_result()==0 ) return 0;
        $this->m_last_error = NULL;
        $b = mysql_free_result($this->m_result);
        if($b == 1) $this->m_result = NULL;
        else        $this->m_get_last_error();
        return $b;
    }
    
    function fields_count(){
        if( $this->m_check_query_result()==0 ) return 0;
        $this->m_last_error = NULL;
        $cnt = mysql_num_fields( $this->m_result );
        return $cnt;
    }
    
    function field_info($n_field){
        //возвращает метаданные поля
        if( $this->m_check_query_result()==0 ) return 0;
        $this->m_last_error = NULL;
        //$field_info['name'] = NULL;
        //$field_info['type'] = NULL;
        //$field_info['size'] = NULL;
        //$field_info['precision'] = NULL;
        $field_info['name']      = mysql_field_name($this->m_result, $n_field);
        $field_info['type']      = mysql_field_type($this->m_result, $n_field);
        $field_info['size']      = mysql_field_len ($this->m_result, $n_field);
        $field_info['precision'] = NULL;
        return $field_info;
    }
    
};

//-----------------------------------------------------------------------------
class myDB_oci8 extends myDB__base{
 //класс для работы с БД ibase
    var $m_dataBase,
        $m_user,
        $m_pass,
        $m_options;
    
    function info(){
        //возвращает информацию о параметрах подключенияс в виде строки
        $str = "oci8 open:";
        if($this->m_connect) $str .= 1;
        else                 $str .= 0;
        
        $str .= "; db:\"".$this->m_dataBase."\" user:\"".$this->m_user."\"";
        if(strlen($this->m_options)>0)
          $str .=" character_set:\"".$this->m_options."\"";
        return $str;
    }
    function m_get_last_error(){
        $this->m_last_error = NULL;

        $t = NULL;
        if($this->m_connect == 0)  $t = oci_error();
        else                       $t = oci_error($this->m_connect);
        if( !empty($t) ) $this->m_last_error = "myDB_oci8 ERROR [".$t['code']."] ".tr($t['message'],"CP1251");
        
        return $this->m_last_error;
    }
    function connect($dataBase,$user,$pass,$opt_character_set=NULL,$role=NULL){
        //
        $this->m_db_driver = "oci8";
        $this->m_last_error = NULL;
        $this->m_connect = 0;
        $this->m_dataBase = $dataBase;
        $this->m_user = $user;
        $this->m_pass = $pass;
        $this->m_options = $opt_character_set;
        
        $this->m_connect = oci_connect($user, $pass, $dataBase, $opt_character_set);
        if($this->m_connect == 0) $this->m_get_last_error();
        
        return $this->m_connect;
    }
    function disconnect(){
        $this->m_last_error = NULL;
        if($this->m_connect == 0){
            $this->m_last_error = tr("myDB_oci8 not connected!");
            return 0;
        }
        $t_disconnect = oci_close($this->m_connect);
        if($t_disconnect) $this->m_connect = NULL;
        else              $this->m_get_last_error();
        return $t_disconnect;
    }
};

class myDBQuery_oci8 extends myDBQuery__base{
  //класс для работы с запросами к myDB_ibase
    
    function m_get_last_error(){
        if( $this->m_myDB_class == NULL ){
            return ($this->m_last_error = "myDBQuery_oci8 ERROR: no connect (myDB_class==NULL)!");
        }elseif( $this->m_myDB_class->isOpen() == 0 ){
            return ($this->m_last_error = "myDBQuery_oci8 ERROR: db is not open!");
        }
        $this->m_last_error = NULL;
        $t = oci_error($this->m_result);
        if( !empty($t) )  $this->m_last_error = "myDBQuery_oci8 ERROR [".$t['code']."] ".tr($t['message'],"CP1251");
        return $this->m_last_error;
    }
    
    function exec($str_query,$options){
        $this->m_result = NULL;  //$this->clear();
        $this->m_last_error = NULL;
        $this->m_last_query = $str_query;
        if( empty($this->m_myDB_class) ){
            $this->m_get_last_error();
            return 0;
        }
        $this->m_result = oci_parse( $this->m_myDB_class->connect_resource(), $str_query);
        if($this->m_result == 0){
            $this->m_get_last_error();
            return 0;
        }
        $b = @oci_execute($this->m_result, OCI_DEFAULT);
        if($b == 0){
            $this->m_get_last_error();
            return 0;
        }
        return 1;
    }
    
    function fetch_row(){
        //загрузка следующей строки запроса,
        //в если строка успешно загружена возврашается массив с данными
        //в противном случае если достигнут конец строк и в случае ошибки возвращается 0
        if( $this->m_check_query_result()==0 ) return 0;
        $this->m_last_error = NULL;
        return oci_fetch_row($this->m_result);
    }
    
    function clear(){
        $this->m_last_query = null;
        if( $this->m_check_query_result()==0 ) return 0;
        $this->m_last_error = NULL;
        $b = oci_free_statement($this->m_result);
        if($b == 1) $this->m_result = NULL;
        else        $this->m_get_last_error();
        return $b;
    }
    
    function fields_count(){
        if( $this->m_check_query_result()==0 ) return 0;
        $this->m_last_error = NULL;
        $cnt = oci_num_fields($this->m_result);
        return $cnt;
    }
    
    function field_info($n_field){
        //возвращает метаданные поля
        if( $this->m_check_query_result()==0 ) return 0;
        $this->m_last_error = NULL;
        //$field_info['name'] = NULL;
        //$field_info['type'] = NULL;
        //$field_info['size'] = NULL;
        //$field_info['precision'] = NULL;
        $field_info = array();
        $n_field++;
        $field_info['name']      = oci_field_name($this->m_result, $n_field);
        $field_info['type']      = oci_field_type($this->m_result, $n_field);
        $field_info['size']      = oci_field_size($this->m_result, $n_field);
        $field_info['precision'] = oci_field_precision($this->m_result, $n_field);
        return $field_info;
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


