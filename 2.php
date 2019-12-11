<pre>
<?php
require_once 'lib.php';
require_once 'intcode.class.php';

echo time_taken();

// Intcode Instructions
$one = function($obj) {
    $address_1      = $obj->memory[$obj->instruction_pointer+1];
    $address_2      = $obj->memory[$obj->instruction_pointer+2];
    $output_address = $obj->memory[$obj->instruction_pointer+3];
    $obj->memory[$output_address] = $obj->memory[$address_1] + $obj->memory[$address_2];
    $obj->instruction_pointer += 4;
};

$two = function($obj) {
    $address_1      = $obj->memory[$obj->instruction_pointer+1];
    $address_2      = $obj->memory[$obj->instruction_pointer+2];
    $output_address = $obj->memory[$obj->instruction_pointer+3];
    $obj->memory[$output_address] = $obj->memory[$address_1] * $obj->memory[$address_2];
    $obj->instruction_pointer += 4;
};

$ninenine = function($obj) {
    return 'halt';
};

// part 1
$c = new IntCode('2.txt');
$c->load_instruction(1, $one);
$c->load_instruction(2, $two);
$c->load_instruction(99, $ninenine);
$c->set_memory_values([1=>12, 2=>2]);
$output = $c->run();
echo "Output: {$c->memory[0]}\n";
echo time_taken()."\n";

die();

// part 2
$c = new IntCode('2.txt');
$c->load_instruction(1, $one);
$c->load_instruction(2, $two);
$c->load_instruction(99, $ninenine);
$memory = $c->memory; // store original
for ($i=0; $i <= 99; $i++) {
    for ($j=0; $j <= 99; $j++) { 
        $c->set_memory_values($memory);
        $c->set_memory_values([1=>$i, 2=>$j]);
        $output = $c->run();
        $address0 = $c->memory[0];
        if ($address0 == 19690720) {
            echo "Output: noun $i, verb $j ".(100 * $i + $j)."\n";
            echo time_taken();
            die();
        }
    }
}

?>