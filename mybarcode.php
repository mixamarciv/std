<?php
//Marciv M.N.(c) mail:mixamarciv@ya.ru
//include_once("mybarcode.php");
//-----------------------------------------------------------------------------
function mybarcode_code128($str){
  //генерим символы для баркода из указанной строки
  $str = (string)$str;  //эта строка обязательна! если в строке нет никаких символов кроме чисел
  $BCode = array();
  $CurMode = "";
  $str_len = strlen($str);
  $BInd = 0;
  
  for($i=0;$i<$str_len;){
      //my_var_dump_html2("$i mode $CurMode",array(implode(";",$BCode),ord($str[$i]).";".ord($str[$i+1])));
      /********************************
      'Текущий символ в строке
      Ch = Asc(Mid(A, I, 1)) //возвращает код символа
      I = I + 1
      'Разбираем только символы от 0 до 127
      If Ch <= 127 Then
          'Следующий символ
          If I <= LenA Then
              Ch2 = Asc(Mid(A, I, 1))
          Else
              Ch2 = 0
          End If
          'Пара цифр - режим С
          If (48 <= Ch) And (Ch <= 57) And _
             (48 <= Ch2) And (Ch2 <= 57) Then
              I = I + 1
      ********************************/
      $i_smb = ord($str[$i]);
      $i++;
      if($i_smb>127) continue; //символы с кодом больше 127 игнорируются
      $i_smb2 = 0;
      if($i<$str_len) $i_smb2 = ord($str[$i]);
      if($i_smb>=48 && $i_smb<=57 && $i_smb2>=48 && $i_smb2<=57){
          $i++;
          /***************
                If BInd = 0 Then
                    'Начало с режима С
                    CurMode = "C"
                    BCode(BInd) = 105
                    BInd = BInd + 1
                ElseIf CurMode <> "C" Then
                    'Переключиться на режим С
                    CurMode = "C"
                    BCode(BInd) = 99
                    BInd = BInd + 1
                End If
          ****************/
          if($BInd==0){
              $BCode[$BInd++] = 105;
          }elseif($CurMode != "C"){
              $BCode[$BInd++] = 99;
          }
          $CurMode = "C";
          /**************
                'Добавить символ режима С
                BCode(BInd) = CInt(Chr(Ch) & Chr(Ch2))
                BInd = BInd + 1
          ***************/
          $BCode[$BInd++] = intval(chr($i_smb).chr($i_smb2));
      }else{
            /*************
            If BInd = 0 Then
                If Ch < 32 Then
                    'Начало с режима A
                    CurMode = "A"
                    BCode(BInd) = 103
                    BInd = BInd + 1
                Else
                    'Начало с режима B
                    CurMode = "B"
                    BCode(BInd) = 104
                    BInd = BInd + 1
                End If
            End If
            *************/
            if($BInd==0){
                if($i_smb<32){
                  //'Начало с режима A
                  $CurMode = "A";
                  $BCode[$BInd++] = 103;
                }else{
                  //'Начало с режима B
                  $CurMode = "B";
                  $BCode[$BInd++] = 104;
                }
            }
            /**************
            'Переключение по надобности в режим A
            If (Ch < 32) And (CurMode <> "A") Then
                CurMode = "A"
                BCode(BInd) = 101
                BInd = BInd + 1
            'Переключение по надобности в режим B
            ElseIf ((64 <= Ch) And (CurMode <> "B")) Or (CurMode = "C") Then
                CurMode = "B"
                BCode(BInd) = 100
                BInd = BInd + 1
            End If
            **************/
            if($i_smb<32 && $CurMode!="A"){
                  $CurMode = "A";
                  $BCode[$BInd++] = 101;
            }elseif(($i_smb>=64 && $CurMode!="B") || $CurMode=="C"){
                  $CurMode = "B";
                  $BCode[$BInd++] = 100;
            }
            /***********
            'Служебные символы
            If (Ch < 32) Then
                BCode(BInd) = Ch + 64
                BInd = BInd + 1
            'Все другие символы
            Else
                BCode(BInd) = Ch - 32
                BInd = BInd + 1
            End If
            ************/
            if($i_smb<32){
                $BCode[$BInd++] = $i_smb+64;
            }else{
                $BCode[$BInd++] = $i_smb-32;
            }
            
      }
  }
  /********************************
    'Подсчитываем контрольную сумму
    CCode = BCode(0) Mod 103
    For I = 1 To BInd - 1
        CCode = (CCode + BCode(I) * I) Mod 103
    Next I
    BCode(BInd) = CCode
    BInd = BInd + 1
    'Завершающий символ
    BCode(BInd) = 106
    BInd = BInd + 1
  ********************************/
  //my_var_dump_html2("\$BCode",$BCode);
  $CCode = $BCode[0]%103;
  for($i=0;$i<=$BInd;$i++){
    $CCode = ($CCode+$BCode[$i]*$i)%103;
  }
  $BCode[$BInd++] = $CCode;
  //'Завершающий символ
  $BCode[$BInd++] = 106;
  //my_var_dump_html2("\$BCode",$BCode);
  /******************************
    'Собираем строку символов шрифта
    S = ""
    For I = 0 To BInd - 1
        S = S & Code_Char(Code_128_ID(BCode(I)))
        'S = S & BarArray(BCode(I))
    Next I
    Code_128 = S
  ******************************/
  $ret_str = "";
  for($i=0;$i<$BInd;$i++){
    $tmp_id = mybarcode_code128_id($BCode[$i]);
    $ret_str .= mybarcode_code_char($tmp_id);
    //$ret_str .= $BCode[$i].";";
    //$ret_str .= $tmp_id;
  }
  //my_var_dump_html2("\$BCode",array("str"=>$str,"code"=>$ret_str));
  return $ret_str;
}
function mybarcode_code_char($str){
    /***************************
      'Штриховые символы шрифта iQs Code 128 по набору полос
      Private Function Code_Char(A As String) As String
          Dim S As String
          Dim I As Integer
          Dim B As String
          Select Case A
          Case "211412": S = "A"
          Case "211214": S = "B"
          Case "211232": S = "C"
          Case "2331112": S = "@"
          Case Else
              S = ""
              For I = 0 To Len(A) / 2 - 1
                  Select Case Mid(A, 2 * I + 1, 2)
                      Case "11": S = S & "0"
                      Case "21": S = S & "1"
                      Case "31": S = S & "2"
                      Case "41": S = S & "3"
                      Case "12": S = S & "4"
                      Case "22": S = S & "5"
                      Case "32": S = S & "6"
                      Case "42": S = S & "7"
                      Case "13": S = S & "8"
                      Case "23": S = S & "9"
                      Case "33": S = S & ":"
                      Case "43": S = S & ";"
                      Case "14": S = S & "<"
                      Case "24": S = S & "="
                      Case "34": S = S & ">"
                      Case "44": S = S & "?"
                  End Select
              Next I
          End Select
          Code_Char = S
      End Function
    ******************************/
    switch($str){
      case "211412": return "A";
      case "211214": return "B";
      case "211232": return "C";
      case "2331112": return "@";
    }
    $ret_str = "";
    for($i=0;$i<strlen($str)/2;$i++){
        $tmp_str = substr($str,2*$i,2);
        switch($tmp_str){
            case "11": $ret_str .= "0"; break;
            case "21": $ret_str .= "1"; break;
            case "31": $ret_str .= "2"; break;
            case "41": $ret_str .= "3"; break;
            case "12": $ret_str .= "4"; break;
            case "22": $ret_str .= "5"; break;
            case "32": $ret_str .= "6"; break;
            case "42": $ret_str .= "7"; break;
            case "13": $ret_str .= "8"; break;
            case "23": $ret_str .= "9"; break;
            case "33": $ret_str .= ":"; break;
            case "43": $ret_str .= ";"; break;
            case "14": $ret_str .= "<"; break;
            case "24": $ret_str .= "="; break;
            case "34": $ret_str .= ">"; break;
            case "44": $ret_str .= "?"; break;
        }
    }
    return $ret_str;
}
function mybarcode_code128_id($id){
  switch($id){
        case 0: return "212222";
        case 1: return "222122";
        case 2: return "222221";
        case 3: return "121223";
        case 4: return "121322";
        case 5: return "131222";
        case 6: return "122213";
        case 7: return "122312";
        case 8: return "132212";
        case 9: return "221213";
        case 10: return "221312";
        case 11: return "231212";
        case 12: return "112232";
        case 13: return "122132";
        case 14: return "122231";
        case 15: return "113222";
        case 16: return "123122";
        case 17: return "123221";
        case 18: return "223211";
        case 19: return "221132";
        case 20: return "221231";
        case 21: return "213212";
        case 22: return "223112";
        case 23: return "312131";
        case 24: return "311222";
        case 25: return "321122";
        case 26: return "321221";
        case 27: return "312212";
        case 28: return "322112";
        case 29: return "322211";
        case 30: return "212123";
        case 31: return "212321";
        case 32: return "232121";
        case 33: return "111323";
        case 34: return "131123";
        case 35: return "131321";
        case 36: return "112313";
        case 37: return "132113";
        case 38: return "132311";
        case 39: return "211313";
        case 40: return "231113";
        case 41: return "231311";
        case 42: return "112133";
        case 43: return "112331";
        case 44: return "132131";
        case 45: return "113123";
        case 46: return "113321";
        case 47: return "133121";
        case 48: return "313121";
        case 49: return "211331";
        case 50: return "231131";
        case 51: return "213113";
        case 52: return "213311";
        case 53: return "213131";
        case 54: return "311123";
        case 55: return "311321";
        case 56: return "331121";
        case 57: return "312113";
        case 58: return "312311";
        case 59: return "332111";
        case 60: return "314111";
        case 61: return "221411";
        case 62: return "431111";
        case 63: return "111224";
        case 64: return "111422";
        case 65: return "121124";
        case 66: return "121421";
        case 67: return "141122";
        case 68: return "141221";
        case 69: return "112214";
        case 70: return "112412";
        case 71: return "122114";
        case 72: return "122411";
        case 73: return "142112";
        case 74: return "142211";
        case 75: return "241211";
        case 76: return "221114";
        case 77: return "413111";
        case 78: return "241112";
        case 79: return "134111";
        case 80: return "111242";
        case 81: return "121142";
        case 82: return "121241";
        case 83: return "114212";
        case 84: return "124112";
        case 85: return "124211";
        case 86: return "411212";
        case 87: return "421112";
        case 88: return "421211";
        case 89: return "212141";
        case 90: return "214121";
        case 91: return "412121";
        case 92: return "111143";
        case 93: return "111341";
        case 94: return "131141";
        case 95: return "114113";
        case 96: return "114311";
        case 97: return "411113";
        case 98: return "411311";
        case 99: return "113141";
        case 100: return "114131";
        case 101: return "311141";
        case 102: return "411131";
        case 103: return "211412";
        case 104: return "211214";
        case 105: return "211232";
        case 106: return "2331112";
  }
  return "ERROR ERROR ERROR ALARM";
}
//-----------------------------------------------------------------------------
/****************
'Штриховые символы шрифта iQs Code 128 по набору полос
Private Function Code_Char(A As String) As String
    Dim S As String
    Dim I As Integer
    Dim B As String
    Select Case A
    Case "211412": S = "A"
    Case "211214": S = "B"
    Case "211232": S = "C"
    Case "2331112": S = "@"
    Case Else
        S = ""
        For I = 0 To Len(A) / 2 - 1
            Select Case Mid(A, 2 * I + 1, 2)
                Case "11": S = S & "0"
                Case "21": S = S & "1"
                Case "31": S = S & "2"
                Case "41": S = S & "3"
                Case "12": S = S & "4"
                Case "22": S = S & "5"
                Case "32": S = S & "6"
                Case "42": S = S & "7"
                Case "13": S = S & "8"
                Case "23": S = S & "9"
                Case "33": S = S & ":"
                Case "43": S = S & ";"
                Case "14": S = S & "<"
                Case "24": S = S & "="
                Case "34": S = S & ">"
                Case "44": S = S & "?"
            End Select
        Next I
    End Select
    Code_Char = S
End Function
*******************************
'Определение набора полос Code 128 по ID
Private Function Code_128_ID(ID As Integer) As String
    Dim S As String
    Select Case ID
        Case 0: S = "212222"
        Case 1: S = "222122"
        Case 2: S = "222221"
        Case 3: S = "121223"
        Case 4: S = "121322"
        Case 5: S = "131222"
        Case 6: S = "122213"
        Case 7: S = "122312"
        Case 8: S = "132212"
        Case 9: S = "221213"
        Case 10: S = "221312"
        Case 11: S = "231212"
        Case 12: S = "112232"
        Case 13: S = "122132"
        Case 14: S = "122231"
        Case 15: S = "113222"
        Case 16: S = "123122"
        Case 17: S = "123221"
        Case 18: S = "223211"
        Case 19: S = "221132"
        Case 20: S = "221231"
        Case 21: S = "213212"
        Case 22: S = "223112"
        Case 23: S = "312131"
        Case 24: S = "311222"
        Case 25: S = "321122"
        Case 26: S = "321221"
        Case 27: S = "312212"
        Case 28: S = "322112"
        Case 29: S = "322211"
        Case 30: S = "212123"
        Case 31: S = "212321"
        Case 32: S = "232121"
        Case 33: S = "111323"
        Case 34: S = "131123"
        Case 35: S = "131321"
        Case 36: S = "112313"
        Case 37: S = "132113"
        Case 38: S = "132311"
        Case 39: S = "211313"
        Case 40: S = "231113"
        Case 41: S = "231311"
        Case 42: S = "112133"
        Case 43: S = "112331"
        Case 44: S = "132131"
        Case 45: S = "113123"
        Case 46: S = "113321"
        Case 47: S = "133121"
        Case 48: S = "313121"
        Case 49: S = "211331"
        Case 50: S = "231131"
        Case 51: S = "213113"
        Case 52: S = "213311"
        Case 53: S = "213131"
        Case 54: S = "311123"
        Case 55: S = "311321"
        Case 56: S = "331121"
        Case 57: S = "312113"
        Case 58: S = "312311"
        Case 59: S = "332111"
        Case 60: S = "314111"
        Case 61: S = "221411"
        Case 62: S = "431111"
        Case 63: S = "111224"
        Case 64: S = "111422"
        Case 65: S = "121124"
        Case 66: S = "121421"
        Case 67: S = "141122"
        Case 68: S = "141221"
        Case 69: S = "112214"
        Case 70: S = "112412"
        Case 71: S = "122114"
        Case 72: S = "122411"
        Case 73: S = "142112"
        Case 74: S = "142211"
        Case 75: S = "241211"
        Case 76: S = "221114"
        Case 77: S = "413111"
        Case 78: S = "241112"
        Case 79: S = "134111"
        Case 80: S = "111242"
        Case 81: S = "121142"
        Case 82: S = "121241"
        Case 83: S = "114212"
        Case 84: S = "124112"
        Case 85: S = "124211"
        Case 86: S = "411212"
        Case 87: S = "421112"
        Case 88: S = "421211"
        Case 89: S = "212141"
        Case 90: S = "214121"
        Case 91: S = "412121"
        Case 92: S = "111143"
        Case 93: S = "111341"
        Case 94: S = "131141"
        Case 95: S = "114113"
        Case 96: S = "114311"
        Case 97: S = "411113"
        Case 98: S = "411311"
        Case 99: S = "113141"
        Case 100: S = "114131"
        Case 101: S = "311141"
        Case 102: S = "411131"
        Case 103: S = "211412"
        Case 104: S = "211214"
        Case 105: S = "211232"
        Case 106: S = "2331112"
    End Select
    Code_128_ID = S
End Function
*******************************
'Строка штрих-кода в кодировке Code 128
Public Function Code_128(A As String) As String
    Dim BCode(0 To 1023) As Integer
    Dim BInd As Integer
    Dim CurMode As String
    Dim Ch As Integer
    Dim Ch2 As Integer
    Dim I As Integer
    Dim LenA As Integer
    Dim CCode As Integer
    Dim S As String
    Dim BarArray As Variant
    
    'Собираем строку кодов
    BInd = 0
    CurMode = ""
    I = 1
    LenA = Len(A)
    While I <= LenA
        'Текущий символ в строке
        Ch = Asc(Mid(A, I, 1)) //возвращает код символа
        I = I + 1
        'Разбираем только символы от 0 до 127
        If Ch <= 127 Then
            'Следующий символ
            If I <= LenA Then
                Ch2 = Asc(Mid(A, I, 1))
            Else
                Ch2 = 0
            End If
            'Пара цифр - режим С
            If (48 <= Ch) And (Ch <= 57) And _
               (48 <= Ch2) And (Ch2 <= 57) Then
                I = I + 1
                If BInd = 0 Then
                    'Начало с режима С
                    CurMode = "C"
                    BCode(BInd) = 105
                    BInd = BInd + 1
                ElseIf CurMode <> "C" Then
                    'Переключиться на режим С
                    CurMode = "C"
                    BCode(BInd) = 99
                    BInd = BInd + 1
                End If
                'Добавить символ режима С
                BCode(BInd) = CInt(Chr(Ch) & Chr(Ch2))
                BInd = BInd + 1
            Else
                If BInd = 0 Then
                    If Ch < 32 Then
                        'Начало с режима A
                        CurMode = "A"
                        BCode(BInd) = 103
                        BInd = BInd + 1
                    Else
                        'Начало с режима B
                        CurMode = "B"
                        BCode(BInd) = 104
                        BInd = BInd + 1
                    End If
                End If
                'Переключение по надобности в режим A
                If (Ch < 32) And (CurMode <> "A") Then
                    CurMode = "A"
                    BCode(BInd) = 101
                    BInd = BInd + 1
                'Переключение по надобности в режим B
                ElseIf ((64 <= Ch) And (CurMode <> "B")) Or (CurMode = "C") Then
                    CurMode = "B"
                    BCode(BInd) = 100
                    BInd = BInd + 1
                End If
                'Служебные символы
                If (Ch < 32) Then
                    BCode(BInd) = Ch + 64
                    BInd = BInd + 1
                'Все другие символы
                Else
                    BCode(BInd) = Ch - 32
                    BInd = BInd + 1
                End If
            End If
        End If
    Wend
    'Подсчитываем контрольную сумму
    CCode = BCode(0) Mod 103
    For I = 1 To BInd - 1
        CCode = (CCode + BCode(I) * I) Mod 103
    Next I
    BCode(BInd) = CCode
    BInd = BInd + 1
    'Завершающий символ
    BCode(BInd) = 106
    BInd = BInd + 1
    'Собираем строку символов шрифта
    'BarArray = Array("155", "515", "551", "449", "485", "845", "458", "494", "854", _
        "548", "584", "944", "056", "416", "452", "065", "425", "461", "560", "506", _
        "542", "164", "524", "212", "245", "605", "641", "254", "614", "650", "119", _
        "191", "911", "089", "809", "881", "098", "818", "890", "188", "908", "980", _
        "01:", "092", "812", "029", "0:1", "821", "221", "182", "902", "128", "1:0", _
        "122", "209", "281", ":01", "218", "290", ":10", "230", "5<0", ";00", "04=", _
        "0<5", "40=", "4<1", "<05", "<41", "05<", "0=4", "41<", "4=0", "<14", "<50", _
        "=40", "50<", "320", "=04", "830", "047", "407", "443", "074", "434", "470", _
        "344", "704", "740", "113", "131", "311", "00;", "083", "803", "038", "0;0", _
        "308", "380", "023", "032", "203", "302", "A", "B", "C", "@")
    S = ""
    For I = 0 To BInd - 1
        S = S & Code_Char(Code_128_ID(BCode(I)))
        'S = S & BarArray(BCode(I))
    Next I
    Code_128 = S
End Function
**************/
//-----------------------------------------------------------------------------
