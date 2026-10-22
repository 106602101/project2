<?php
// Sanitises a single value OR an entire array 

function sanitise($input) {

    $output = is_array($input) 
    // Allows for nested arrays by including the function inside itself
        ? array_map("sanitise", $input) 
        : clean_value($input);

    return $output;
}

// Validators 

function is_valid_job_ref($ref_number) {
    return preg_match('/^[A-Z0-9]{6}$/D', $ref_number);
}

function is_valid_name($name, $max_length = 20) {
    $name = trim($name);
    return preg_match("/^[A-Za-z' -]+$/", $name) === 1
        && strlen($name) <= $max_length;
}

function is_valid_date() {

}

function is_in_list() {

}

function is_valid_postcode() {

}

function is_valid_email() {

}

function is_valid_phone() {

}

// INTERNAL HELPERS: Used only by functions above.
// Other pages should only use the public functions above.

function clean_value($value) {
    $value = (string) $value;
    $value = trim($value);
    $value = stripslashes($value);
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}