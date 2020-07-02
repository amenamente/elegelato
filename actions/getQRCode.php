<?php
session_start();
require_once "../lib/phpqrcode/qrlib.php";

if(!isset($_SESSION['uCode'])){
    header("Location: index.php");
    exit();
} else {
    if(!empty($_GET) && isset($_GET['action']) && isset($_GET['oCode'])){
        $action = filter_var($_GET['action'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $oCode = filter_var($_GET['oCode'], FILTER_SANITIZE_NUMBER_INT);
        $content = '{"action":"'.$action.'","oCode":"'.$oCode.'"}';
        QRcode::png($content, null, "M", 9, 0, false);
    }
}