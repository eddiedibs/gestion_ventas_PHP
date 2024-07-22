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
            die("empty");
        }

        $stmt = makeQuery($pdo, 
                    "SELECT producto_id, cantidad FROM items_carrito WHERE carrito_id = ?",
                    [$carrito_id]);
        
        $items_carrito =  $stmt->fetchAll();

        $productos = [];
        $subtotal_final = 0;
        $total_iva = 0;
        require_once "validator_handler.php";
        foreach ($items_carrito as $item) {
            $stmt = makeQuery($pdo, 
                "SELECT nombre, precio_base, descuento, cantidad_total, tiene_iva FROM productos WHERE id = ?",
                [$item['producto_id']]);
            // $stmt->execute([$item['producto_id']]);
            $producto = $stmt->fetchAll();

            $iva = "N/A";
            // Calculate the discount amount
            $producto[0]["subtotal"] = $producto[0]["precio_base"] - ($producto[0]["precio_base"] * $producto[0]["descuento"]);
         

            // Format the final price to 2 decimal places
            $producto_tiene_iva = $producto[0]["tiene_iva"];
            if ($producto_tiene_iva){
                $producto[0]["subtotal"] = $producto[0]["subtotal"] + ($producto[0]["subtotal"] * 0.16);
                $iva = "16%";
                $finalPrice = $item['cantidad'] * $producto[0]["subtotal"];
                $total_iva = $total_iva + ($finalPrice * 0.16);
            }else{
                $finalPrice = $item['cantidad'] * $producto[0]["subtotal"];

            }
            $subtotal_final = $subtotal_final + $finalPrice;
            $product_data = array(
                "id" => $item['producto_id'],
                "nombre" => $producto[0]["nombre"],
                "precio_base" => $producto[0]["precio_base"]."$",
                "descuento" => ($producto[0]["descuento"] * 100)."%",
                "IVA" => $iva,
                "subtotal" => stripDecimal($finalPrice)."$",
                "cantidad" => $item['cantidad'],
            );
            $productos[] = $product_data;
            // $productos[] = array_push($producto, $item['cantidad']);
        }
        $total = $subtotal_final + $total_iva;
        header('Content-Type: application/json');
        $subtotal_final = stripDecimal($subtotal_final)." $";
        $total_iva = stripDecimal($total_iva)." $";
        $total = stripDecimal($total)." $";

        if (empty($items_carrito)){
            $subtotal_final = "";
            $total_iva = "";
            $total = "";
        
        };
        $return_data = json_encode(array(
            "status" => "success",
            "message" => "Productos actualizados exitosamente.",
            "productos" => $productos,
            "subtotal_final" => $subtotal_final,
            "total_iva" => $total_iva,
            "total" => $total,
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

