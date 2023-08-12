<?php
// Realiza la conexión a la base de datos
$connection = mysqli_connect("localhost", "root", "root", "db_formulariovotacion");
mysqli_set_charset($connection, "utf8mb4");

if (!$connection) {
    die("Error de conexión: " . mysqli_connect_error());
}

if (isset($_POST['rut'])) {
    $rut = $_POST['rut'];

    // Limpia el Formato del RUT y matiene los guiones
    $rut = preg_replace('/[^0-9kK-]/', '', $rut); 
    
    // Consulta a la base de datos para verificar si el RUT ya existe
    $query = "SELECT COUNT(*) as count FROM voto WHERE RUT_VOTO = '$rut'";
    $result = mysqli_query($connection, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $count = $row['count'];
        
        // Devuelve una respuesta JSON indicando si el RUT existe o no
        echo json_encode(array("exists" => $count > 0));
    } else {
        echo json_encode(array("exists" => false));
    }
} else {
    echo json_encode(array("exists" => false));
}

mysqli_close($connection);
?>






