<?php

for ($i = 206938; $i <= 679128; $i++) {
  $cl = 0;
  $same = false;
  $s = (string)$i;
  for ($j=0;$j<=5;$j++) {
    if ($j>0) {
      if ($s[$j] < $s[$j-1]) continue 2;
      if ($s[$j] == $s[$j-1]) $same = true;
    }
  }
  if ($same) $out[] = $i;
}
echo count($out)."\n";

$out2 = [];
foreach ($out as $str) {
  settype($str, 'string');
  $x = [];
  for ($j=0;$j<=5;$j++) {
    @$x[$str[$j]]++;
  }
  if (in_array(2, $x)) $out2[] = $str;
}

echo count($out2)."\n";

