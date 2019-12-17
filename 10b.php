<pre>
<?php
require_once 'lib.php';
$input = trim(file_get_contents('10.txt'));

$input =
".#..##.###...#######
##.############..##.
.#.######.########.#
.###.#######.####.#.
#####.##.#.##.###.##
..#####..#.#########
####################
#.####....###.#.#.##
##.#################
#####.##.###..####..
..######..##.#######
####.##.####...##..#
.#####..#.######.###
##...#.##########...
#.##########.#######
.####.#.###.###.#.##
....##.##.###..#####
.#.#.###########.###
#.#.#.#####.####.###
###.##.####.##.#..##";

$input =
'.#....#####...#..
##...##.#####..##
##...#...#.#####.
..#.....#...###..
..#.#.....#....##';
$x = 8; $y = 3; // TEST INPUT

$input =
'
..#...#
.#.....
#..o.#.
.#....#
.#..#..
';

foreach (explode("\n", trim($input)) as $y => $line) {
    foreach (str_split($line) as $x => $char) {
        $grid[$y][$x] = $char;
    }
}



$x = 3; $y = 2; // FRIGGIN INSANITY CHECK TEST INPUT

$width = count($grid[0]);
$height = count($grid);

// $x = $y = 11; // our giant rotating laser \o/ shoot all the things f yea

$n = 1; // zapped asteroids
while ($n <= 200) {
    $to_zap = $blocked = []; // things we can see _right now_
    // Thought process: find all asteroids it can currently see, vaporize them clockwise, starting right above it.
    // To get them in the right order sort them /somehow/ by the angle of the line to their coords from [11,11]
    // something trig, yeah? oh that should be trivial
    // anyway when that's done do the whole thing again, find all asteroids it can currently see and then vaporize them in the right order.
    // also count them, and yell at nr 200. Bloody elves..
    $nx = $x + 1; // go east
    while ($nx < $width) {
        if ($grid[$y][$nx] === '#') {
            $to_zap[] = ($nx-$x).':0'; // add that to Zaps
            break; // then the rest is blocked and can be ignored
        }
        $nx++;
    }
    $px = $x - 1; // other way
    while ($px >= 0) {
        if ($grid[$y][$px] === '#') {
            $to_zap[] = ($px-$x).':0'; // add that to Zappa
            break; // then the rest is blocked and can be ignored
        }
        $px--; // that Pet Shop Boys song
    }
    
    
    // check all the asteroids below AND ABOVE it, line by line:
    $north = array_reverse(array_slice($grid, 0, $y, true));
    $south = array_slice($grid, $y+1, null, true);
    
    $noso = array_merge($north, $south);
    // var_export($noso);die();
    foreach ($noso as $cy => $row) {
        foreach ($row as $cx => $char) {
            if (isset($blocked["$cx:$cy"])) continue; // when going through the x's on a line, Skip the ones in blocked
            if ($char === '#') {     // if there is an asteroid in the checked line, add it to the to-be-zap'd array
                $to_zap[] = ($cx - $x).':'.($cy - $y);
                // now find the blind spots behind that asteroid, and block them off
                list($dx, $dy) = reduce_fraction($cx - $x, $cy - $y);
                if ($dx == 0) $dy = 1;
                if ($dy == 0) $dx = 1;
                $nx = $cx + $dx;
                $ny = $cy + $dy;
                while($nx >= 0 and $nx < $width && $ny >= 0 and $ny < $height) {
                    $blocked["$nx:$ny"] = 1;
                    $nx += $dx;
                    $ny += $dy;
                }
            }
        }
    }
    
    var_export($to_zap);
    // die("\nthis is unsorted");
    usort($to_zap, 'by_angle');
    var_export($to_zap);
    die("this is sorted MAYBE the right way");
    
    $n++; // <-- move this line to where an asteroid gets zapped
}

function by_angle($a, $b) {
    list($ax, $ay) = explode(':', $a);
    list($bx, $by) = explode(':', $b);
    $adeg = return_degrees($ax, -$ay);
    $bdeg = return_degrees($bx, -$by);
    return $adeg > $bdeg;
}

// var_export($asteroids);die();
$max = 0;
foreach ($asteroids as $key => $ast) {
    $c = count($ast->sees);
    if ($c > $max) {
        $high = $key;
        $max = $c;
    }
}
echo "Output: $high: ".count($asteroids[$high]->sees);

?>