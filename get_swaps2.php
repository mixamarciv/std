<?php
/*******************************************
 * get_swaps2 - генерит все возможные комбинации из указанного набора 
 *              символов (второй параметр $text) и сохраняет их в файл *.out
 *              
 *******************************************/

$text = "’€‚ЋЌЉђ";//"123456";
$len  = strlen($text); 
$fout = fopen(__FILE__".out","wb");

function swap($text,$i,$j){
    $tt = $text[$i];
    $text[$i] = $text[$j];
    $text[$j] = $tt;
    return $text;
}

function insert_smb($text,$smb,$i){
    $text = substr_replace($text, $smb, $i,0);
    return $text;
}

function remove_smb($text,$i){
    $text = substr_replace($text, "", $i,1);
    return $text;
}

function get_info_text($info){
  $str = "";
  foreach($info as $k=>$v){
    $i = $v['i'];
    $j = $v['j'];
    $smb = $v['smb'];
    $str .= "  [$k]$i|$j'{$smb}'";
  }
  return "";
  return $str;
}

function get_swaps2($start_text,$text,$end_text,$level_need=0,$level=0,$k=0,$info=array()){
    global $fout;
    $len = strlen($text);

    echo ".";
    
    if($level_need==0){
        $level_need = $len - 2;
    }
    if($len<2){
        fwrite($fout,$start_text.$text."!!ERROR!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"."\n");
        echo "!!ERROR!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
        return;
    }
    if($len==2){
        if($level_need>$level){
            return;
        }
        $text1 = $start_text.$text.$end_text;
        $text2 = $start_text.swap($text,0,1).$end_text;
        fwrite($fout,$text1.get_info_text($info)."\n");
        fwrite($fout,$text2.get_info_text($info)."\n");
        return;
    }
    if($len>2){
        $len2 = $len-1;
        //echo "\n$start_text $text";
        for($i=0;$i<$len;$i++){
            $smb = $text[$i];
            $text2 = remove_smb($text,$i);
            
            for($j=0;$j<$len2-1;$j++){
                $text3 = insert_smb($text2,$smb,$j);
                $text4 = substr($text2,$j,$len2-$j);
                $text4_begin = substr($text3,0,$j+1);
                //echo "\n--[level=$level|i=$i|j=$j|smb=$smb]-- ".$start_text.$text4_begin." ".$text4;
                $info[$level]['i']=$i;
                $info[$level]['j']=$j;
                $info[$level]['smb']=$smb;
                get_swaps2($start_text.$text4_begin,$text4,$end_text,$level_need,$level+1,$i,$info);
            }
        }
        return;
    }
}



get_swaps2("",$text,"");



fclose($fout);
