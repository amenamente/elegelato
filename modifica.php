<?php
session_start();
require_once 'lib/utilities.php';
require_once 'lib/config.php';
forceHTTPS();

htmlHead("Elé - Gelato d'autore | privacy policy");
if(!isset($_SESSION['uCode'])){
    header("Location: index.php");
    exit();
}
    testJS();
    openBox("trueJS"); ?>
    <div class="text-center">
        <a href="index.php"><img id="banner-login" src="icons/banner.png"></a><br><br>
        <h4>in costruzione</h4>
    </div><br>
    <div class="text-center">
        <?php row1Button("indietro", "indietro"); ?>
    </div>
    <script type="text/javascript">
        $("#indietroButton").click( () => {
            window.location = "index.php";
        });
    </script>
<?php closeBox(); ?>