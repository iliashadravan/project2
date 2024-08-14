
<?php

function request($field)
{
    return isset($_REQUEST[$field]) && $_REQUEST[$field] != "" ? trim($_REQUEST[$field]) : null;
}

function has_error($field)
{
    global $errors;
    return isset($errors[$field]);
}

function get_error($field)
{
    global $errors;
    return has_error($field) ? $errors[$field] : null;
}
?>
