<?php
//include_once("strFunc.php");

//-----------------------------------------------------------------------------
function include_html($filePath,$fileURL="",$file_type="html",$include_type="html"){
//подключаем к документу внешний файл в зависимости от $include_type
// $include_type="html" - в теле документа
// $include_type="url"  - как ссылку на внешний файл в теле этого документа
// и $file_type
// $file_type="html"
// $file_type="stylesheet" | "text/css" | "css"
// $file_type="text/javascript" | "javascript"
  if(trim($fileURL)=="") $include_type="html";
  $separator = "==================================================";
  echo "\n<!-- {$separator} BEGIN INCLUDE({$file_type} AS {$include_type}): \"{$filePath}\" {$separator} -->\n";
  
  if($file_type=="html"){
    if($include_type=="url") echo "<!-- INCLUDE WARNING: incompatible file_type($file_type) & include_type($include_type) -->\n";
    include($filePath);
  }elseif($file_type=="stylesheet" || $file_type=="text/css" || $file_type=="css"){
    if($include_type=="url"){
        echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$fileURL}\">";
    }else{
        echo "<style>\n";
        include($filePath);
        echo "\n</style>";
    }
  }elseif($file_type=="text/javascript" || $file_type=="javascript"){
    if($include_type=="url"){
        echo "<script type=\"text/javascript\" src=\"{$fileURL}\"></script>";
    }else{
        echo "<script type=\"text/javascript\">\n";
        include($filePath);
        echo "\n</script>";
    }
  }
  
  echo "\n<!-- {$separator}  END  INCLUDE({$file_type} AS {$include_type}): \"{$filePath}\" {$separator} -->\n";
}
//-----------------------------------------------------------------------------
function include_once_dir($includePath){
    //включает все файлы из указанной директории и поддиректорий
    if(is_dir($includePath)){
        if($dh = opendir($includePath)){
            while(($file = readdir($dh)) !== false){
                if($file=="." || $file=="..") continue;
                if(is_dir($includePath."/".$file)){
                    include_once_dir($includePath."/".$file);
                    continue;
                }
                
                $pos = strrpos($file,".");
                if($pos===false) continue;
                $extension = strtolower(substr($file,$pos,strlen($file)-$pos));
                
                if($extension==".php" || $extension==".inc"){
                    //echo "<br>".$includePath."/".$file;
                    include_once($includePath."/".$file);
                }
            }
            closedir($dh);
        }
    }
}
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
