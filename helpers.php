<?php

/**
 * Vairāku mērķu optimizācijas uzdevuma palīgfunkcijas
 *
 * PHP version 5
 *
 * @author     Dāvis Krēgers <davis@image.lv>
 * @copyright  2015 Dāvis Krēgers
 * @license    https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0 Universal (CC0 1.0) 
 * @version    SVN: $Id$
 * @link       http://faili.deiveris.lv/genetiskais-algoritms1/
 */

// Noapaļo skaitli līdz 0.1 precizitātei
function skaitlis($val) {
	return number_format((float) $val, 3, '.', ''); 
}

// Noapaļo skaitli līdz .001 precizitātei
function prbsk($val) {
	return number_format((float) $val, 3, '.', ''); 
}

// Pārveido skaitli uz binārās vērtības skalu
function real2bin($val) {
	global $intv, $dalsk;
	return ($val - $intv[0])*((pow(2, $dalsk)-1)/($intv[1]-$intv[0]));
}

// Pārveido bināro vērtību uz reālu skaitli
function bin2real($val) {
	global $intv, $dalsk;
	return $intv[0] + $val * (($intv[1]-$intv[0])/(pow(2,$dalsk)-1));
}

function render_tree($tree, $echo = true) {
    $return = "";
    $operator = $tree['value'];

    if(isset($tree['children']) && count($tree['children']) == 2) {

        if($tree['children'][0]['value'] == 'X') $return .= '(X)';
        else $return .= "(".render_tree($tree['children'][0]).")";

        $return .= $operator;

        if($tree['children'][1]['value'] == 'X') $return .= '(X)';
        else $return .= "(".render_tree($tree['children'][1]).")";

    }
    elseif(isset($tree['children']) && count($tree['children']) == 1) { 

        $return .= $operator;

        if($tree['children'][0]['value'] == 'X') $return .= '(X)';
        else $return .= "(".render_tree($tree['children'][0]).")";

    }

   return $return;
}

function calculate_tree($tree, $x) {
    $tree = render_tree($tree);
    $tree = str_replace('X', $x, $tree);
    $result = $p = eval('return '.$tree.';');
    return $result;
}

class Counter {
    private $c = 0;
    public function next_id() {
        return ++$this->c;
    }
    public function get() {
        return $this->c;
    }
}


function draw_tree($objTree, $tree, $c_id, $parent = 0) {
    //add nodes to the tree, parameters: id, parentid optional text, width, height, image(path)
    if($c_id->get() == 0 ) $c_id->next_id();

    $objTree->add($c_id->get(), $parent, $tree['value'], 100);

    $parent = $c_id->get();
    $c_id->next_id();

    if(isset($tree['children']) && count($tree['children']) == 2) {

        if($tree['children'][0]['value'] == 'X') $objTree->add($c_id->get(), $parent, $tree['children'][0]['value'], 100);
        else $objTree = draw_tree($objTree, $tree['children'][0], $c_id, $parent); 

        $c_id->next_id();

        if($tree['children'][1]['value'] == 'X') $objTree->add($c_id->get(), $parent, $tree['children'][1]['value'], 100);
        else $objTree = draw_tree($objTree, $tree['children'][1], $c_id, $parent); 
    }
    elseif(isset($tree['children']) && count($tree['children']) == 1) { 

        if($tree['children'][0]['value'] == 'X') $objTree->add($c_id->get(), $parent, $tree['children'][0]['value'], 100);
        else $objTree = draw_tree($objTree, $tree['children'][0], $c_id, $parent); 

    }

   return $objTree;

}


function count_branches($array, $count = -1) {
    $starting_count = $count;

    if(is_array($array)) {

        $count++;

        if(isset($array['children'])) {
            $sum = 0;
            foreach($array['children'] as $child) {
                $sum += count_branches($child, 0);
            }
            return $count + $sum;
       }
       else {
            return $count;
       }

    }
    
    return $count;
}

function get_branch($array, $get, $count = -1, $total = 0) {
   
    if(is_array($array)) {

        $count++;

        if($total == $get) return array(false, $array);

        if(isset($array['children'])) {
            $sum = 0;
            foreach($array['children'] as $child) {
               
               $total++;

                $return = get_branch($child, $get, 0, $total);

                if($return[0] == false) return $return;
                else {
                    $sum += $return[0];
                    $total = $return[1];
                }

            }

            return array($count + $sum, $total);
       }
       else {
            return array($count, $total);
       }

    }
    
    return $count;
}

function update_branch($array, $get, $update, $count = -1, $total = 0) {
   
    if(is_array($array)) {

        $count++;

        if($total == $get) {
            return array(false, $update);
        }

        if(isset($array['children'])) {
            $sum = 0;
            foreach($array['children'] as $key => $child) {
               
               $total++;

                $return = update_branch($child, $get, $update, 0, $total);

                if($return[0] == false) {
                    $array['children'][$key] = $return[1];
                    return array(false, $array);
                }
                else {
                    $sum += $return[0];
                    $total = $return[1];
                }

            }

            return array($count + $sum, $total);
       }
       else {
            return array($count, $total);
       }

    }
    
    return $count;
}

function koka_krustosana($krustosanas_punkts1, $krustosanas_punkts2, $merkis, $otrs_koks) {

    $first = get_branch($merkis, $krustosanas_punkts1);
    $second = get_branch($otrs_koks, $krustosanas_punkts2);

    $temp = update_branch($merkis, $krustosanas_punkts1, $second[1]);
    $second = update_branch($otrs_koks, $krustosanas_punkts2, $first[1]);

    return array($temp[1], $second[1]);
}

// 11, 7, 11, 7, 3, 6

// X -> [x -> [x, x], x] 