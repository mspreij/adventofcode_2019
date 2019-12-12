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
            @$zeroes[$z][$p] += 1;
        }
    }
}

foreach ($zeroes as $z => $triplet) {
    // this dumps out few enough to check manually \o/
    if ($triplet[0] < 10) echo $z .':'. var_export($triplet, 1);
}

// and the answer is 19 * 127 = 2413

?>