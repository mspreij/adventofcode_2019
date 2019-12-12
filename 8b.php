<pre>
<?php
$input = trim(file_get_contents('8.txt'));

$i = 0;
for ($z=0; $z < 100; $z++) { 
    for ($y=0; $y < 6; $y++) { 
        for ($x=0; $x < 25; $x++) { 
            $p = substr($input, $i, 1);
            $i++;
            $img[$z][$y][$x] = $p;
        }
    }
}

for ($x=0; $x < 25; $x++) { 
    for ($y=0; $y < 6; $y++) { 
        $z = 0;
        if ($img[$z][$y][$x] != 2) {
            $out[$x][$y] = $img[$z][$y][$x];
        }else{
            while ($img[$z][$y][$x] == 2) $z++;
            $out[$x][$y] = $img[$z][$y][$x];
        }
    }
}

$str = '';
for ($i=0; $i < 6; $i++) { 
    for ($j=0; $j < 25; $j++) { 
        $str .= $out[$j][$i];
    }
    $str .= "\n";
}

echo str_replace([1, 0], ['#', ' '], $str);

?>