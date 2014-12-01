<?php
/**************************************
*
*  Kage Park
*  2012-08-16
*
**************************************/

function _k_skip_many_space($arr=array(),$current) {
        if($current>0)
           $z=$current-1;
        if(strcmp($arr[$z],' ')==0 && strcmp($arr[$current],' ')==0) return true;
        return false;
}

/*
  2012-08-20
  return false or array;
  output("<num>", "<string>");
  example)
  $kkk=_k_block($<array>,$<num>);
  if($kkk > 0 ) {
       $i=$kkk[0];
       $tmp .= $kkk[1];
       continue;
  }
*/
function _k_block($arr=array(),$current) {
    $chk1=0;
    $chk2=0;
    $out=array();
    $tmp="";
    $tmp2="";
    for($i=$current;$i<strlen($arr);$i++) {
        if(strcmp($arr[$i],'\'')==0 && $chk1==0) {
           $tmp .= "$arr[$i]";
           $chk1=1;
           continue;
        }else if(strcmp($arr[$i],'\'')==0 && $chk1==1) {
           $tmp .= "$arr[$i]";
           $chk1=0;
           $out[0]="$i";
           $out[1]="$tmp";
           return $out;
        } else if($chk1==1) {
           $tmp .= $arr[$i];
           continue;
        }

        if(strcmp($arr[$i],'\"')==0 && $chk2==0) {
           $tmp2 .= $arr[$i];
           $chk2=1;
           continue;
        }else if(strcmp($arr[$i],'\"')==0 && $chk2==1) {
           $tmp2 .= $arr[$i];
           $chk2=0;
           $out[0]="$i";
           $out[1]="$tmp2";
           return $out;
        } else if($chk2==1) {
           $tmp2 .= $arr[$i];
           continue;
        }
        if($chk1==0 && $chk2==0) {
           return -1;
        }
    }
    return -1;
}


function _k_find_block($arr=array(),$current,$block_simbol=NULL) {
    $chk1=0;
    $out=array();
    $tmp="";
    if ($block_simbol!=NULL) {
        if (sizeof($block_simbol) == 2 ) {
           $start_block=$block_simbol[0];
           $end_block=$block_simbol[1];
        } else {
           return -1;
        }
    } else {
        $start_block="'";
        $end_block="'";
    }
    for($i=$current;$i<strlen($arr);$i++) {
        if(strcmp($arr[$i],$start_block)==0 && $chk1==0) {
           $tmp .= "$arr[$i]";
           $chk1=1;
           continue;
        }else if(strcmp($arr[$i],$end_block)==0 && $chk1==1) {
           $tmp .= "$arr[$i]";
           $chk1=0;
           $out[0]="$i";
           $out[1]="$tmp";
           return $out;
        } else if($chk1==1) {
           $tmp .= $arr[$i];
           continue;
        }

        if($chk1==0) return -1;
    }
    return -1;
}

/* Example
list($aaa,$bbb,$ccc,$mmm)=_k_array("abcd efg kkkk jjjj uuuu",2);
//list($aaa,$bbb,$ccc,$mmm)=_k_array("abcd efg kkkk jjjj uuuu");
echo ">$aaa<br>";
echo ">$bbb<br>";
echo ">$ccc<br>";
echo ">$mmm<br>"; */
function _k_array($src,$num=NULL) {
    $out=array();
    $j=0;
    $z=0;
    $chk1=0;
    $chk2=0;
    $m=0;
    for($i=0;$i<strlen($src);$i++) {
        //make a one space for many space
        if(_k_skip_many_space($src,$i)) continue;

        // make a one value after number of assigned arrary
        if($num!=NULL && ($num-1) <= $j) {
           $tmp .= $src[$i];
           continue;
        }

        //block for "\'" 
        $kkk=_k_find_block($src,$i);
        if($kkk > 0 ) {
           $i=$kkk[0];
           $tmp .= $kkk[1];
           continue;
        }

        //block for "\"" 
        $block_simbol=array("\"","\"");
        $kkk=_k_find_block($src,$i,$block_simbol);
        if($kkk > 0 ) {
           $i=$kkk[0];
           $tmp .= $kkk[1];
           continue;
        }


        // sperate with " " or "\t" or "\n"
        if(strcmp($src[$i],' ')==0 ||strcmp($src[$i],"\t")==0 || strcmp($src[$i],"\n")==0) {
              $out[$j]="$tmp";
              $tmp="";
              $j++;
        } else {
              $tmp .= $src[$i];
        }
    } 
    $out[$j]="$tmp";
    return $out;
}

function _k_find_array($src,$num=NULL,$block_simbol=NULL) {
    $out=array();
    $j=0;
    $z=0;
    $chk1=0;
    $chk2=0;
    $m=0;
    for($i=0;$i<strlen($src);$i++) {
        //make a one space for many space
        if(_k_skip_many_space($src,$i)) continue;

        // make a one value after number of assigned arrary
        if($num!=NULL && ($num-1) <= $j) {
           $tmp .= $src[$i];
           continue;
        }

        //block for block_simbol
        $kkk=_k_find_block($src,$i,$block_simbol);
        if($kkk > 0 ) {
           $i=$kkk[0];
           $tmp .= $kkk[1];
           continue;
        }

        // sperate with " " or "\t" or "\n"
        if(strcmp($src[$i],' ')==0 ||strcmp($src[$i],"\t")==0 || strcmp($src[$i],"\n")==0) {
              $out[$j]="$tmp";
              $tmp="";
              $j++;
        } else {
              $tmp .= $src[$i];
        }
    }
    $out[$j]="$tmp";
    return $out;
}


?>
