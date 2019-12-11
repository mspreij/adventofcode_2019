<pre><?php
require_once 'lib.php';
require_once 'intcode.class.php';


// Intcode Instructions
$one = function($obj) {
    $p_modes = $obj->get_p_modes();
    $value_1 = $obj->get_memory_value($obj->instruction_pointer+1, $p_modes[0]);
    $value_2 = $obj->get_memory_value($obj->instruction_pointer+2, $p_modes[1]);
    
    $output_address = $obj->memory[$obj->instruction_pointer+3];
    $obj->memory[$output_address] = $value_1 + $value_2;
    $obj->instruction_pointer += 4;
};

$two = function($obj) {
    $p_modes = $obj->get_p_modes();
    $value_1 = $obj->get_memory_value($obj->instruction_pointer+1, $p_modes[0]);
    $value_2 = $obj->get_memory_value($obj->instruction_pointer+2, $p_modes[1]);
    
    $output_address = $obj->memory[$obj->instruction_pointer+3];
    $obj->memory[$output_address] = $value_1 * $value_2;
    $obj->instruction_pointer += 4;
};

$input_value = function($obj) {
    $input = $obj->get_input();
    // no parameter modes for only writing
    $output_address = $obj->memory[$obj->instruction_pointer+1];
    $obj->memory[$output_address] = $input;
    $obj->instruction_pointer += 2;
};

$output_value = function($obj) {
    $p_modes = $obj->get_p_modes();
    $value = $obj->get_memory_value($obj->instruction_pointer+1, $p_modes[0]);
    $obj->send_output($value);
    $obj->instruction_pointer += 2;
};

$jump_if_true = function($obj)
{
    $p_modes = $obj->get_p_modes();
    $value_1 = $obj->get_memory_value($obj->instruction_pointer+1, $p_modes[0]);
    if ($value_1 != 0) {
        $obj->instruction_pointer = $obj->get_memory_value($obj->instruction_pointer+2, $p_modes[1]);
    }else{
        $obj->instruction_pointer += 3;
    }
};

$jump_if_false = function($obj)
{
    $p_modes = $obj->get_p_modes();
    $value_1 = $obj->get_memory_value($obj->instruction_pointer+1, $p_modes[0]);
    if ($value_1 == 0) {
        $obj->instruction_pointer = $obj->get_memory_value($obj->instruction_pointer+2, $p_modes[1]);
    }else{
        $obj->instruction_pointer += 3;
    }
};

$less_than = function($obj) {
    $p_modes = $obj->get_p_modes();
    $value_1 = $obj->get_memory_value($obj->instruction_pointer+1, $p_modes[0]);
    $value_2 = $obj->get_memory_value($obj->instruction_pointer+2, $p_modes[1]);
    if ($p_modes[2] != 0) throw new Exception("instruction less_than: parameter 3 mode should be 0!!!", 1);
    $output_address = $obj->memory[$obj->instruction_pointer+3];
    $obj->set_memory_value($output_address, (int) ($value_1 < $value_2));
    $obj->instruction_pointer += 4;
};

$equals = function($obj) {
    $p_modes = $obj->get_p_modes();
    $value_1 = $obj->get_memory_value($obj->instruction_pointer+1, $p_modes[0]);
    $value_2 = $obj->get_memory_value($obj->instruction_pointer+2, $p_modes[1]);
    if ($p_modes[2] != 0) throw new Exception("instruction equals: parameter 3 mode should be 0!!!", 1);
    $output_address = $obj->memory[$obj->instruction_pointer+3];
    $obj->set_memory_value($output_address, (int) ($value_1 == $value_2));
    $obj->instruction_pointer += 4;
};

$ninenine = function($obj) {
    return 'halt';
};

echo time_taken()."\n";
$c = new IntCode('5.txt');

$c->load_instruction(1, $one);
$c->load_instruction(2, $two);
$c->load_instruction(3, $input_value);
$c->load_instruction(4, $output_value);
$c->load_instruction(5, $jump_if_true);
$c->load_instruction(6, $jump_if_false);
$c->load_instruction(7, $less_than);
$c->load_instruction(8, $equals);
$c->load_instruction(99, $ninenine);


try {
    $c->input = 5;
    $results = $c->run();
} catch (Exception $e) {
    echo '>>>>> '. $e->getMessage()."\n";
    die();
}

echo "Output: ". join(':', $results)."\n";
echo time_taken()."\n";

?>