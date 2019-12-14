<pre>
<?php
require_once 'lib.php';
$input = trim(file_get_contents('10.txt'));

$input =
".#..#
.....
#####
....#
...##";

foreach (explode("\n", $input) as $y => $line) {
    foreach (str_split($line) as $x => $char) {
        // who knows, we might need this ¯\_(ツ)_/¯
        $grid[$y][$x] = $char;
        // make a list of asteroids anyway, we'll probably need those
        if ($char === '#') {
            $asteroids["$x:$y"] = ['sees'=>[], 'blocked'=>[]];
        }
    }
}


$width = count($grid[0]);
$height = count($grid);

foreach ($asteroids as $Ast_key => $Ast) {
    list($x, $y) = explode(':', $Ast_key);
    // on its own line ($y), the first asteroid on its right it can see
    $nx = $x + 1;
    while ($nx < $width) {
        if ($grid[$y][$nx] === '#') {
            $Ast['sees'][] = "$nx:$y"; // add that to sees
            $asteroids["$nx:$y"]['sees'][] = $Ast_key; // and add A to its >sees
            break; // then the rest is blocked and can be ignored
        }
        $nx++;
    }
    // check all the asteroids below them, line by line:
    foreach (array_slice($grid, $y+1, null, true) as $cy => $row) {
        foreach ($row as $cx => $char) {
            if (isset($Ast['blocked']["$cx:$cy"])) continue; // when going through the x's on a line, Skip the ones in A->blocked
            if ($char === '#') {                           // if there is an asteroid in the checked line, you can see it!
                $Ast['sees'][] = "$cx:$cy";                 // Add it to A->sees
                $asteroids["$cx:$cy"]['sees'][] = $Ast_key; // add yourself to its >sees
                // now find the blind spots behind that asteroid, and block them off
                list($dx, $dy) = reduce_fraction($cx - $x, $cy - $y);
                $nx = $cx + $dx;
                $ny = $cy + $dy;
                while($nx >= 0 and $nx < $width && $ny >= 0 and $ny < $height) {
                    $Ast['blocked']["$nx:$ny"] = 1;
                    $nx += $dx;
                    $ny += $dy;
                }
            }
        }
    }
    $asteroids[$Ast_key] = $Ast;
}

var_export($asteroids);die();
$max = 0;
foreach ($asteroids as $key => $ast) {
    $c = count($ast['sees']);
    if ($c > $max) {
        $high = $key;
        $max = $c;
    }
}
var_export($key);

/*

Objective: find asteroid which can see the most other asteroids. When 3 asteroids are exactly on the same line (any angle), the first can't see the last.

How to get there: find the number of asteroids each asteroid can see.
Give an asteroid a "sees" array which stores other asteroids it can (yes) see, and a "blocked" array which will contain coordinates exactly behind the asteroids it can see.

Notes:
- if it turns out A can see B, B can see A. Store that in B's >sees list too while scoring A.
- therefore you can get away with only checking the asteroids 'south' of the current one, if going from north to south (and on its own line, only east is interesting)
- an asteroid at [x,y] can see all asteroids on [x-1,y], [x+1,y], [x,y-1] and [x,y+1]. not sure this is really useful /yet/.

So.
v run through each asteroid A from north to south:
  v on its own line ($y), the first on $x+ it can see, add that (and add A to its >sees), then the rest is blocked and can be ignored
  - check all the asteroids below them, line by line:
    - when going through the x's on a line, Skip the ones in A->blocked. this can be true for the second and lower lines under A.
    - "A? if there is an asteroid in the checked line, YOU CAN SEE IT."
      - Add it to A->sees
      - add A to its >sees
      - now for the fun bit that makes the above true: figure out the x/y from A (current) to B, say it's relative -3/2. This means it can also Not see the block at -6/4. Buuuut, if the x/y was say 8/6, this doesn't just rule out 16/12 but also 12/9, so figure out the smallest step that gets you there (AKA reducing/simplifying fractions) and increment with that, until its x or y is outside the area range, to block out locations (A->blocked[] = those x/y's)

*/

?>