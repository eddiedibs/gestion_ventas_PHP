<?php


// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $cliente_id = $_SESSION['cliente_id']; // Assume cliente_id is stored in session
  
  try {
    // Get cart details
    $stmt = $pdo->prepare("SELECT ci.producto_id, ci.cantidad, p.precio
                           FROM items_carrito ci
                           JOIN carritos c ON ci.carrito_id = c.id
                           JOIN productos p ON ci.producto_id = p.id
                           WHERE c.cliente_id = ?");
    $stmt->execute([$cliente_id]);
    $items_pedido = $stmt->fetchAll();

    $total = array_reduce($items_pedido, function($carry, $item) {
        return $carry + $item['cantidad'] * $item['precio'];
    }, 0);

    // Create order
    $stmt = $pdo->prepare("INSERT INTO pedidos (cliente_id, total, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$cliente_id, $total]);
    $pedido_id = $pdo->lastInsertId();

    // Add items to order
    $stmt = $pdo->prepare("INSERT INTO items_pedido (pedido_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)");
    foreach ($items_pedido as $item) {
        $stmt->execute([$pedido_id, $item['producto_id'], $item['cantidad'], $item['precio']]);
    }

    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM items_carrito WHERE carrito_id IN (SELECT id FROM carritos WHERE cliente_id = ?)");
    $stmt->execute([$cliente_id]);
    $stmt = $pdo->prepare("DELETE FROM carritos WHERE cliente_id = ?");
    $stmt->execute([$cliente_id]);

    echo "Order placed successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }



} else{
    header("Location: ./index.php");

}

// include 'conexion.php';

// $producto_id = $_POST['producto_id'];
// $cantidad = $_POST['cantidad'];

// $sql = "SELECT * FROM productos WHERE id = $producto_id";
// $result = mysqli_query($conn, $sql);
// $row = mysqli_fetch_assoc($result);

// $nombre = $row['nombre'];
// $precio_base = $row['precio_base'];
// $descuento = $row['descuento'];
// $subtotal = ($precio_base - $descuento) * $cantidad;

// echo "<tr>
//         <td>$nombre</td>
//         <td class='cantidad'>$cantidad</td>
//         <td class='precio'>$precio_base</td>
//         <td class='descuento'>$descuento</td>
//         <td class='subtotal'>$subtotal</td>
//         <input type='hidden' class='producto_id' value='$producto_id'>
//       </tr>";

// mysqli_close($conn);
?>

