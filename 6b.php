<pre>
<?php
require_once 'lib.php';

$input = explode(' ', "COM)B B)Z Z)Y Y)X X)W W)C C)D D)E E)F B)G G)H D)I E)J J)K K)L K)YOU I)SAN");
$input = explode("\n", trim(file_get_contents('6.txt')));

$graph = [];
$address_book['COM'] = [];
$unknown = [];

while (1) {
    foreach ($input as $pair) {
        list($parent, $child) = explode(')', $pair);
        if (isset($address_book[$parent])) {
            $address_book[$child] = array_merge($address_book[$parent], [$parent]);
        }else{
            $unknown[] = $pair;
        }
    }
    if (count($unknown) == 0) break;
    $input = $unknown;
    $unknown = [];
}

$path1 = $address_book['YOU'];
$path2 = $address_book['SAN'];


for ($i=0; $i < count($path1); $i++) { 
    if ($path1[$i] != $path2[$i]) {
        echo "i $i\n";
        echo (count($path1) - $i) + (count($path2) - $i)."\n";
        die();
    }else{
        echo $path1[$i].' '.$path2[$i]."\n";
    }
}

// long story short: I suck at graph theory :-/

?>