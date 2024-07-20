<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    try {
        require_once 'conexion.php'; 
        $cedula_rif = $_GET['cedula_rif']; // Make sure to validate/sanitize this input        $query = "SELECT * FROM productos;";
        $cliente_info_stmt = makeQuery($pdo, 
                        "SELECT * FROM clientes WHERE cedula_rif = ?",
                        [$cedula_rif]);
        
        $cliente_info = $cliente_info_stmt->fetch(PDO::FETCH_ASSOC);
        // Check if $cliente_info is false
        if (!$cliente_info) {
            $return_data = json_encode(array(
                "status" => "failed",
                "message" => "El cliente con numero de documento ".$cedula_rif." no se encuentra registrado.",
            ));
            die($return_data);
        }else{
            header('Content-Type: application/json');
            $return_data = json_encode(array(
                "status" => "success",
                "message" => "El cliente con numero de documento ".$cedula_rif." ha sido seleccionado.",
                "cliente_info" => $cliente_info,
            ));
                
            $pdo = null;
            $cliente_info_stmt = null;
    
            $_SESSION['cliente_id'] = $cliente_info["id"];
            die($return_data);
        }


    } catch (PDOException $e){
        require_once 'error_handler.php'; 
        $error_msg = handleError($e->getCode());
        $return_data = json_encode(array("status" => "failed", "message" => $error_msg));
        die($return_data);
    }


} else{
    header("Location: ./index.php");

}

?>

