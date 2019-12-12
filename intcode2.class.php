<?php

class Intcode2
{
    
    /** -- Methods -----------------------
     *                                            
     * __construct($file='')                      -- loads file (if any) into memory
     * init($program='', $input=0)                -- nice in loops: re-sets program from file or string, sets input, re-sets output,
                                                       re-sets instruction_pointer
     * run()                                      -- sets instruction_pointer to 0, runs program. returns $output array after halt
     * set_memory_values(array $pairs)            -- [over]writes specific memory addresses with values
     * set_memory_value($address, $value)         -- [re-]sets a value at a memory address
     * get_memory_value($address, $mode=0)        -- get value from memory taking parameter mode into account:
                                                        For memory [3, 8, 4, 7, 5, 1, 13, 9, 12]
                                                        - 0 / positional is the value of the address at that address: get_memory_value(3, 0) => 9
                                                        - 1 / immediate  is the value at that address:                get_memory_value(3, 1) => 7
     * get_p_modes()                              -- returns array where [0] is the parameter mode for the first argument of current instruction,
                                                       [1] for second etc.
     * get_input()                                
     * send_output($value='')                     -- stores input in ->output array.
     * load_file($file)                           -- used by construct, reads in program file and calls >load_data
     * load_data($data)                           -- called by >load_file, accepts string data, sets memory
     * load_instruction($opcode, $instruction)    -- each instruction must accept an Intcode object as parameter
     * 
     * 
    **/
    
    
    public $name = '';
    public $names = ['Alice', 'Bob', 'Carol', 'David', 'Erin', 'Frank', 'Grace', 'Harry', 'Iris', 'John', 'Kayleigh', 'Luke'];
    public $instruction_pointer = 0;
    public $memory = [];
    public $input  = [];
    public $output = [];
    public $status = 'init';
    public $status_list = ['init', 'running', 'waiting', 'halt'];
    public $output_target;
    protected $relative_base = 0; // https://adventofcode.com/2019/day/9
    protected $instructions = [];
    
    public $debug = 0;
    
    // Construct
    public function __construct($file='')
    {
        $this->get_name();
        $this->set_instructions();
        if (strlen($file)) {
            $this->load_file($file);
        }
    }
    
    protected function get_name()
    {
        static $counter = 0;
        $this->name = $this->names[$counter];
        $counter++;
    }
    
    public function init($program='', $input=0)
    {
        $this->debug('init');
        if (file_exists($program)) {
            $this->load_file($program);
        }elseif (strlen($program)){
            $this->load_data($program);
        }
        $this->input   = (array) $input;
        $this->output  = [];
        $this->instruction_pointer = 0; // for good measure
        $this->status  = 'init';
    }
    
    // --- run ---
    function run()
    {
        if (! count($this->memory)) throw new Exception("Program missing", 1);
        if ($this->status === 'init') {
            $this->instruction_pointer = 0; // init
            $this->debug('starting run');
            $this->status = 'running';
        }
        while (1 and $this->status != 'halt') {
            $value = $this->memory[$this->instruction_pointer];
            $opcode = (int) substr($value, -2);
            // echo "$opcode\n";
            // possible to set parameter modes here, too
            if (isset($this->instructions[$opcode])) {
                $result = $this->instructions[$opcode]();
            }else{
                throw new Exception("unknown instruction: $opcode", 1);
            }
            if ($result === 'halt') {
                $this->debug('run: halt (break)');
                break;
            }
        }
        // $this->debug('-'); // this shows the callstack at the end..
        return $this->output;
    }
    
    // --- set_memory_values ---
    public function set_memory_values(array $pairs)
    {
        foreach ($pairs as $address => $value) {
            $this->memory[$address] = $value;
        }
    }
    
    // --- set_memory_value ---
    public function set_memory_value($address, $value)
    {
        $this->memory[$address] = $value;
    }
    
    public function get_memory_value($address, $mode=0)
    {
        $value = $this->memory[$address];
        switch ($mode) {
            case 0:
                // mode 0: positional mode
                return isset($this->memory[$value]) ? $this->memory[$value] : 0;
                break;
            case 1:
                // mode 1: immediate mode
                return $value;
                break;
            case 2:
                // mode 2: relative mode
                $index = $value + $this->relative_base;
                return isset($this->memory[$index]) ? $this->memory[$index] : 0;
                break;
            default:
                // mode wtf: wtf mode?
                throw new Exception("get_memory_value: wtf kind of mode is ".var_export($mode, 1).'?', 1);
        }
    }
    
    // --- parameter modes ---
    public function get_p_modes()
    {
        $val = $this->memory[$this->instruction_pointer];
        $res = array_map('intval', str_split(strrev(str_pad(substr($val, 0, -2), 3, '0', STR_PAD_LEFT))));
        $this->debug("get_p_modes: ".join(',', $res));
        return $res;
    }
    
    // --- I/O ---
    // this mainly exists so the last computer knows not to throw away the output if there's no taker
    public function accepting_input()
    {
        $accepting_statuses = ['init', 'running', 'waiting'];
        return in_array($this->status, $accepting_statuses);
    }
    
    public function store_input($value)
    {
        $this->input[] = $value;
    }
    
    public function get_input()
    {
        if (count($this->input) === 0) throw new Exception("I need input! :-(", 1);
        $res = array_shift($this->input);
        $this->debug('get_input: found '. var_export($res, 1));
        return $res;
    }
    
    public function send_output($value='')
    {
        // see if target is accepting input. or waiting for input. or even running. or at least like not halted?
        $class = get_class($this);
        if ($this->output_target instanceof $class) {
            if ($this->output_target->accepting_input()) {
                $this->output_target->store_input($value);
                $this->output_target->run();
                return;
            }
        }
        // store in own buffer.. who knows.
        $this->output[] = $value;
    }
    
    // --- load_file ---
    public function load_file($file)
    {
        $data = file_get_contents($file);
        return $this->load_data($data);
    }
    
    public function load_data($data)
    {
        $data = trim($data);
        if (! strlen($data)) throw new Exception("Program data empty", 1);
        $program = explode(',', $data);
        $this->memory = $program;
    }
    
    // --- load_instruction ---
    public function load_instruction($opcode, $instruction)
    {
        $this->instructions[$opcode] = $instruction;
    }
    
    protected function set_instructions()
    {
        $this->instructions = [
            
            // sum
            1 => function() {
                $p_modes = $this->get_p_modes();
                $value_1 = $this->get_memory_value($this->instruction_pointer+1, $p_modes[0]);
                $value_2 = $this->get_memory_value($this->instruction_pointer+2, $p_modes[1]);
                $output_address = $this->memory[$this->instruction_pointer+3];
                if ($p_modes[2] == 2) $output_address += $this->relative_base;
                $this->memory[$output_address] = $value_1 + $value_2;
                $this->debug("instr addition(1), val 1: $value_1, val 2: $value_2, output $output_address");
                $this->instruction_pointer += 4;
            },
            
            // product
            2 => function() {
                $p_modes = $this->get_p_modes();
                $value_1 = $this->get_memory_value($this->instruction_pointer+1, $p_modes[0]);
                $value_2 = $this->get_memory_value($this->instruction_pointer+2, $p_modes[1]);
                $output_address = $this->memory[$this->instruction_pointer+3];
                if ($p_modes[2] == 2) $output_address += $this->relative_base;
                $this->memory[$output_address] = $value_1 * $value_2;
                $this->debug("instr times(2), val 1: $value_1, val 2: $value_2, output $output_address");
                $this->instruction_pointer += 4;
            },
            
            // get input
            3 => function() {
                $p_modes = $this->get_p_modes();
                $input = $this->get_input();
                $output_address = $this->get_memory_value($this->instruction_pointer+1, $p_modes[0]);
                // if ($p_modes[0] == 2) $output_address += $this->relative_base; ^ same?
                // $output_address = $this->memory[$this->instruction_pointer+1];
                $this->memory[$output_address] = $input;
                $this->debug("instr input(3), val: $input, output address: $output_address");
                $this->instruction_pointer += 2;
            },
            
            // send output
            4 => function() {
                $p_modes = $this->get_p_modes();
                $value = $this->get_memory_value($this->instruction_pointer+1, $p_modes[0]);
                $this->debug("instr output(4), val: $value");
                $this->instruction_pointer += 2;
                $this->send_output($value);
            },
            
            // is not zero
            5 => function() {
                $p_modes = $this->get_p_modes();
                $value_1 = $this->get_memory_value($this->instruction_pointer+1, $p_modes[0]);
                if ($value_1 != 0) {
                    $this->instruction_pointer = $this->get_memory_value($this->instruction_pointer+2, $p_modes[1]);
                }else{
                    $this->instruction_pointer += 3;
                }
                $this->debug("instr jump_if_true(5), val: $value_1, next pointer: $this->instruction_pointer");
            },
            
            // is zero
            6 => function() {
                $p_modes = $this->get_p_modes();
                $value_1 = $this->get_memory_value($this->instruction_pointer+1, $p_modes[0]);
                if ($value_1 == 0) {
                    $this->instruction_pointer = $this->get_memory_value($this->instruction_pointer+2, $p_modes[1]);
                }else{
                    $this->instruction_pointer += 3;
                }
                $this->debug("instr jump_if_false(6), val: $value_1, next pointer: $this->instruction_pointer");
            },
            
            // less then
            7 => function() {
                $p_modes = $this->get_p_modes();
                $value_1 = $this->get_memory_value($this->instruction_pointer+1, $p_modes[0]);
                $value_2 = $this->get_memory_value($this->instruction_pointer+2, $p_modes[1]);
                // if ($p_modes[2] != 0) throw new Exception("instruction less_than: parameter 3 mode should be 0!!!", 1);
                $output_address = $this->memory[$this->instruction_pointer+3];
                if ($p_modes[2] == 2) $output_address += $this->relative_base;
                $this->debug("instr less_then(7), val 1: $value_1, val 2: $value_2, output $output_address");
                $this->set_memory_value($output_address, (int) ($value_1 < $value_2));
                $this->instruction_pointer += 4;
            },
            
            // equals
            8 => function() {
                $p_modes = $this->get_p_modes();
                $value_1 = $this->get_memory_value($this->instruction_pointer+1, $p_modes[0]);
                $value_2 = $this->get_memory_value($this->instruction_pointer+2, $p_modes[1]);
                // if ($p_modes[2] != 0) throw new Exception("instruction equals: parameter 3 mode should be 0!!!", 1);
                $output_address = $this->memory[$this->instruction_pointer+3];
                if ($p_modes[2] == 2) $output_address += $this->relative_base;
                $this->debug("instr equals(8), val 1: $value_1, val 2: $value_2, output $output_address");
                $this->set_memory_value($output_address, (int) ($value_1 == $value_2));
                $this->instruction_pointer += 4;
            },
            
            // adjust relative base
            9 => function() {
                $p_modes = $this->get_p_modes();
                $input = $this->get_memory_value($this->instruction_pointer+1, $p_modes[0]);
                // $input = $this->memory[$this->instruction_pointer+1];
                $this->relative_base += $input;
                $this->debug("instr adjust rel_base(9), val: $input, new relative base: $this->relative_base");
                $this->instruction_pointer += 2;
            },
            
            // halt
            99 => function() {
                $this->status = 'halt';
                return 'halt';
            },
            
        ];
    }
    
    protected function debug($msg)
    {
        if (! $this->debug) return;
        echo '['.microtime()."] $this->name - address $this->instruction_pointer: {$this->memory[$this->instruction_pointer]} > $msg\n";
    }
    
}
