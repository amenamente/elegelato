<?php
session_start();
require_once '../../lib/config.php';
require_once '../../lib/utilities.php';
forceHTTPS();

function noRollBackError ($conn) {
    mysqli_close($conn);
    http_response_code(500);
    exit();
}

if(!isset($_SESSION['uCode'])){
    header("Location: index.php");
    exit();
} else {
    if(!empty($_GET) && isset($_GET['oCode'])){
        $conn = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
        if (mysqli_connect_errno()) {
            http_response_code(500);
            exit();
        }
        if ($stmt = mysqli_prepare($conn, "SELECT outcome, points FROM COLLECT_TRANSACTION WHERE oCode = ? AND uCode = ?;")) {
            $oCode = filter_var($_GET['oCode'], FILTER_SANITIZE_NUMBER_INT);
            if (!mysqli_stmt_bind_param($stmt, "ii", $oCode, $_SESSION['uCode']))
                noRollBackError($conn);
            if (!mysqli_stmt_execute($stmt))
                noRollBackError($conn);
            if(!mysqli_stmt_bind_result($stmt, $outcome, $points))
                noRollBackError($conn);
            if (! mysqli_stmt_fetch($stmt))
                noRollBackError($conn);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
        } else {
            http_response_code(500);
            exit();
        }
        $result = [
            'outcome' => $outcome,
            'points' => $points
        ];
        echo json_encode($result);
        exit();
    } else {
        http_response_code(400);
        exit();
    }
}