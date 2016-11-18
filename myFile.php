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
function my_readFile($file,$from=0,$bytes=5000000){
  $f = fopen($file,"rb");
  fseek($f,$from);
  $str = fread($f,$bytes);
  fclose($f);
  return $str;
}
//-----------------------------------------------------------------------------
function mkdir_r($dirName){
    $dirs = explode('/', $dirName);
    $dir='';
    foreach ($dirs as $part) {
        $dir.=$part.'/';
        if (!is_dir($dir) && strlen($dir)>0)
            mkdir($dir);
    }
}
