<?php
$content = file_get_contents('model/crm_user.class.php');

// 1. In testAPI, remove lines between $return = array(); and echo "Working<pre>";
$pos1 = strpos($content, "public function testAPI(\$get){");
if ($pos1 !== false) {
    $pos_arr = strpos($content, "\$return = array();", $pos1);
    $pos_echo = strpos($content, "echo \"Working<pre>\";", $pos_arr);
    if ($pos_arr !== false && $pos_echo !== false) {
        $before = substr($content, 0, $pos_arr + strlen("\$return = array();\n"));
        $after = substr($content, $pos_echo);
        $content = $before . "\n\t\t" . $after;
        echo "Cleaned testAPI dead code.\n";
    }
}

// 2. In feasibility, remove the dead /** ... **/ block between "// End feasibilty part By Kote - 25-06-2025" and "// Feasibilty\n    public function getFeasiblityData($get){"
$target_comment = "// End feasibilty part By Kote - 25-06-2025";
$pos_feas = strpos($content, $target_comment);
if ($pos_feas !== false) {
    $pos_active = strpos($content, "public function getFeasiblityData(\$get){", $pos_feas);
    if ($pos_active !== false) {
        $before = substr($content, 0, $pos_feas + strlen($target_comment) . "\n\n");
        $after = substr($content, $pos_active);
        $content = $before . "\n    " . $after;
        echo "Cleaned dead feasibility comments.\n";
    }
}

file_put_contents('model/crm_user.class.php', $content);
