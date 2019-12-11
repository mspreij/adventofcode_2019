<?php
$things = explode("\n", file_get_contents('1.txt'));

// part one
// $sum = 0;
// foreach ($things as $thing) {
//     $sum += floor($thing/3)-2;
// }
// echo $sum;


// part 2
$sum = 0;
foreach ($things as $thing) {
    $thing_fuel = $thing;
    while ($thing_fuel >= 9) {
        $thing_fuel = floor($thing_fuel/3)-2;
        $sum += $thing_fuel;
    }
}
echo $sum;



?>