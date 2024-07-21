<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'validator_handler.php';
    $nombre = $_POST["nombre"];
    $cedula_rif = $_POST['cedula_rif'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    $data_to_validate = array(
        "nombre" => $nombre,
        "cedula_rif" => $cedula_rif,
        "telefono" => $telefono,
    );


    $error_msg = handleValidation("cliente", $data_to_validate);
    if ($error_msg != ""){
        $return_data = json_encode(array("status" => "failed", "message" => $error_msg));
        die($return_data);
    };
    try {
        require_once 'conexion.php'; 
        $stmt = makeQuery($pdo, 
                "INSERT INTO clientes (nombre, cedula_rif, telefono, direccion) VALUES (?,?,?,?);", 
                [$nombre,$cedula_rif,$telefono,$direccion]);
        $cliente_id = $pdo->lastInsertId();

        header('Content-Type: application/json');
        $return_data = json_encode(array(
            "status" => "success",
            "message" => "Cliente '$cedula_rif' creado exitosamente."
        ));
        // Create new cart
        makeQuery($pdo, 
                "INSERT INTO carritos (cliente_id) VALUES (?)",
                [$cliente_id]);
        $carrito_id = $pdo->lastInsertId();
        $_SESSION['cliente_id'] = $cliente_id; // Assume cliente_id is stored in session
        $_SESSION['carrito_id'] = $carrito_id; // Assume cliente_id is stored in session
        $_SESSION['cedula_rif'] = $cedula_rif;
        $pdo = null;
        $stmt = null;
        die($return_data);
        // header("Location: ./index.php");

    } catch (PDOException $e){
        require_once 'error_handler.php'; 
        $error_msg = handleError($e->getCode(), $e->getMessage());
        $return_data = json_encode(array("status" => "failed", "message" => $error_msg));
        die($return_data);
    }


} else{
    header("Location: ./index.php");

}
?>