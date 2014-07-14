<?php
//Marciv M.N.(c) mail:mixamarciv@ya.ru
//include_once("strFunc.php");
//-----------------------------------------------------------------------------
//ini_set('display_errors',1);
//error_reporting(E_ALL);
//-----------------------------------------------------------------------------
//function tr( $str, $from="CP1251", $to="IBM866")
function tr( $str, $from="UTF-8", $to="UTF-8"){
  //транслейт )), чтоб тока в одном месте можно было задать с какой кодировкой бороться будем
  if($from==$to) return $str;
  //"UTF-8"
  //"CP1251"
  //"IBM866"
  //"koi8-r"
  //"Latin1"
  //"ASCII"
  //"ISO-8859-1"

  //модификаторы //TRANSLIT //IGNORE   (напр "ASCII//IGNORE" или "CP1251//TRANSLIT//IGNORE")
  $str0 = $str;
  //convert_cyr_string ( $str,  );
  $ret_str = iconv( $from, $to."//IGNORE", $str);
  if($ret_str == FALSE)
  {
    //echo "tr - FALSE\n";
    $ret_str = $str0;
  }
  
  //con
  return /*"[$from->$to]".*/$ret_str;
}
//-----------------------------------------------------------------------------
function rus_to_translit($string){
  $converter = array(
    'а' => 'a',  'б' => 'b',  'в' => 'v',   'г' => 'g',  'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'zh',
    'з' => 'z',  'и' => 'i',  'й' => 'y',   'к' => 'k',  'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
    'п' => 'p',  'р' => 'r',  'с' => 's',   'т' => 't',  'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
    'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ь' => '\'', 'ы' => 'y', 'ъ' => '',  'э' => 'e', 'ю' => 'yu',
    'я' => 'ya',
    'А' => 'A',  'Б' => 'B',  'В' => 'V',   'Г' => 'G',  'Д' => 'D', 'Е' => 'E', 'Ё' => 'E', 'Ж' => 'ZH',
    'З' => 'Z',  'И' => 'I',  'Й' => 'Y',   'К' => 'K',  'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
    'П' => 'P',  'Р' => 'R',  'С' => 'S',   'Т' => 'T',  'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
    'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SCH', 'Ь' => '\'', 'Ы' => 'Y', 'Ъ' => '',  'Э' => 'E', 'Ю' => 'YU',
    'Я' => 'YA',
  );

  return strtr($string, $converter);
}
//-----------------------------------------------------------------------------
function my_microtime($show_ms=1){
  $l_microtime=microtime(1);
  if($show_ms)  $l_microtime+=microtime(0)/1000;
  return $l_microtime;
}
//-----------------------------------------------------------------------------
function my_microtime_to_str($my_microtime){
  $ms  = ($my_microtime*100)%100;
  $sec = round($my_microtime);
  if($sec>60)
  {
    $min = round($sec/60);
    $sec = $sec%60;
    if($min>60)
    {
      $hour = round($min/60);
      $min  = $min%60;
      return sprintf("%d:%02d:%02d",$hour,$min,$sec);
    }
    return sprintf("%2d:%02d",$min,$sec);
  }
  return sprintf("%d.%02d",$sec,$ms);
}
//-----------------------------------------------------------------------------
function my_datetime_to_str($p_date=0,$p_microtime=0){
  if($p_date     ==0) $p_date=time();
  if($p_microtime==0) $p_microtime=microtime(1);
  
//date_to_str
/****
$today = date("F j, Y, g:i a");                 // March 10, 2001, 5:16 pm
$today = date("m.d.y");                         // 03.10.01
$today = date("j, n, Y");                       // 10, 3, 2001
$today = date("Ymd");                           // 20010310
$today = date('h-i-s, j-m-y, it is w Day z ');  // 05-16-17, 10-03-01, 1631 1618 6 Fripm01
$today = date('\i\t \i\s \t\h\e jS \d\a\y.');   // It is the 10th day.
$today = date("D M j G:i:s T Y");               // Sat Mar 10 15:16:08 MST 2001
$today = date('H:m:s \m \i\s\ \m\o\n\t\h');     // 17:03:17 m is month
$today = date("H:i:s");                         // 17:16:17
***/
  $p_microtime = $p_microtime%1000;
  $p_microtime_len = strlen($p_microtime);
  if($p_microtime_len==1) $p_microtime.="00";
  else if($p_microtime_len==2) $p_microtime.="0";
  
  return date("Y.m.d_H-i-s.",$p_date).$p_microtime;
}
//-----------------------------------------------------------------------------
function my_date_to_str($p_date=0){
  if($p_date     ==0) $p_date=time();
  return date("Y.m.d",$p_date);
}
//-----------------------------------------------------------------------------
function get_normal_path($str){
    $str  = str_replace("\\","/",$str);
    $str  = str_replace("\"","",$str);
    if($str[strlen($str)-1]!="/")  $str.="/";
    return $str;
}
//-----------------------------------------------------------------------------
function my_trim(&$str){
  //ыыы, не софсем велосипед
    $str = trim($str);
    $len = strlen($str);
    if($len==0) return $str;
    //print("len==$len\n");
    $last_pos = $len - 1;
    while(
        ( $str[0]==$str[$last_pos] && ( $str[0] == "'" || $str[0] == "\"" ) ) ||
        ( $str[0] == "(" && $str[$last_pos] == ")" ) ||
        ( $str[0] == "[" && $str[$last_pos] == "]" ) ||
        ( $str[0] == "<" && $str[$last_pos] == ">" ) ||
        ( $str[0] == "{" && $str[$last_pos] == "}" )
      ){
        $str[0] = ' ';
        $str[$last_pos] = ' ';
        $str = trim($str);
        
        $len = strlen($str);
        $last_pos = $len - 1;
        if($len==0) return $str;
    }
    return $str;
}
//-----------------------------------------------------------------------------
function my_find_end_br($str,$br_begin,$br_end,$from_pos,$to_pos=-1){
  //фукция поиска закрывающей скобки
    $len = strlen($str);
    if($len==0) return FALSE;
    if($len < ($from_pos+$br_end_len)  ||
       $to_pos < $from_pos ) return FALSE;
    if($to_pos > $len) $to_pos = $len;
    
    while( 1 )
    {
        $pos_end = strpos($str,$br_end,$from_pos);
        $pos_begin = strpos($str,$br_begin,$from_pos);
        if( $pos_end != -1 && $pos_end > $to_pos ) return FALSE;
        if( $pos_begin == FALSE || $pos_begin > $pos_end ) return $pos_end;
        if( $pos_end == FALSE ) return FALSE;
        $from_pos = $pos_end + 1; 
    }
    return $pos_end;
}
//-----------------------------------------------------------------------------
function php_var_to_js_var($var,$no_newLine=0,$level=0){
//функция переводит массив заданных переменных $var в javascript набор данных
//например $var['item1']=100; $var['item2']="test";
//для js будет { item1 : 100, item2: 'test' }
 
  $str = "";
  $space = "    ";
  if($level>0){
    $space_need = $space;
    for($i=0;$i<$level;$i++){
      $space_need .= $space;
    }
    $space = $space_need;
  }

  if(is_array($var)){
    $i = 0;
    foreach($var as $k=>$v){
      if($i++>0) $str .= ",";
      $str .= "\n".$space;
      if(is_array($v)){
        $str .= "\"$k\" : {".php_var_to_js_var($v,$no_newLine,$level+1)."}";
      }else{
        $val = php_value_to_js_value($v,$no_newLine);
        $str .= "\"$k\" : {$val} ";
      }
    }
  }else{
    $str .= php_value_to_js_value($var,$no_newLine);
  }
  return $str;
}
function php_value_to_js_value($value,$no_newLine=0){
  $val = $value;
  if($val=="" && strlen($val)==0){
    $val = "null";
  }else if(ctype_digit($val)){
    if($val[0]=="0") $val = "\"{$val}\"";
    else $val = $val;
  }elseif(is_string($val) && (substr(trim($val),0,8)=="function" ||
                              substr(trim($val),0,1)=="{" ||
                              substr(trim($val),strlen(trim($val))-2,2)=="()"
                              )
         ){
    $val = $val;
  }else if(is_string($val)){
    if($no_newLine==0){
      $val = str_replace("\r\n","\n",$val);
      $val = str_replace("\r","\n",$val);
      $val = str_replace("\n","\\\n\\n",$val);
    }else{
      $val = str_replace("\r\n","\n",$val);
      $val = str_replace("\r","\n",$val);
      $val = str_replace("\n"," ",$val);
    }
    $val = str_replace("\"","\\\"",$val);
    $val = "\"{$val}\"";
  }
  return $val;
}
//-----------------------------------------------------------------------------
function replace_vars_to_value_in_str($vars,$str){
  //подстановка переменных на их значения в тексте
  foreach($vars as $key => $value){
    $str = str_replace($key,$value,$str);
  }
  return $str;
}
//-----------------------------------------------------------------------------
function replace_vars_to_value_in_vars_str($vars,$vars_str){
  //подстановка переменных на их значения в значениях переменных $vars_str
  foreach($vars_str as $key => $value){
    $vars_str[$key] = replace_vars_to_value_in_str($vars,$value);
  }
  return $vars_str;
}
//-----------------------------------------------------------------------------
function vars_to_user_vars($vars){
  //перевод обычных переменных в переменные для подстановки их значений в тексте
  $user_vars = array();
  if(is_array($vars) && count($vars)>0)
    foreach($vars as $key => $value){
      if(strrpos($key,"##[")===0 && strrpos($key,"]##")===strlen($key)-1-3) continue;
      $user_var = "##[".$key."]##";
      $user_vars[$user_var] = $value;
    }
  return $user_vars;
}
//-----------------------------------------------------------------------------
function my_var_dump_html($var_name,$var){
  echo "<H3>{$var_name}</H3><pre>";
  var_dump($var);
  echo "</pre>";
}
function my_var_dump_html2($var_name,$var,$parent_name="",$level=0,$sort=1,$max_level=9999){
  if($level==0){
    if($parent_name=="") $parent_name = $var_name;
    echo "<pre style=\"border:1px dashed #aaa;padding:1px;margin:1px;text-align: left;\">";
  }
  if(is_array($var)){
    if($level<$max_level){
      if($sort) ksort($var);
      foreach($var as $key=>$val){
        my_var_dump_html2($key,$val,$parent_name."['$key']",$level+1);
      }
    }else{
      echo "{$parent_name} = array(...(size:".sizeof($var)."))\n";
    }
  }elseif(is_object($var)){
    echo "{$parent_name} = object(size:".sizeof($var).")\n";
  }else{
    //if(is_double($var) || is_numeric($var) || is_bool($var))
    //  echo "{$parent_name} = {$var}\n";
    //else
      echo "{$parent_name} = \"".htmlspecialchars($var)."\"\n";
  }
  if($level==0){
    echo "</pre>";
  }
}
function my_var_dump_html2_str($var_name,$var,$parent_name="",$level=0,$sort=1,$max_level=9999){
  ob_start();
  my_var_dump_html2($var_name,$var,$parent_name,$level,$sort,$max_level);
  $str = ob_get_contents();
  ob_end_clean();
  return $str;
}
//-----------------------------------------------------------------------------
//добавляем слеши тока к определенному символу(символам)
function my_addslashes_symbol($str,$simbol_or_arr){
  $str = str_replace("\\","\\\\",$str);
  if(is_array($simbol_or_arr)){
    foreach($simbol_or_arr as $i=>$simbol){
      $str = str_replace($simbol,"\\".$simbol,$str);
    }
  }else{
    $simbol = $simbol_or_arr;
    $str = str_replace($simbol,"\\".$simbol,$str);
  }
  return $str;
}
//-----------------------------------------------------------------------------
function my_substr_delete($str,$substr_from,$substr_to){
  //удаляем подстроку с $substr_from по $substr_to(обе включительно) в строке $str
  $pos_begin = strpos($str,$substr_from);
  if($pos==false) return $str;
  $len_from = strlen($substr_from);
  $len_to   = strlen($substr_to);
  while($pos_begin!==false){
    $pos_end = strpos($str,$substr_to,$pos_begin+$len_from);
    if($pos_end===false){
      $str = substr($str,0,$pos_begin);
      return $str;
    }
    $str = substr($str,0,$pos_begin).substr($str,$pos_end+$len_to);
    $pos_begin = strpos($str,$substr_from,$pos_begin);
  }
  return $str;
}
//-----------------------------------------------------------------------------
function my_substr_delete_comments($str){
  //удаляем комментарии типа /* и */ , // и \n 
  $str = my_substr_delete($str,"/*","*/");
  $str = my_substr_delete($str,"//","\n");
  return $str;
}
//-----------------------------------------------------------------------------
function my_get_url_to_server_path($dir_path){
  //получаем $url путь к текущему каталогу из реального-физического пути(D:\hosts\site1.ru\www\index.php)
  $this_host = $_SERVER['SERVER_NAME'];           // название сайта
  if(trim($_SERVER['SERVER_PORT'])!="80") $this_host.=":".$_SERVER['SERVER_PORT'];
  $this_path     = $_SERVER['SCRIPT_FILENAME'];   // D:/_webserver/vhosts/site9.test/www/index.php
  $this_url_path = $_SERVER['PHP_SELF'];          // /index.php
  
  $this_path = str_replace("\\","/",$_SERVER['SCRIPT_FILENAME']);
  $dir_path = str_replace("\\","/",$dir_path);
  
  if(substr(strtolower(PHP_OS),0,3)=="win"){
    $this_path = strtolower($this_path);
    $dir_path = strtolower($dir_path);
    $this_url_path = strtolower($this_url_path);
  }
  
  if($this_path[strlen($this_path)-1]!="/") $this_path .= "/";
  if($dir_path [strlen($dir_path) -1]!="/") $dir_path  .= "/";
  
  
  if($this_url_path[strlen($this_url_path)-1]!="/") $this_url_path = dirname($this_url_path)."/";
  
  if(strrpos($this_path,$this_url_path)==strlen($this_path)-strlen($this_url_path)){
    $this_path = substr($this_path,0,strlen($this_path)-strlen($this_url_path));
    $need_path = substr($dir_path,strlen($this_path));
    return $need_path;
  }else{
    my_var_dump_html2("AAAAAA",array("this_path"=>$this_path,"this_url_path"=>$this_url_path));
  }
  
  my_var_dump_html2("PHP_OS",PHP_OS);
  my_var_dump_html2("this_host",$this_host);
  my_var_dump_html2("server",$this_host);
  return "ERROR";
}
//-----------------------------------------------------------------------------
//2 функции для упаковки и распаковки массива данных
function request_data_compress($data){
    $str = serialize($data);
    $str = gzcompress($str,9);
    $str = base64_encode($str);
    //$str = convert_uuencode($str);
    $str = divide_str($str,122);
    return $str;
}

function divide_str($str,$str_block_size=200){
    //разделяем строку на массив строк размером по $str_block_size символов
    $len = strlen($str);
    $cnt = ceil($len/$str_block_size);
    $arr_str = array();
    for($i=0;$i<$cnt;$i++){
        $arr_str[$i] = substr($str,$i*$str_block_size,$str_block_size);
    }
    $arr_str[count($arr_str)] = "$cnt | $len";
    return $arr_str;
}

function request_data_uncompress($data){
    $str = implode($data);
    $str = base64_decode($str);
    //$str = convert_udecode($str);
    $str = gzuncompress($str);
    $str = unserialize($str);
    return $str;
}
//-----------------------------------------------------------------------------
function urlencode_array($var,$main_var=""){
  if(!is_array($var)) return urlencode($var);
  $toImplode = array();
  foreach($var as $key => $value){
      if(is_array($value)){
          if($main_var=="") $toImplode[] = urlencode_array($value, "{$key}");
          else              $toImplode[] = urlencode_array($value, "{$main_var}[{$key}]");
      }else{
          if($main_var=="") $toImplode[] = "{$key}=".urlencode($value);
          else              $toImplode[] = "{$main_var}[{$key}]=".urlencode($value);
      }
  }
  return implode("&", $toImplode);
}
//-----------------------------------------------------------------------------
$mx_str_format_space = "                           ";
function mx_str_format($var,$need_length,$align='left'){
  $s = (string)$var;
  $len = strlen($s);
  if($len==$need_length) return $s;
  if($len>$need_length){
    $s = substr($s,0,$need_length);
    return $s;
  }

  {
    global $mx_str_format_space;
    if($align=='left'){
      $s .= $mx_str_format_space;
      while(($len=strlen($s))<$need_length) $s .= $mx_str_format_space;
      $s = substr($s,0,$need_length);
      return $s;
    }
    $s = $mx_str_format_space.$s;
    while(($len=strlen($s))<$need_length) $s = $mx_str_format_space.$s;
    $s = substr($s,$len-$need_length,$need_length);
    return $s;
  }
}
//-----------------------------------------------------------------------------
