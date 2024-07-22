<?php
function handleValidation($validation_type, $body) {

    if ($validation_type === 'cliente'){
        $cedula_rif = $body['cedula_rif'];
        $nombre = $body['nombre'];
        $telefono = $body['telefono'];

        // Validate cedula_rif
        if (!preg_match('/^(V|J|G|E|R|P)\d{7,9}$/', $cedula_rif)) {
            return "Cédula/RIF inválida. Ejemplo: V23546975";
        }

        // Validate nombre
        if (!preg_match('/^[a-zA-Z\s]+$/', $nombre)) {
            return "Nombre inválido.";
        }

        // Validate telefono
        if (!preg_match('/^\d+$/', $telefono)) {
            return "Teléfono inválido. Ejemplo: 04249787816";
        } else {
            return "";
        }

    }
     
}
function stripDecimal($number) {
    // Convert the number to a string
    $numberStr = (string)$number;
    // Find the position of the decimal point
    $decimalPos = strpos($numberStr, '.');
    // If there is no decimal point, return the original number
    if ($decimalPos === false) {
        return $numberStr;
    }
    // Extract the part before the decimal point
    $beforeDecimal = substr($numberStr, 0, $decimalPos);
    // Extract the part after the decimal point
    $afterDecimal = substr($numberStr, $decimalPos + 1, 2);
    // Combine the parts back together
    $strippedNumber = $beforeDecimal . '.' . $afterDecimal;
    
    return $strippedNumber;
}

?>
