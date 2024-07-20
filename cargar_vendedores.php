<?php
include 'conexion.php';

$sql = "SELECT * FROM vendedores";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    echo "<option value='".$row['id']."'>".$row['nombre']."</option>";
}

mysqli_close($conn);
?>

