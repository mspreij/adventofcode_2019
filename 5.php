<pre><?php
require_once 'lib.php';
require_once 'intcode.class.php';


// Intcode Instructions
$one = function($obj) {
    $p_modes = $obj->get_p_modes();
    
    $address_1 = $obj->memory[$obj->instruction_pointer+1];
    $value_1 = $p_modes[0] ? $address_1 : $obj->memory[$address_1];
    $address_2 = $obj->memory[$obj->instruction_pointer+2];
    $value_2 = $p_modes[1] ? $address_2 : $obj->memory[$address_2];
    
    $output_address = $obj->memory[$obj->instruction_pointer+3];
    $obj->memory[$output_address] = $value_1 + $value_2;
    $obj->instruction_pointer += 4;
};

$two = function($obj) {
    $p_modes = $obj->get_p_modes();
    
    $address_1 = $obj->memory[$obj->instruction_pointer+1];
    $value_1 = $p_modes[0] ? $address_1 : $obj->memory[$address_1];
    $address_2 = $obj->memory[$obj->instruction_pointer+2];
    $value_2 = $p_modes[1] ? $address_2 : $obj->memory[$address_2];
    
    $output_address = $obj->memory[$obj->instruction_pointer+3];
    $obj->memory[$output_address] = $value_1 * $value_2;
    $obj->instruction_pointer += 4;
};

$three = function($obj) {
    $input = $obj->get_input();
    // no parameter modes for only writing
    $output_address = $obj->memory[$obj->instruction_pointer+1];
    $obj->memory[$output_address] = $input;
    $obj->instruction_pointer += 2;
};

$four = function($obj) {
    // parameter modes?
    $address_1 = $obj->memory[$obj->instruction_pointer+1];
    $obj->send_output($obj->memory[$address_1]);
    $obj->instruction_pointer += 2;
};

$ninenine = function($obj) {
    return 'halt';
};

// part 1
echo time_taken()."\n";
$c = new IntCode('5.txt');

$c->load_instruction(1, $one);
$c->load_instruction(2, $two);
$c->load_instruction(3, $three);
$c->load_instruction(4, $four);
$c->load_instruction(99, $ninenine);

$c->input = 1;

$result = $c->run();
echo "Output: ".var_export($result);
echo time_taken()."\n";

?>