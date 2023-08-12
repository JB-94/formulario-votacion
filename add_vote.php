<?php
$connection = mysqli_connect("localhost", "root", "root", "db_formulariovotacion");
mysqli_set_charset($connection, "utf8mb4");

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    if (!$connection) {
        throw new Exception("Error de conexión: " . mysqli_connect_error());
    }

    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $alias = isset($_POST['alias']) ? $_POST['alias'] : '';
    $rut = isset($_POST['rut']) ? $_POST['rut'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $idRegion = isset($_POST['id_region']) ? $_POST['id_region'] : '';
    $idComuna = isset($_POST['id_comuna']) ? $_POST['id_comuna'] : '';
    $idCandidato = isset($_POST['id_candidato']) ? $_POST['id_candidato'] : null; // Puede ser nulo
    $comoSeEntero = isset($_POST['como_se_entero']) ? implode(",", $_POST['como_se_entero']) : '';

    if ($nombre === '' || $alias === '' || $rut === '' || $email === '' || $idRegion === '' || $idComuna === '' || $comoSeEntero === '') {
        throw new Exception("Todos los campos son requeridos");
    }

    $fechavoto = date('Y-m-d H:i:s');

    $query = "INSERT INTO voto (ID_CANDIDATO, FECHA_VOTO, NOMBRES_VOTO, ALIAS_VOTO, RUT_VOTO, EMAIL_VOTO, ID_COMUNA)
               VALUES ('$idCandidato', '$fechavoto', '$nombre', '$alias', '$rut', '$email', $idComuna)";

    $result = mysqli_query($connection, $query);

    if (!$result) {
        throw new Exception("Error al insertar el voto: " . mysqli_error($connection));
    }

    // Insertar cómo se enteró de nosotros en la tabla nosotros
    $queryNosotros = "INSERT INTO nosotros (ID_VOTO, NOMBRE_NOSOTROS)
                      VALUES (LAST_INSERT_ID(), '$comoSeEntero')";
    
    $resultNosotros = mysqli_query($connection, $queryNosotros);

    if (!$resultNosotros) {
        throw new Exception("Error al insertar cómo se enteró: " . mysqli_error($connection));
    }

    mysqli_close($connection);

    // Envía una respuesta JSON al cliente para indicar éxito
    echo json_encode(array("success" => true));
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    // Envía una respuesta JSON al cliente para indicar error
    echo json_encode(array("error" => $errorMessage));
}
?>





