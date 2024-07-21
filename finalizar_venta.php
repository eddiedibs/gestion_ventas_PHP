<?php
include 'conexion.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente_id = $_POST['cliente_id'];
    $productos = $_POST['productos'];
    $subtotal = $_POST['subtotal'];
    $impuesto = $_POST['impuesto'];
    $total = $_POST['total'];
    $vendedor_id = 1; // Ejemplo, esto debería ser dinámico basado en el vendedor autenticado
    $fecha = date('Y-m-d');
    try {
        require_once 'conexion.php'; 
        $query = "SELECT * FROM vendedores;";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        header('Content-Type: application/json');
        $return_data = json_encode(array(
            "vendedores" => $stmt->fetchAll(),
        ));
            
        $pdo = null;
        $stmt = null;

        die($return_data);
        // header("Location: ./index.php");

    } catch (PDOException $e){
        require_once 'error_handler.php'; 
        $error_msg = handleError($e->getCode());
        $return_data = json_encode(array("status" => "failed", "message" => $error_msg));
        die($return_data);
    }


} else{
    header("Location: ./index.php");

}
// $sql = "INSERT INTO ventas (cliente_id, vendedor_id, fecha, subtotal, impuesto, total) VALUES ('$cliente_id', '$vendedor_id', '$fecha', '$subtotal', '$impuesto', '$total')";

// try {
//     require_once 'conexion.php'; 
//     $stmt = makeQuery($pdo, 
//             "INSERT INTO clientes (nombre, cedula_rif, telefono, direccion) VALUES (?,?,?,?);", 
//             [$nombre,$cedula_rif,$telefono,$direccion]);
//     $cliente_id = $pdo->lastInsertId();

//     header('Content-Type: application/json');
//     $return_data = json_encode(array(
//         "status" => "success",
//         "message" => "Cliente '$cedula_rif' creado exitosamente."
//     ));
//     // Create new cart
//     makeQuery($pdo, 
//             "INSERT INTO carritos (cliente_id) VALUES (?)",
//             [$cliente_id]);
//     $carrito_id = $pdo->lastInsertId();
//     $_SESSION['cliente_id'] = $cliente_id; // Assume cliente_id is stored in session
//     $_SESSION['carrito_id'] = $carrito_id; // Assume cliente_id is stored in session
//     $_SESSION['cedula_rif'] = $cedula_rif;
//     $pdo = null;
//     $stmt = null;
//     die($return_data);
//     // header("Location: ./index.php");

// } catch (PDOException $e){
//     require_once 'error_handler.php'; 
//     $error_msg = handleError($e->getCode(), $e->getMessage());
//     $return_data = json_encode(array("status" => "failed", "message" => $error_msg));
//     die($return_data);
// }

// mysqli_close($conn);
?>

