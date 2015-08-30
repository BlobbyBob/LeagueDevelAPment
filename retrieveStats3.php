<?php

header("Content-type: text/plain");

set_time_limit(0);

$champsX = Array(103=>"Ahri",84=>"Akali",34=>"Anivia",1=>"Annie",268=>"Azir",432=>"Bard",53=>"Blitzcrank",63=>"Brand",69=>"Cassiopeia",31=>"Chogath",42=>"Corki",131=>"Diana",245=>"Ekko",60=>"Elise",28=>"Evelynn",81=>"Ezreal",9=>"FiddleSticks",105=>"Fizz",3=>"Galio",79=>"Gragas",74=>"Heimerdinger",43=>"Karma",30=>"Karthus",38=>"Kassadin",55=>"Katarina",10=>"Kayle",85=>"Kennen",96=>"KogMaw",7=>"Leblanc",127=>"Lissandra",117=>"Lulu",99=>"Lux",54=>"Malphite",90=>"Malzahar",82=>"Mordekaiser",25=>"Morgana",76=>"Nidalee",61=>"Orianna",78=>"Poppy",68=>"Rumble",13=>"Ryze",35=>"Shaco",27=>"Singed",37=>"Sona",50=>"Swain",134=>"Syndra",17=>"Teemo",23=>"Tryndamere",4=>"TwistedFate",45=>"Veigar",161=>"Velkoz",112=>"Viktor",8=>"Vladimir",101=>"Xerath",115=>"Ziggs",26=>"Zilean",143=>"Zyra");
///////////////////////////////////////////////////////////
function getIC($p, $champs, &$resArray, $winReq = false, $itemType = 1) {

$pStats = $p["stats"];

$win = ($winReq) ? $pStats["winner"] : 1;

if (in_array($p["championId"], $champs)) {
  
  $arr1 = Array($pStats["item0"],$pStats["item1"],$pStats["item2"],$pStats["item3"],$pStats["item4"],$pStats["item5"]);
  $arr2 = Array(3285,3089,3027,3151,3115,3157,3116,3040,3135,3165,3174,3152,3100,3001); // Items
  if ($itemType == 2) $arr2 = Array(3006,3009,3020,3047,3111,3117,3158); // Boots
  if ($itemType == 3) $arr2 = Array(3105,3102,3026,3156,3139,3222,3065,3001,3174);
  $c = array_intersect($arr1, $arr2);
  
  if (count($c) >= 1) {
  
    foreach ($arr2 as $item) {
    
      if (in_array($item, $c)) {
        $resArray[1][$p["championId"]][$item] += $win;
        $resArray[2][$p["championId"]][$item]++;
      } else if (!$winReq) $resArray[2][$p["championId"]][$item]++;
    
    }
    
  }
  
}

}
/////////////////////////////////////////////////////////
function getStat($p, $id, $winReq = false, $champ = false, $dps = false, $itemType = 1) {

$pStats = $p["stats"];
$apitems = Array(3285,3089,3027,3151,3115,3157,3116,3040,3135,3165,3174,3152,3100,3001);
$items = Array(3285,3089,3027,3151,3115,3157,3116,3040,3135,3165,3174,3152,3100,3001);
if ($itemType == 2) $items = Array(3006,3009,3020,3047,3111,3117,3158);
if ($itemType == 3) $items = Array(3105,3102,3026,3156,3139,3222,3065,3001,3174);

$win = ($winReq) ? $pStats["winner"] : 1;

//if (isset($pStats["totalDamageDealtToChampions"]) && isset($dps)) echo $pStats["totalDamageDealtToChampions"] . "/" . $dps + "\r\n";

if ($champ && (in_array($pStats["item0"], $items) || in_array($pStats["item1"], $items) || in_array($pStats["item2"], $items) || in_array($pStats["item3"], $items) || in_array($pStats["item4"], $items) || in_array($pStats["item5"], $items))) {
  if ($dps) return $pStats["totalDamageDealtToChampions"]/$dps;
  if ($p["championId"] == $id) return $win;
  if ($winReq) return -1;
  else return 0;
}
$item = false;

if ($winReq == false && $champ == false && $dps == false) {

$pItems = Array($pStats["item0"], $pStats["item1"], $pStats["item2"], $pStats["item3"], $pStats["item4"], $pStats["item5"]);
$mage = array_intersect($pItems, $apitems);
if (count($mage) > 0) {

  $cut = array_intersect(array_intersect($pItems, $items), Array($id));
  
  return count($cut);

} else return -1;
}

if ((count(array_intersect(Array($pStats["item0"], $pStats["item1"], $pStats["item2"], $pStats["item3"], $pStats["item4"], $pStats["item5"]), $apitems)) >= 1 || $itemType != 2) && $pStats["item0"] == $id || $pStats["item1"] == $id || $pStats["item2"] == $id || $pStats["item3"] == $id || $pStats["item4"] == $id || $pStats["item5"] == $id) $item = true;
if ($item && $dps) return $pStats["totalDamageDealtToChampions"]/$dps;
else if ($dps) return -1;
if ($item) return $win;

if ($winReq) return -1;
else return 0;

}
//////////////////////////////////////////////////////////////
function addStat($stat, &$var, $key, &$champReset = 2) {
  if ($champReset == 2) $cr = true;
  switch ($stat) {
    case 1:  $var[1][$key]++;$var[2][$key]++;break;
    case 0:  if ($champReset && !isset($cr)) { $var[2][$key]++;$champReset = false;} else if (isset($cr)) {$var[2][$key]++;}break;
    case -1: break;
    default: $var[1][$key]+=$stat;$var[2][$key]++; break;
  }

}
///////////////////////////////////////////////////////////////////////

$dir1 = "../json/5.11/";
$dir2 = "../json/5.14/";

foreach ($champsX as $ch) {
  $winrateC[1][$ch] = $winrateC[2][$ch] = $poprateC[1][$ch] = $poprateC[2][$ch] = 0;
}

$champs = Array(103,84,34,1,268,432,53,63,69,31,42,131,245,60,28,81,9,105,3,79,74,43,30,38,55,10,85,96,7,127,117,99,54,90,82,25,76,61,78,68,13,35,27,37,50,134,17,4,23,45,161,112,8,101,115,26,143);
$items = Array(3285,3089,3027,3151,3115,3157,3116,3040,3135,3165,3174,3152,3100,3001);
$boots = Array(3006,3009,3020,3047,3111,3117,3158);
$mr = Array(3105,3102,3026,3156,3139,3222,3065,3001,3174);


foreach ($items as $i) {
  $dpsI[1][$i] = $dpsI[2][$i] = $winrateI[1][$i] = $winrateI[2][$i] = $poprateI[1][$i] = $poprateI[2][$i] = 0;
}

foreach ($boots as $i) {
  $dpsB[1][$i] = $dpsB[2][$i] = $winrateB[1][$i] = $winrateB[2][$i] = $poprateB[1][$i] = $poprateB[2][$i] = 0;
}

foreach ($mr as $i) {
  $dpsMR[1][$i] = $dpsMR[2][$i] = $winrateMR[1][$i] = $winrateMR[2][$i] = $poprateMR[1][$i] = $poprateMR[2][$i] = 0;
}



foreach ($champs as $c) {

  foreach($items as $i) {
    $popArray[1][$c][$i] = 0;
    $popArray[2][$c][$i] = 1;
    $winArray[1][$c][$i] = 0;
    $winArray[2][$c][$i] = 1;
  }
  foreach ($boots as $i) {  
    $popArrayB[1][$c][$i] = 0;
    $popArrayB[2][$c][$i] = 1;
    $winArrayB[1][$c][$i] = 0;
    $winArrayB[2][$c][$i] = 1;
  }

}

/////////////////////////////////////////////////////////////////////////////


if ($handle = opendir($dir1)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
  
            $content = file_get_contents($dir1 . $entry);
            
            $arr = json_decode($content, true);
            
            $player = $arr["participants"];
            
            foreach ($champs as $r) $champReset[$r] = 1;
            
            foreach ($player as $k=>$p) {
              
              foreach ($champsX as $id=>$ch) {
                addStat(getStat($p, $id, false, true), $poprateC, $ch, $champReset[$id]);
                addStat(getStat($p, $id, true, true), $winrateC, $ch);
              }
              
              foreach ($items as $i) {
                addStat(getStat($p, $i, false), $poprateI, $i);
                addStat(getStat($p, $i, true), $winrateI, $i);
                
                addStat(getStat($p, $i, false, false, $arr["matchDuration"]), $dpsI, $i);
              }
              
              foreach ($boots as $i) {
                addStat(getStat($p, $i, false, false, false, 2), $poprateB, $i);
                addStat(getStat($p, $i, true, false, false, 2), $winrateB, $i);
                
                addStat(getStat($p, $i, false, false, $arr["matchDuration"], 2), $dpsB, $i);
              }
              
              foreach ($mr as $i) {
                addStat(getStat($p, $i, false, false, false, 3), $poprateMR, $i);
                addStat(getStat($p, $i, true, false, false, 3), $winrateMR, $i);
                
                addStat(getStat($p, $i, false, false, $arr["matchDuration"], 3), $dpsMR, $i);
              }
              
              getIC($p, $champs, $popArray);
              getIC($p, $champs, $winArray, true);
              
              getIC($p, $champs, $popArrayB, false, 2);
              getIC($p, $champs, $winArrayB, true, 2);
            
            }
            
        }
    }
    closedir($handle);
}
//////////////////////////////////////////////////


foreach ($winrateC[1] as $item=>$stat) {
  if ($winrateC[2][$item] == 0) $winrateC[0][$item] = 0;
   else $winrateC[0][$item] = $winrateC[1][$item]/$winrateC[2][$item];
}
foreach ($poprateC[1] as $item=>$stat) {
  if ($poprateC[2][$item] == 0) $poprateC[0][$item] = 0;
   else $poprateC[0][$item] = $poprateC[1][$item]/$poprateC[2][$item];
}
/////////////////////////////////////////////////
foreach ($winrateI[1] as $item=>$stat) {
  if ($winrateI[2][$item] == 0) $winrateI[0][$item] = 0;
   else $winrateI[0][$item] = $winrateI[1][$item]/$winrateI[2][$item];
}
foreach ($poprateI[1] as $item=>$stat) {
  if ($poprateI[2][$item] == 0) $poprateI[0][$item] = 0;
   else $poprateI[0][$item] = $poprateI[1][$item]/$poprateI[2][$item];
}
foreach ($dpsI[1] as $item=>$stat) {
  if ($dpsI[2][$item] == 0) $dpsI[0][$item] = 0;
   else $dpsI[0][$item] = $dpsI[1][$item]/$dpsI[2][$item];
}
/////////////////////////////////////////////////
foreach ($winrateB[1] as $item=>$stat) {
  if ($winrateB[2][$item] == 0) $winrateB[0][$item] = 0;
   else $winrateB[0][$item] = $winrateB[1][$item]/$winrateB[2][$item];
}
foreach ($poprateB[1] as $item=>$stat) {
  if ($poprateB[2][$item] == 0) $poprateB[0][$item] = 0;
   else $poprateB[0][$item] = $poprateB[1][$item]/$poprateB[2][$item];
}
foreach ($dpsB[1] as $item=>$stat) {
  if ($dpsB[2][$item] == 0) $dpsB[0][$item] = 0;
   else $dpsB[0][$item] = $dpsB[1][$item]/$dpsB[2][$item];
}/////////////////////////////////////////////////
foreach ($winrateMR[1] as $item=>$stat) {
  if ($winrateMR[2][$item] == 0) $winrateMR[0][$item] = 0;
   else $winrateMR[0][$item] = $winrateMR[1][$item]/$winrateMR[2][$item];
}
foreach ($poprateMR[1] as $item=>$stat) {
  if ($poprateMR[2][$item] == 0) $poprateMR[0][$item] = 0;
   else $poprateMR[0][$item] = $poprateMR[1][$item]/$poprateMR[2][$item];
}
foreach ($dpsMR[1] as $item=>$stat) {
  if ($dpsMR[2][$item] == 0) $dpsMR[0][$item] = 0;
   else $dpsMR[0][$item] = $dpsMR[1][$item]/$dpsMR[2][$item];
}
////////////////////////////////////////////
foreach ($popArray[1] as $k=>$p) {
  foreach ($p as $x=>$y) {
    if (isset($popArray[1][$k][$x]) && isset($popArray[2][$k][$x]) && $popArray[2][$k][$x] != 0) {
      $popArray[0][$k][$x] = $popArray[1][$k][$x] / $popArray[2][$k][$x] * 100;
    }
  }
}

foreach ($winArray[1] as $k=>$p) {
  foreach ($p as $x=>$y) {
    if (isset($winArray[1][$k][$x]) && isset($winArray[2][$k][$x]) && $winArray[2][$k][$x] != 0) {
      $winArray[0][$k][$x] = $winArray[1][$k][$x] / $winArray[2][$k][$x] * 100;
    }
  }
}  

foreach ($popArrayB[1] as $k=>$p) {
  foreach ($p as $x=>$y) {
    if (isset($popArrayB[1][$k][$x]) && isset($popArrayB[2][$k][$x]) && $popArrayB[2][$k][$x] != 0) {
      $popArrayB[0][$k][$x] = $popArrayB[1][$k][$x] / $popArrayB[2][$k][$x] * 100;
    }
  }
}

foreach ($winArrayB[1] as $k=>$p) {
  foreach ($p as $x=>$y) {
    if (isset($winArrayB[1][$k][$x]) && isset($winArrayB[2][$k][$x]) && $winArrayB[2][$k][$x] != 0) {
      $winArrayB[0][$k][$x] = $winArrayB[1][$k][$x] / $winArrayB[2][$k][$x] * 100;
    }
  }
} 
////////////////////////////////////////////////////

$arrOpop = $popArray[0];
$arrOwin = $winArray[0];

$arrOpopBC = $popArrayB[0];
$arrOwinBC = $winArrayB[0];

$arrOpopC = $poprateC[0];
$arrOwinC = $winrateC[0];

$arrOpopI = $poprateI[0];
$arrOwinI = $winrateI[0];
$arrOdpsI = $dpsI[0];

$arrOpopB = $poprateB[0];
$arrOwinB = $winrateB[0];
$arrOdpsB = $dpsB[0];

$arrOpopMR = $poprateMR[0];
$arrOwinMR = $winrateMR[0];
$arrOdpsMR = $dpsMR[0];

/////////////////////////////////////////////////////////

if ($handle = opendir($dir2)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
  
            $content = file_get_contents($dir2 . $entry);
            
            $arr = json_decode($content, true);
            
            $player = $arr["participants"];
            
            foreach ($champs as $r) $champReset[$r] = 1;
            
            foreach ($player as $k=>$p) {
              
              foreach ($champsX as $id=>$ch) {
                addStat(getStat($p, $id, false, true), $poprateC, $ch, $champReset[$id]);
                addStat(getStat($p, $id, true, true), $winrateC, $ch);
              }
              
              foreach ($items as $i) {
                addStat(getStat($p, $i, false), $poprateI, $i);
                addStat(getStat($p, $i, true), $winrateI, $i);
                
                addStat(getStat($p, $i, false, false, $arr["matchDuration"]), $dpsI, $i);
              }
              
              foreach ($boots as $i) {
                addStat(getStat($p, $i, false, false, false, 2), $poprateB, $i);
                addStat(getStat($p, $i, true, false, false, 2), $winrateB, $i);
                
                addStat(getStat($p, $i, false, false, $arr["matchDuration"], 2), $dpsB, $i);
              }
              
              foreach ($mr as $i) {
                addStat(getStat($p, $i, false, false, false, 3), $poprateMR, $i);
                addStat(getStat($p, $i, true, false, false, 3), $winrateMR, $i);
                
                addStat(getStat($p, $i, false, false, $arr["matchDuration"], 3), $dpsMR, $i);
              }
              
              getIC($p, $champs, $popArray);
              getIC($p, $champs, $winArray, true);
              
              getIC($p, $champs, $popArrayB, false, 2);
              getIC($p, $champs, $winArrayB, true, 2);
              
            }
            
        }
    }
    closedir($handle);
}

/////////////////////////////////////////////////////////

foreach ($winrateC[1] as $item=>$stat) {
  if ($winrateC[2][$item] == 0) $winrateC[0][$item] = 0;
   else $winrateC[0][$item] = $winrateC[1][$item]/$winrateC[2][$item];
}
foreach ($poprateC[1] as $item=>$stat) {
  if ($poprateC[2][$item] == 0) $poprateC[0][$item] = 0;
   else $poprateC[0][$item] = $poprateC[1][$item]/$poprateC[2][$item];
}
/////////////////////////////////////////////////
foreach ($winrateI[1] as $item=>$stat) {
  if ($winrateI[2][$item] == 0) $winrateI[0][$item] = 0;
   else $winrateI[0][$item] = $winrateI[1][$item]/$winrateI[2][$item];
}
foreach ($poprateI[1] as $item=>$stat) {
  if ($poprateI[2][$item] == 0) $poprateI[0][$item] = 0;
   else $poprateI[0][$item] = $poprateI[1][$item]/$poprateI[2][$item];
}
foreach ($dpsI[1] as $item=>$stat) {
  if ($dpsI[2][$item] == 0) $dpsI[0][$item] = 0;
   else $dpsI[0][$item] = $dpsI[1][$item]/$dpsI[2][$item];
}
/////////////////////////////////////////////////
foreach ($winrateB[1] as $item=>$stat) {
  if ($winrateB[2][$item] == 0) $winrateB[0][$item] = 0;
   else $winrateB[0][$item] = $winrateB[1][$item]/$winrateB[2][$item];
}
foreach ($poprateB[1] as $item=>$stat) {
  if ($poprateB[2][$item] == 0) $poprateB[0][$item] = 0;
   else $poprateB[0][$item] = $poprateB[1][$item]/$poprateB[2][$item];
}
foreach ($dpsB[1] as $item=>$stat) {
  if ($dpsB[2][$item] == 0) $dpsB[0][$item] = 0;
   else $dpsB[0][$item] = $dpsB[1][$item]/$dpsB[2][$item];
}/////////////////////////////////////////////////
foreach ($winrateMR[1] as $item=>$stat) {
  if ($winrateMR[2][$item] == 0) $winrateMR[0][$item] = 0;
   else $winrateMR[0][$item] = $winrateMR[1][$item]/$winrateMR[2][$item];
}
foreach ($poprateMR[1] as $item=>$stat) {
  if ($poprateMR[2][$item] == 0) $poprateMR[0][$item] = 0;
   else $poprateMR[0][$item] = $poprateMR[1][$item]/$poprateMR[2][$item];
}
foreach ($dpsMR[1] as $item=>$stat) {
  if ($dpsMR[2][$item] == 0) $dpsMR[0][$item] = 0;
   else $dpsMR[0][$item] = $dpsMR[1][$item]/$dpsMR[2][$item];
}
////////////////////////////////////////////
foreach ($popArray[1] as $k=>$p) {
  foreach ($p as $x=>$y) {
    if (isset($popArray[1][$k][$x]) && isset($popArray[2][$k][$x]) && $popArray[2][$k][$x] != 0) {
      $popArray[0][$k][$x] = $popArray[1][$k][$x] / $popArray[2][$k][$x] * 100;
    }
  }
}

foreach ($winArray[1] as $k=>$p) {
  foreach ($p as $x=>$y) {
    if (isset($winArray[1][$k][$x]) && isset($winArray[2][$k][$x]) && $winArray[2][$k][$x] != 0) {
      $winArray[0][$k][$x] = $winArray[1][$k][$x] / $winArray[2][$k][$x] * 100;
    }
  }
}  

foreach ($popArrayB[1] as $k=>$p) {
  foreach ($p as $x=>$y) {
    if (isset($popArrayB[1][$k][$x]) && isset($popArrayB[2][$k][$x]) && $popArrayB[2][$k][$x] != 0) {
      $popArrayB[0][$k][$x] = $popArrayB[1][$k][$x] / $popArrayB[2][$k][$x] * 100;
    }
  }
}

foreach ($winArrayB[1] as $k=>$p) {
  foreach ($p as $x=>$y) {
    if (isset($winArrayB[1][$k][$x]) && isset($winArrayB[2][$k][$x]) && $winArrayB[2][$k][$x] != 0) {
      $winArrayB[0][$k][$x] = $winArrayB[1][$k][$x] / $winArrayB[2][$k][$x] * 100;
    }
  }
} 
////////////////////////////////////////////////////

$arrNpop = $popArray[0];
$arrNwin = $winArray[0];

$arrNpopBC = $popArrayB[0];
$arrNwinBC = $winArrayB[0];

$arrNpopC = $poprateC[0];
$arrNwinC = $winrateC[0];

$arrNpopI = $poprateI[0];
$arrNwinI = $winrateI[0];
$arrNdpsI = $dpsI[0];

$arrNpopB = $poprateB[0];
$arrNwinB = $winrateB[0];
$arrNdpsB = $dpsB[0];

$arrNpopMR = $poprateMR[0];
$arrNwinMR = $winrateMR[0];
$arrNdpsMR = $dpsMR[0];


////////////////////////////////////////////////////////////

//header("Content-type: text/plain");

function makeText($arr1, $arr2, $type = 1) {

  $up = '<span class=\'fa fa-caret-up\'></span>';
  $down = '<span class=\'fa fa-caret-down\'></span>';
  $equal = '<span class=\'fa fa-caret-right\'></span>';

  if ($type == 1) {
    
    $champs = Array(103=>"Ahri",84=>"Akali",34=>"Anivia",1=>"Annie",268=>"Azir",432=>"Bard",53=>"Blitzcrank",4=>"TwistedFate",63=>"Brand",69=>"Cassiopeia",31=>"Chogath",42=>"Corki",131=>"Diana",245=>"Ekko",60=>"Elise",28=>"Evelynn",81=>"Ezreal",9=>"FiddleSticks",105=>"Fizz",3=>"Galio",79=>"Gragas",74=>"Heimerdinger",43=>"Karma",30=>"Karthus",38=>"Kassadin",55=>"Katarina",10=>"Kayle",85=>"Kennen",96=>"KogMaw",7=>"Leblanc",127=>"Lissandra",117=>"Lulu",99=>"Lux",54=>"Malphite",90=>"Malzahar",82=>"Mordekaiser",25=>"Morgana",76=>"Nidalee",61=>"Orianna",78=>"Poppy",68=>"Rumble",13=>"Ryze",35=>"Shaco",27=>"Singed",37=>"Sona",50=>"Swain",134=>"Syndra",17=>"Teemo",23=>"Tryndamere",45=>"Veigar",161=>"Velkoz",112=>"Viktor",8=>"Vladimir",101=>"Xerath",115=>"Ziggs",26=>"Zilean",143=>"Zyra");
    
    $string = "";
    
    foreach ($arr1 as $c=>$itemsOld) {
    
      $string .= "\"{$champs[$c]}\"=>Array(";
        
        $itemsNew = $arr2[$c];
        
        arsort($itemsOld);
        arsort($itemsNew);
        $count = 0;
        foreach ($itemsNew as $item=>$rateN) {
        
          if ($count++ < 3) {
            $rateO = round($itemsOld[$item],1);
            $rateN = round($itemsNew[$item],1);
            if ($rateO < $rateN) $string .= "$item=>\"$rateO%&nbsp;$up&nbsp;$rateN%\",";
            else if ($rateO > $rateN) $string .= "$item=>\"$rateO%&nbsp;$down&nbsp;$rateN%\",";
            else if ($rateO == $rateN) $string .= "$item=>\"$rateO%&nbsp;$equal&nbsp;$rateN%\",";
          } else if ($count == 3) {
            $rateO = round($itemsOld[$item],1);
            $rateN = round($itemsNew[$item],1);
            if ($rateO < $rateN) $string .= "$item=>\"$rateO%&nbsp;$up&nbsp;$rateN%\"";
            else if ($rateO > $rateN) $string .= "$item=>\"$rateO%&nbsp;$down&nbsp;$rateN%\"";
            else if ($rate == $rateN) $string .= "$item=>\"$rateO%&nbsp;$equal&nbsp;$rateN%\"";
          }
        
        }
      
      $string .= "),";
      
    }
  } else if ($type == 2) {
    
    $string = "";
    
    foreach ($arr1 as $c=>$rateO) {
    
      $string .= "\"$c\"=>";
      
      $rateO = round($rateO,3)*100;
      $rateN = round($arr2[$c],3)*100;
      if ($rateO < $rateN) $string .= "\"$rateO%&nbsp;$up&nbsp;$rateN%\",";
      else if ($rateO > $rateN) $string .= "\"$rateO%&nbsp;$down&nbsp;$rateN%\",";
      else if ($rateO == $rateN) $string .= "\"$rateO%&nbsp;$equal&nbsp;$rateN%\",";
    
    }
  
  } else if ($type == 3) {
  
    $string = "";
    
    foreach ($arr1 as $i=>$dpsO) {
    
      $dpsO = round($dpsO, 1);
      $dpsN = round($arr2[$i], 1);
      
      $string .= "$i=>";
      
      if ($dpsO < $dpsN) $string .= "\"{$dpsO}&nbsp;$up&nbsp;{$dpsN}\",";
      else if ($dpsO > $dpsN) $string .= "\"{$dpsO}&nbsp;$down&nbsp;{$dpsN}\",";
      else if ($dpsO == $dpsN) $string .= "\"{$dpsO}&nbsp;$equal&nbsp;{$dpsN}\",";
      
    }
    
  }
  
  echo '= Array(' . $string . ');';
  
}

/////////////////////////////////////////////////
/*
echo "<pre>";
print_r($arrOwinC);
echo "</pre>";
*/

echo "\r\n\r\n";
echo "\$poprateIC ";
makeText($arrOpop, $arrNpop);
echo "\r\n\r\n";
echo "\$winrateIC ";
makeText($arrOwin, $arrNwin);

echo "\r\n\r\n";
echo "\$poprateBC ";
makeText($arrOpopBC, $arrNpopBC);
echo "\r\n\r\n";
echo "\$winrateBC ";
makeText($arrOwinBC, $arrNwinBC);


echo "\r\n\r\n";
echo "\$poprate ";
makeText($arrOpopC, $arrNpopC, 2);
echo "\r\n\r\n";
echo "\$winrate ";
makeText($arrOwinC, $arrNwinC, 2);
echo "\r\n\r\n";
echo "\$poprateI ";
makeText($arrOpopI, $arrNpopI, 2);
echo "\r\n\r\n";
echo "\$winrateI ";
makeText($arrOwinI, $arrNwinI, 2);
echo "\r\n\r\n";
echo "\$dpsI ";
makeText($arrOdpsI, $arrNdpsI, 3);
echo "\r\n\r\n";
echo "\$poprateB ";
makeText($arrOpopB, $arrNpopB, 2);
echo "\r\n\r\n";
echo "\$winrateB ";
makeText($arrOwinB, $arrNwinB, 2);
echo "\r\n\r\n";
echo "\$dpsB ";
makeText($arrOdpsB, $arrNdpsB, 3);
echo "\r\n\r\n";
echo "\$poprateMR ";
makeText($arrOpopMR, $arrNpopMR, 2);
echo "\r\n\r\n";
echo "\$winrateMR ";
makeText($arrOwinMR, $arrNwinMR, 2);
echo "\r\n\r\n";
echo "\$dpsMR ";
makeText($arrOdpsMR, $arrNdpsMR, 3);


?>