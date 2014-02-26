<?php
//Marciv M.N.(c) mail:mixamarciv@ya.ru
require_once(dirname(__FILE__)."/strFunc.php");
//-----------------------------------------------------------------------------
define("VAR_BEGIN", "##[");
define("VAR_END", "]##");
define("VAR_DELIM_LEN", strlen(VAR_BEGIN) );

class paramParser
{ //paramParser разбирает параметры типа "[var1] value1 [var2] value2 value3 value4 value5"
  //и далее можно с ними работать как с обычным ассоциативным массивом

    var $m_vars = array();       //параметры переменные, типа "[var1] value1 [var2] value2"
    
    var $m_params = array();     //параметры которые не являются переменными
    
    var $m_all_params_count = 0;  //всего параметров неучитывая значения переменных
    
    function init()
    {//задание основных переменных из параметров запуска скрипта
        $argc = $GLOBALS["argc"];
        $argv = $GLOBALS["argv"];

        $this->m_vars = array();
        $this->m_params = array();
        $this->m_all_params_count = 0;

       for($i=0;$i<$argc;$i++)
       {
          $s = $argv[$i];
          $len = strlen($s);
          if($s[0]=='[' && $s[$len-1]==']')
          {
            $s = substr($s,1,$len-2);
            $i++;
            $v = $argv[$i];
            $this->m_vars[strtolower($s)] = $v;
          }else{
            $this->m_params[$this->m_all_params_count] = $s;
          }
          $this->m_all_params_count++;
       }
       //echo show_vars();
    }
    
    function fvars()
    {//возвращает cписок всех переменных
        return $this->m_vars;
    }
    
    function str_all_vars()
    {//возвращает строку со списком всех переменных и их значений
        $str = "";
        foreach($this->m_vars as $key => $value)
        {
            $str .= "[$key] == \"$value\"\n";
        }
        return $str;
    }
    
    function fvar($var)
    {//возвращает значение переменной $var
        $var = strtolower($var);
        
        if($var == "all_vars")
            return $this->str_all_vars();
            
        if(array_key_exists($var,$this->m_vars)==FALSE)
            return NULL;
        
        return $this->m_vars[$var];
    }
    
    function fparams()
    {//возвращает параметры запуска скрипта которые не относятся к переменным
        return $this->m_params;
    }
    function fparam ($int_n_param)
    {//возвращает int_n_param параметр по порядку
        return $this->m_params[$int_n_param];
    }
    
    function set_var($var,$var_value)
    {//задаем значение переменной
        $var = strtolower($var);
        $this->m_vars[$var] = $var_value;
    }
    
    function show_all()
    {//вывод списка переменных +параметров
        $str = tr("всего параметров ".$this->m_all_params_count."\n");
        $str .= tr("--список переменных ").count($this->m_vars).":\n";
        //$str .= tr("список переменных ").count($this->m_vars).":\n";
        foreach($this->m_vars as $key => $value)
        {
            $str .= "[".$key."]=\"".$value."\"\n";
        }
        $str .= tr("список остальных параметров ").count($this->m_params).":\n";
        foreach($this->m_params as $key => $value)
        {
            $str .= "[".$key."]=\"".$value."\"\n";
        }
        return $str;
    }
    
    static function replace_vars_value(&$buffer,&$user_vars){
      //функция заменяет переменные на их значения
      //переменные вида ##[var_name]##
        $i_var_begin = strpos($buffer,VAR_BEGIN);
        $temp_vars = array();
        if(is_array($user_vars))
          foreach($user_vars as $k=>$v){
            $temp_vars[strtolower($k)] = $v;
            if(substr($k,0,VAR_DELIM_LEN)==VAR_BEGIN){
              $k = trim($k);
              if(substr($k,strlen($k)-VAR_DELIM_LEN,VAR_DELIM_LEN)==VAR_END){
                $tmp_var = substr($k,VAR_DELIM_LEN,strlen($k)-VAR_DELIM_LEN*2);
                $tmp_var = strtolower($tmp_var);
                $temp_vars[$tmp_var] = $v;
              }
            }
          }
        //my_var_dump_html2("temp_vars",$temp_vars);
        while($i_var_begin!==FALSE){
            $i_var_end = strpos($buffer,VAR_END,$i_var_begin);
            if($i_var_end==FALSE){
                echo tr("ошибка обработки переменных, окончание задания переменной(\"VAR_END\") ненайдено!");
                return 0;
            }
            
            $var = strtolower(trim(substr($buffer,$i_var_begin+3,$i_var_end-$i_var_begin-VAR_DELIM_LEN)));
            
            $value = "";
            //my_var_dump_html2("var!0!",$var);
            if(array_key_exists($var,$temp_vars)==TRUE){
              $value = $temp_vars[$var];
              //my_var_dump_html2("var!1!",$var);
            }else{
              //my_var_dump_html2("var!2!",$var);
              if(isset($this)){
                $value = $this->fvar($var);
              }
            }
            
            //echo "<br>replace_vars_value: VAL[$var] = ".$value;
            $buffer = substr_replace($buffer,$value,$i_var_begin,$i_var_end+VAR_DELIM_LEN-$i_var_begin);
            
            $var_begin_str = VAR_BEGIN;
            $var_begin_search_pos = $i_var_begin-1;
            if($var_begin_search_pos == -1)
                $var_begin_search_pos = 0;
                
            $i_var_begin = strpos($buffer,$var_begin_str,$var_begin_search_pos);
        }
        //echo "\n==================-----------------------=========================\n";
        //echo $buffer;
        //echo "\n==================-----------------------=========================\n";
        return $buffer;
    }
    
};
//-----------------------------------------------------------------------------
function get_fnc_params($fnc)
{
  ; //
}
//-----------------------------------------------------------------------------
//$params = new paramParser();  //создаем один такой обьект и далее тока его и используем! 
//$params->init(); 
//-----------------------------------------------------------------------------
?>