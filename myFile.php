<?php
//include_once("strFunc.php");
//-----------------------------------------------------------------------------
function my_writeToFile($file, $mode, $str){
  if(!file_exists(dirname($file))) mkdir(dirname($file),077,1);
  $f = fopen($file,$mode);
  fwrite($f,$str);
  fclose($f);
}
//-----------------------------------------------------------------------------
function my_readFile($file,$from=0,$bytes=5000000 /*1mb*/) 
{
  $f = fopen($file,"rb");
  fseek($f,$from);
  $str = fread($f,$bytes);
  fclose($f);
  return $str;
}
//-----------------------------------------------------------------------------
