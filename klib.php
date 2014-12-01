<?php

//////////////Linux command///////////////////
function _k_l_awk($src,$pnum,$split=" ") {
    $data=array();
    if(strcmp($split," ")==0) {
        $tmp="";
        $tmp=ereg_replace("[ ]+"," ",trim($src,' '));
        $data=split($split,$tmp);
    } else {
        $data=split($split,$src);
    }
    $pnum=$pnum-1;
//echo "awk> $src => >$data[$pnum]< $pnum >$split<<br>";
//    return $data[$pnum];
    return trim ( $data[$pnum],"[\t\n\r\0\x0B]");
}

function _k_count_block($val,$count) {
    if(eregi("[[:space:]]{",$val) && eregi("[[:space:]]}[\r\n]",$val) )
        return $count;
    else if(eregi("[[:space:]]{[\r\n]",$val))  
        return $count+1;
    else if(eregi("[[:space:]]}[\r\n]",$val) || eregi("}[\r\n]",$val))  
        return $count-1;
    else
        return $count;
}

function _k_log($src,$level=NULL) {
    if($level==NULL) return 0;

    if(strcmp($level,"ws")==0) 
        echo "<br> dbg> $src";
    else if(strcmp($level,"cs")==0) 
        echo "\n dbg> $src";
}

function _k_split_words($string, $max = 1)  {
    $words = preg_split('/\s/', $string);
    $lines = array();
    $line = '';

    foreach ($words as $k => $word) {
        $length = strlen($line . ' ' . $word);
        if ($length <= $max) {
            $line .= ' ' . $word;
        } else if ($length > $max) {
            if (!empty($line)) $lines[] = trim($line);
            $line = $word;
        } else {
            $lines[] = trim($line) . ' ' . $word;
            $line = '';
        }
    }
    $lines[] = ($line = trim($line)) ? $line : $word;

    return $lines;
}

function _k_conf_naked($src,$simbol=NULL) {
  if($simbol==NULL) {
     $simbol=array("'","\"");
  }
  for($i=0;$i<count($simbol);$i++) {
      if(eregi($simbol[$i],$src)) {
          $data=split($simbol[$i],$src);
              return ereg_replace("[\r\n]","",$data[1]); //remove enter key
      }
  }
  return -1;
}


function _k_conf_items($find,$file_name) {
  $output="";
  //$dbg="ws";
  $dbg=null;
  $oc=0; //open count {
  $cc=0; //close count }
  $block=0; //{}
  $find_block=0; //{}
  $chk=0;
  $fchk=0;
  $path=array();  
  $path=split("/",$find);

  $find_deep=sizeof($path)-1;
  if($find_deep>=3) {
      $deep=3; 
  } else {
      $deep=$find_deep;
  }
  _k_log("find:$find, find_deep: $find_deep, deep: $deep<br>",$dbg);

  //readfile
  $line=file($file_name);
  while(list($key,$val)=each($line)) {
 
     if(! eregi("^#",$val)) {

        $block=_k_count_block($val,$block);

        if($fchk==1 && $block<$find_block && $find_block > 0) {
             return $output;
        } else if($chk==1) {
            if($block<$find_block && $find_block > 0) {
               _k_log(" out of block : block:$block <=> find block:$find_block<br>",$dbg);
               return -1;
            }
        }

        if( $oc != $deep ) { // find path from root to last path
           if( eregi("^$path[$oc] ",trim($val,' ')) ) {
               _k_log("Found : $path[$oc] ($oc) , $val<br>",$dbg);
               //if(eregi(" {",$val)) 
               if(eregi("[[:space:]]{[\r\n]",$val))  {
                   $find_block=$block;
                   $oc=$oc+1;
                   $chk=1;
               }
           }
        } else if( $oc == $deep ) { // find destnation
           $find_val=_k_l_awk($val,1," ");
           if(strcmp($find_val,$path[$oc])==0)  {
               _k_log("found path at $key to $val : $find_val with {} at oc:$oc & deep:$deep & block:$block<br>",$dbg);
               $find_block=$block;
               $fchk=1;
           } else  if ( $fchk==1 && $block == $find_block ) {
               $item=_k_l_awk($val,1," ");
               if ( strcmp("$item", "}")!=0 && strcmp("$item","")!=0)
                   $output="$output $item";
           } 
           _k_log(">>>> $key=>$val : olc:$oc deep:$deep block:$block find_block:$find_block chk:$chk<br>",$dbg);
        }
     }
  }
  return -1;
}

function _k_conf_read($find,$file_name) {
  //$dbg="ws";
  $dbg=null;
  $oc=0; //open count {
  $cc=0; //close count }
  $block=0; //{}
  $find_block=0; //{}
  $chk=0;
  $path=array();  
  $path=split("/",$find);

  $find_deep=sizeof($path)-1;
  if($find_deep>=3) {
      $deep=3; 
  } else {
      $deep=$find_deep;
  }
  _k_log("find:$find, find_deep: $find_deep, deep: $deep<br>",$dbg);

  //readfile
  $line=file($file_name);
  while(list($key,$val)=each($line)) {
 
     if(! eregi("^#",$val)) {

        $block=_k_count_block($val,$block);
        if($chk==1) {
            if($block<$find_block && $find_block > 0) {
               _k_log(" out of block : block:$block <=> find block:$find_block<br>",$dbg);
               return -1;
            }
        }

        if( $oc != $deep ) { // find path from root to last path
           if( eregi("^$path[$oc] ",trim($val,' ')) ) {
               _k_log("Found : $path[$oc] ($oc) , $val<br>",$dbg);
               //if(eregi(" {",$val)) 
               //if(eregi("[[:space:]]{[\r\n]",$val))  {
               if(eregi("[[:space:]]{[[:space:]][\r\n]",$val) || eregi("[[:space:]]{[\r\n]",$val))  {
                   $find_block=$block;
                   $oc=$oc+1;
                   $chk=1;
               }
           }
        } else if( ($oc == $deep) ) { // find destnation
           $find_val=_k_l_awk($val,1," ");
           if(strcmp($find_val,$path[$oc])==0)  {
               if(eregi("[{}]",$val)) { //extra { } 
                   _k_log("found path at $key to $val : $find_val with {} at oc:$oc & deep:$deep<br>",$dbg);
                   $tmp=array();
                   $tmp=split(" ",ereg_replace("[ ]+"," ",trim(_k_l_awk(_k_l_awk($val,2,"{"),1,"}"),' ')));
                   for($i=0;$i<count($tmp);$i++) {
                       $_f_v=_k_l_awk($tmp[$i],1,"=");
                       if(strcmp($_f_v,$path[$find_deep])==0) {
                           $mmm=_k_l_awk($tmp[$i],2,"=");
                           $aaa=_k_conf_naked($mmm);
                           if($aaa<0)
                              return ereg_replace("[\r\n]","",$mmm);
                           else
                              return $aaa;
                       }
                   }
               } else {
                   _k_log("found path : $find_val without {} at oc:$oc & deep:$deep<br>",$dbg);
                   $value=_k_conf_naked($val);
                   if($value<0) {
                       return ereg_replace("[\r\n]","",_k_l_awk($val,2," "));
                   } else {
                       return $value;
                   }
               }
           }
        }
     }
  }
  return -1;
}

function _k_data_check($arr,$input) {
    $input_arr=_k_split_words($input);
    for($i=0; $i<count($input_arr);$i++) {
         $kk=$input_arr[$i];
         if ( $arr[$kk] == NULL )
               echo " * $kk value not found<br>";
    }
}

?>
