<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function handleError($errorCode, $errorMessage) {

    $message = "";
    // Switch statement to handle different error codes
    switch ($errorCode) {
        case '23000':
            $pattern = "/'([^']+)'/";
            if (preg_match($pattern, $errorMessage, $matches)) {
                // $matches[1] contains the value between the single quotes
                $captured_value = $matches[1];
                $message .= "El registro $captured_value esta duplicado.";
                break;
            } else {
                $message .= "Existe un registro duplicado o inexistente.";
                break;
            }

        default:
            $message .= $errorCode." ".$errorMessage;
            break;
    }

    // Return the formatted error message
    return $message;
}
?>