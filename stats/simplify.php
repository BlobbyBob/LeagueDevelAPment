<?php

function simplifyItems($id) {

// Boot enchantments

if ($id >= 1300 && $id<=1304) return 3006;
if ($id >= 1305 && $id<=1309) return 3009;
if ($id >= 1310 && $id<=1314) return 3020;
if ($id >= 1315 && $id<=1319) return 3047;
if ($id >= 1320 && $id<=1324) return 3111;
if ($id >= 1325 && $id<=1329) return 3117;
if ($id >= 1330 && $id<=1334) return 3158;

// All jgl items with Runeglaive to Trailblazer

if ($id == 3708 || $id == 3716 || $id == 3720) return 3724;

return $id;

}

$dir = "../json/euw/{$_GET["dir"]}/";

if ($handle = opendir($dir)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            
            $content = file_get_contents($dir . $entry);
            
            $arr = json_decode($content, true);
            
            $player = $arr["participants"];
            
            foreach ($player as $k=>$p) {
            
              $player[$k]["stats"]["item0"] = simplifyItems($p["stats"]["item0"]);
              $player[$k]["stats"]["item1"] = simplifyItems($p["stats"]["item1"]);
              $player[$k]["stats"]["item2"] = simplifyItems($p["stats"]["item2"]);
              $player[$k]["stats"]["item3"] = simplifyItems($p["stats"]["item3"]);
              $player[$k]["stats"]["item4"] = simplifyItems($p["stats"]["item4"]);
              $player[$k]["stats"]["item5"] = simplifyItems($p["stats"]["item5"]);
            
            }
            
            $arr["participants"] = $player;
            
            $content = json_encode($arr);
            
            file_put_contents($dir . $entry, $content);
            
        }
    }
    closedir($handle);
}

?>