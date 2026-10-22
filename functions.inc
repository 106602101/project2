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
    if (!is_string($ref_number)) {
        return false;
    }
    return preg_match('/^[A-Z0-9]{6}$/D', $ref_number);
}

function is_valid_name($name, $max_length = 20) {
    if (!is_string($name)) {
        return false;
    }
    $name = trim($name);
    return preg_match("/^[A-Za-z' -]+$/", $name) === 1
        && strlen($name) <= $max_length;
}

function is_valid_date($date) {
    if (!is_string($date)){
        return false;
    }
    $date = trim($date);
    if (preg_match("/^(\d{2})\/(\d{2})\/(\d{4})$/", $date, $parts) !== 1) {
        return false;
    }
    return checkdate((int) $parts[2], (int) $parts[1], (int) $parts[3]);
}

function is_in_list($value, $allowed) {
    if (!is_string($value)) {
        return false;
    }
    return in_array(trim($value), $allowed, true);
}

function is_valid_postcode($postcode) {
    if (!is_string($postcode)) {
        return false;
    }
    return preg_match("/^\d{4}$/", trim($postcode)) === 1;
}

function is_valid_email($email) {
    if (!is_string($email)) {
        return false;
    }
    $email = trim($email);
    return strlen($email) <= 254
        && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
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