Attribute VB_Name = "Barcode128"
Option Explicit

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

'Определение набора полос Code 2 of 7 по символу
Private Function Codabar_Ch(Ch As String) As String
    Dim S As String
    Select Case Ch
        Case "0": S = "11111331"
        Case "1": S = "11113311"
        Case "2": S = "11131131"
        Case "3": S = "33111111"
        Case "4": S = "11311311"
        Case "5": S = "31111311"
        Case "6": S = "13111131"
        Case "7": S = "13113111"
        Case "8": S = "13311111"
        Case "9": S = "31131111"
        Case "-": S = "11133111"
        Case "$": S = "11331111"
        Case ":": S = "31113131"
        Case "/": S = "31311131"
        Case ".": S = "31313111"
        Case "+": S = "11313131"
        Case "a": S = "11331111"
        Case "b": S = "13131131"
        Case "c": S = "11131331"
        Case "d": S = "11133311"
        Case "t": S = "11331311"
        Case "n": S = "13131131"
        Case "*": S = "11131331"
        Case "e": S = "11133311"
        Case Else: S = ""
    End Select
    Codabar_Ch = S
End Function

'Определение набора полос Code 39 по символу
Private Function Code_39_Ch(Ch As String) As String
    Dim S As String
    Select Case Ch
        Case "1": S = "3113111131"
        Case "2": S = "1133111131"
        Case "3": S = "3133111111"
        Case "4": S = "1113311131"
        Case "5": S = "3113311111"
        Case "6": S = "1133311111"
        Case "7": S = "1113113131"
        Case "8": S = "3113113111"
        Case "9": S = "1133113111"
        Case "0": S = "1113313111"
        Case "A": S = "3111131131"
        Case "B": S = "1131131131"
        Case "C": S = "3131131111"
        Case "D": S = "1111331131"
        Case "E": S = "3111331111"
        Case "F": S = "1131331111"
        Case "G": S = "1111133131"
        Case "H": S = "3111133111"
        Case "I": S = "1131133111"
        Case "J": S = "1111333111"
        Case "K": S = "3111111331"
        Case "L": S = "1131111331"
        Case "M": S = "3131111311"
        Case "N": S = "1111311331"
        Case "O": S = "3111311311"
        Case "P": S = "1131311311"
        Case "Q": S = "1111113331"
        Case "R": S = "3111113311"
        Case "S": S = "1131113311"
        Case "T": S = "1111313311"
        Case "U": S = "3311111131"
        Case "V": S = "1331111131"
        Case "W": S = "3331111111"
        Case "X": S = "1311311131"
        Case "Y": S = "3311311111"
        Case "Z": S = "1331311111"
        Case "-": S = "1311113131"
        Case ".": S = "3311113111"
        Case " ": S = "1331113111"
        Case "*": S = "1311313111"
        Case "$": S = "1313131111"
        Case "/": S = "1313111311"
        Case "+": S = "1311131311"
        Case "%": S = "1113131311"
        Case Else: S = ""
    End Select
    Code_39_Ch = S
End Function

'Определение ширины полос Interleaved 2 of 5 для одного символа
Private Function Code_2of5_Ch(Ch As String) As String
    Dim S As String
    Select Case Ch
        Case "0": S = "11331"
        Case "1": S = "31113"
        Case "2": S = "13113"
        Case "3": S = "33111"
        Case "4": S = "11313"
        Case "5": S = "31311"
        Case "6": S = "13311"
        Case "7": S = "11133"
        Case "8": S = "31131"
        Case "9": S = "13131"
    End Select
    Code_2of5_Ch = S
End Function

'Определение набора полос Interleaved 2 of 5 по двум символам
Private Function Interleaved_2of5_Pair(Pair As String) As String
    Dim S1 As String
    Dim S2 As String
    Dim S As String
    Dim I As Integer
    S1 = Code_2of5_Ch(Mid(Pair, 1, 1))
    S2 = Code_2of5_Ch(Mid(Pair, 2, 1))
    S = ""
    For I = 1 To Len(S1)
        S = S & Mid(S1, I, 1) & Mid(S2, I, 1)
    Next I
    Interleaved_2of5_Pair = S
End Function

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
        Ch = Asc(Mid(A, I, 1))
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

'Строка штрих-кода в кодировке Codabar
Public Function Codabar(A As String)
    Dim I As Integer
    Dim S As String
    S = ""
    For I = 1 To Len(A)
        S = S & Code_Char(Codabar_Ch(Mid(A, I, 1)))
    Next I
    'Старт/стоп d/e. Возможные варианты a/t b/n c/* d/e
    Codabar = Code_Char(Codabar_Ch("d")) & S & Code_Char(Codabar_Ch("e"))
End Function

'Строка штрих-кода в кодировке Code 39
Public Function Code_39(A As String)
    Dim I As Integer
    Dim S As String
    S = ""
    For I = 1 To Len(A)
        S = S & Code_Char(Code_39_Ch(Mid(A, I, 1)))
    Next I
    'Старт/стоп - символ *
    Code_39 = Code_Char(Code_39_Ch("*")) & S & Code_Char(Code_39_Ch("*"))
End Function

'Строка штрих-кода в кодировке Interleaved 2 of 5
Public Function Interleaved_2of5(A As String, Optional Check As Boolean = False)
    Dim I As Integer
    Dim D As String
    Dim S As String
    Dim Ch As Integer
    Dim K As Integer
    'Преобразование к строке фифр
    D = ""
    For I = 1 To Len(A)
        Ch = Asc(Mid(A, I, 1))
        If (48 <= Ch) And (Ch <= 57) Then
            D = D & Chr(Ch)
        End If
    Next I
    'Добавить лидирующий 0
    If ((Len(D) Mod 2 > 0) And (Not Check)) Or _
        ((Len(D) Mod 2 = 0) And Check) Then
        D = "0" & D
    End If
    'Расчет и добавление контрольного разряда
    If Check Then
        K = 0
        For I = 1 To Len(D)
            If I Mod 2 > 0 Then
                K = K + CInt(Mid(D, I, 1)) * 3
            Else
                K = K + CInt(Mid(D, I, 1))
            End If
        Next I
        K = 10 - (K Mod 10)
        D = D & CStr(K)
    End If
    'Составление строки кода по парам цифр
    S = ""
    For I = 0 To Len(D) / 2 - 1
        S = S & Code_Char(Interleaved_2of5_Pair(Mid(D, I * 2 + 1, 2)))
    Next I
    'Добавить старт/стоп символы
    Interleaved_2of5 = Code_Char("1111") & S & Code_Char("3111")
End Function

