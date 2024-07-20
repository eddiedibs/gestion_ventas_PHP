<?php
function handleValidation($validation_type, $body) {

    if ($validation_type === 'cliente'){
        $cedula_rif = $body['cedula_rif'];
        $nombre = $body['nombre'];
        $telefono = $body['telefono'];

        // Validate cedula_rif
        if (!preg_match('/^(V|J|G|E|R|P)\d{7,9}$/', $cedula_rif)) {
            return "Cédula/RIF inválida.";
        }

        // Validate nombre
        if (!preg_match('/^[a-zA-Z\s]+$/', $nombre)) {
            return "Nombre inválido.";
        }

        // Validate telefono
        if (!preg_match('/^\d+$/', $telefono)) {
            return "Teléfono inválido.";
        } else {
            return "";
        }

    }

     
}
?>
