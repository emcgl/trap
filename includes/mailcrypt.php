<?php

/*
 * Replace @ with [AT] and . with [DOT]  
 */
function antiSpamMail($mail)
{
        $result = str_replace('.', ' [DOT] ', $mail);
        $result = str_replace('@', ' [AT] ', $result);
        return($result);
}

/*
 * Simple encrypt email for href (working with jscript decrypting) 
 */
function hrefCryptMail($mail)
{
        $cs = "mailto:".$mail;
        $result="";
        for($i=0;$i<strlen($cs);$i++) {
                $n=ord($cs[$i]);
                if($n>=8364)$n=128;             
                $result.=chr($n+1);
        }
        $href="javascript:linkTo_UnCryptMailto('$result');";
        return $href;
}
?>
