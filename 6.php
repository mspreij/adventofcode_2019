<pre>
<?php
require_once 'lib.php';

// NB: krijg het goeie antwoord maar die code klopt niet :|

$input = explode(' ', "COM)B B)C C)D D)E E)F B)G G)H D)I E)J J)K K)L");
$input = explode("\n", trim(file_get_contents('6.txt')));

$graph = [];
foreach ($input as $pair) {
    list($parent, $child) = explode(')', $pair);
    $graph[$parent]['children'][] = $child; // <- dit is geen graph maar een lijst..
}

define('DEBUG', false);

function steps($node, $i=0)
{
    global $graph;
    if (DEBUG) echo "Running for $node\n";
    if (isset($graph[$node]['children'])) {
        $children = $graph[$node]['children'];
        $sub = $i;
        if (DEBUG) echo join(', ', array_keys($children))."\n";
        foreach ($children as $childNode) {
            if (DEBUG) echo "calling steps for childnode: $childNode\n";
            $sub += steps($childNode, $i+1);
        }
        return $sub;
    }else{
        return $i;
    }
}

echo steps('COM');


?>