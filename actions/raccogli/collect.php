<?php
session_start();
require_once '../../lib/utilities.php';
require_once '../../lib/config.php';
forceHTTPS();

function withRollbackError($conn) {
    if(! mysqli_rollback($conn)) {
        http_response_code(500);
        exit();
    }
    mysqli_close($conn);
    http_response_code(500);
    exit();
}

function noRollBackError ($conn) {
    mysqli_close($conn);
    http_response_code(500);
    exit();
}

if(!isset($_SESSION['aCode'])){
    header("Location: index.php");
    exit();
} else if(isset($_SESSION['aCode']) && strpos($_SESSION['rights'], 'C') !== false && isset($_GET['oCode']) && isset($_GET['euro'])){
    if(!filter_var($_GET['oCode'], FILTER_VALIDATE_INT)){
        http_response_code(400);
        exit();
    }
    if(!filter_var($_GET['euro'], FILTER_VALIDATE_INT)){
        http_response_code(400);
        exit();
    }
    $oCode = $_GET['oCode'];
    $euro = $_GET['euro'];
    $conn = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
    if (mysqli_connect_error()) {
        http_response_code(500);
        exit();
    }
    if(! mysqli_autocommit($conn, false)) {
        mysqli_close($conn);
        http_response_code(500);
        exit();
    }
    if ($stmt = mysqli_prepare($conn, "SELECT uCode, outcome FROM COLLECT_TRANSACTION WHERE oCode = ? FOR UPDATE;")) {
        if (!mysqli_stmt_bind_param($stmt, "i", $oCode))
            noRollBackError($conn);
        if (!mysqli_stmt_execute($stmt))
            noRollBackError($conn);
        if(!mysqli_stmt_bind_result($stmt, $uCode, $outcome))
            noRollBackError($conn);
        if(!mysqli_stmt_fetch($stmt)) { // codice QR non valido
            mysqli_close($conn);
            http_response_code(404);
            exit();
        } else if ($outcome === "OK") { // codice QR già utilizzato
            mysqli_close($conn);
            http_response_code(410);
            exit();
        }
        if(! mysqli_stmt_close($stmt))
            noRollBackError($conn);
    } else
        noRollBackError($conn);

    $result = mysqli_query($conn, "SELECT points FROM USER WHERE uCode = ${uCode} FOR UPDATE;");
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $uPoints = $row['points'];
    } else
        noRollBackError($conn);

    $points = fromEuroToPoints($euro);
    $newPoints = $uPoints + $points;
    if(! mysqli_query($conn, "UPDATE USER SET points = ${newPoints} WHERE uCode = ${uCode};"))
        noRollBackError($conn);

    $date = date("Y-m-d");
    $hour = date("H");
    $result = mysqli_query($conn, "SELECT tCode FROM DAY WHERE hour = ${hour} AND tDate LIKE '${date}';");
    if (mysqli_num_rows($result) === 0) {
        $dayOfWeek = date("D");
        if(!mysqli_query($conn, "INSERT INTO DAY(tDate, dayOfWeek, hour) VALUES ('${date}', '${dayOfWeek}', '${hour}');"))
            withRollbackError($conn);
        $tCode = mysqli_insert_id($conn);
    } else if (mysqli_num_rows($result) === 1){
        $row = mysqli_fetch_assoc($result);
        $tCode = $row['tCode'];
    }

    $aCode = $_SESSION['aCode'];
    if(! mysqli_query($conn, "UPDATE COLLECT_TRANSACTION SET aCode = ${aCode}, points = ${points}, tCode = ${tCode}, euro = ${euro}, outcome = 'OK' WHERE oCode = ${oCode} AND outcome LIKE 'WAIT';"))
        withRollbackError($conn);
    mysqli_commit($conn);
    mysqli_close($conn);
    http_response_code(200);
    exit();
} else {
    http_response_code(400);
    exit();
}