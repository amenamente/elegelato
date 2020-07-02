<?php
require_once '../../lib/config.php';

if(!empty($_GET) && isset($_GET['phone'])){
    $conn = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
    if (mysqli_connect_error()) {
        die("Connessione al database fallita");
    }

    if ($stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM USER WHERE phone LIKE ?;")) {
        $phone= $_GET['phone'];
        if (!mysqli_stmt_bind_param($stmt, "s", $phone))
            die("Binding dei parametri fallito");
        if (!mysqli_stmt_execute($stmt))
            die("Esecuzione della query fallita");
        if(!mysqli_stmt_bind_result($stmt, $count))
            die("Binding dei risultati fallito");
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    } else
        die("Preparazione dello statement fallita");
    mysqli_close($conn);
    header('Content-type: application/json');
    if($count === 1) {
        echo json_encode(array( 'existing' => true));
    } else {
        echo json_encode(array( 'existing' => false));
    }
} else {
    http_response_code(400);
    exit;
}