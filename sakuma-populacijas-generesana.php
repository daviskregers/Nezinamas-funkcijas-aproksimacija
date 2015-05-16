<?php
require 'vendor/autoload.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Dāvis Krēgers &mdash; REGRESIJAS UZDEVUMS</title>
</head>
<body>

<?php

$used = array();
global $funkciju_kopa, $funkciju_kopa_keys;
$funkciju_kopa = ['+' => 2, '-' => 2, '*' => 2, '/' => 2, 'sin' => 1, 'cos' => 1, 'exp' => 1];
$funkciju_kopa_keys = array_keys($funkciju_kopa);
$individi = 6;
$max_garums = array(2, 4);
$individi_masivs = array();

class Field_calculate {
    const PATTERN = '/(?:\-?\d+(?:\.?\d+)?[\+\-\*\/])+\-?\d+(?:\.?\d+)?/';

    const PARENTHESIS_DEPTH = 10;

    public function calculate($input){
        if(strpos($input, '+') != null || strpos($input, '-') != null || strpos($input, '/') != null || strpos($input, '*') != null){
            //  Remove white spaces and invalid math chars
            $input = str_replace(',', '.', $input);
            $input = preg_replace('[^0-9\.\+\-\*\/\(\)]', '', $input);

            //  Calculate each of the parenthesis from the top
            $i = 0;
            while(strpos($input, '(') || strpos($input, ')')){
                $input = preg_replace_callback('/\(([^\(\)]+)\)/', 'self::callback', $input);

                $i++;
                if($i > self::PARENTHESIS_DEPTH){
                    break;
                }
            }

            //  Calculate the result
            if(preg_match(self::PATTERN, $input, $match)){
                return $this->compute($match[0]);
            }

            return 0;
        }

        return $input;
    }

    private function compute($input){
        $compute = create_function('', 'return '.$input.';');

        return 0 + $compute();
    }

    private function callback($input){
        if(is_numeric($input[1])){
            return $input[1];
        }
        elseif(preg_match(self::PATTERN, $input[1], $match)){
            return $this->compute($match[0]);
        }

        return 0;
    }
}

function generate_tree($length = [2,4], $level = 0, $operators = 2) {
    global $funkciju_kopa, $funkciju_kopa_keys;


    if(is_array($length)) {
        $length2 = rand($length[0], $length[1]);
    }


    $builder = array();
    $rand = 4;
    if($level == 0) {
        $builder['value'] = $funkciju_kopa_keys[rand(0, count($funkciju_kopa_keys) -1)];
        $builder['children'] = generate_tree($length, $level + 1, $funkciju_kopa[$builder['value']]);
    }
    elseif($rand == 0 && $level < $length2) {
        $builder['value'] = $funkciju_kopa_keys[rand(0, count($funkciju_kopa_keys) -1)];
        $builder['children'][0] = generate_tree($length, $level + 1, $funkciju_kopa[$builder['value']]);
        if($funkciju_kopa[$builder['value']] == 2)
            $builder['children'][1] = generate_tree($length, $level + 1, $funkciju_kopa[$builder['value']]);
    }
    else { 
        $rand1 =  rand(0,1);
        $rand2 =  rand(0,1);

        if($rand1 == 0 && $level < $length2 || $level < $length[0]) {
            $builder[0]['value'] = $funkciju_kopa_keys[rand(0, count($funkciju_kopa_keys) -1)];
            $builder[0]['children'] = generate_tree($length, $level + 1, $funkciju_kopa[$builder[0]['value']]);
        }
        else {
            $builder[0]['value'] = 'X';
        }

        if(($rand2 == 0 && $level < $length2 || $level < $length[0]) && $operators == 2) {
            $builder[1]['value'] = $funkciju_kopa_keys[rand(0, count($funkciju_kopa_keys) -1)];
            $builder[1]['children'] = generate_tree($length, $level + 1, $funkciju_kopa[$builder[1]['value']]);
        }
        elseif($operators == 2) {
            $builder[1]['value'] = 'X';
        }
    }

    return $builder;
}


echo "\$sakuma_populacija = array(<br />";
for($i = 0; $i < $individi; $i++) {
$tree = generate_tree();
echo var_export($tree);
echo ",<Br />";
}
echo ");<br />"
// calculate_tree($tree, 5);
?>

</body>
</html>
