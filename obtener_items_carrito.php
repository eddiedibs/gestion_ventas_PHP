<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    try {
        require_once 'conexion.php'; 
        $carrito_id = $_SESSION['carrito_id'];
        if (empty($carrito_id)){
            die();
        }

        $stmt = makeQuery($pdo, 
                    "SELECT producto_id, cantidad FROM items_carrito WHERE carrito_id = ?",
                    [$carrito_id]);
        $items_carrito =  $stmt->fetchAll();
        if (empty($items_carrito)){
            die();
        };

        

        $productos = [];
        foreach ($items_carrito as $item) {
            $stmt = makeQuery($pdo, 
                "SELECT nombre, precio_base, descuento, cantidad_total FROM productos WHERE id = ?",
                [$item['producto_id']]);
            // $stmt->execute([$item['producto_id']]);
            $producto = $stmt->fetchAll();


            $producto[0]["subtotal"] = $item['cantidad'] * $producto[0]["precio_base"];
            // Calculate the discount amount
            $discountAmount = $producto[0]["subtotal"] * $producto[0]["descuento"] / 100;

            // Calculate the final price
            $finalPrice = $producto[0]["subtotal"] - $discountAmount;

            // Format the final price to 2 decimal places
            $finalPriceFormatted = number_format($finalPrice, 2);
            $product_data = array(
                "nombre" => $producto[0]["nombre"],
                "precio_base" => $producto[0]["precio_base"]."$",
                "descuento" => $producto[0]["descuento"]."%",
                "subtotal" => $finalPriceFormatted."$",
                "cantidad" => $item['cantidad'],
            );
            $productos[] = $product_data;
            // $productos[] = array_push($producto, $item['cantidad']);
        }
        // $items_carrito =  $stmt->fetchAll()
        header('Content-Type: application/json');

        $return_data = json_encode(array(
            "status" => "success",
            "message" => "Productos actualizados exitosamente.",
            "productos" => $productos,
        ));
            
        $pdo = null;
        $stmt = null;

        die($return_data);

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

