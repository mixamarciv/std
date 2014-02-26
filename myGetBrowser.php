<?php
//-----------------------------------------------------------------------------
function getBrowser(){
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $ub = '';
        if(preg_match('/MSIE/i',$u_agent)){
            $ub = "Internet Explorer";
        }
        elseif(preg_match('/Firefox/i',$u_agent)){
            $ub = "Mozilla Firefox";
        }
        elseif(preg_match('/Safari/i',$u_agent)){
            $ub = "Apple Safari";
        }
        elseif(preg_match('/Chrome/i',$u_agent)){
            $ub = "Google Chrome";
        }
        elseif(preg_match('/Flock/i',$u_agent)){
            $ub = "Flock";
        }
        elseif(preg_match('/Opera/i',$u_agent)){
            $ub = "Opera";
        }
        elseif(preg_match('/Netscape/i',$u_agent)){
            $ub = "Netscape";
        }
        return $ub;
}
function using_ie(){
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $ub = False;
    if(preg_match('/MSIE/i',$u_agent)){
        $ub = True;
    }
    return $ub;
}
//-----------------------------------------------------------------------------


