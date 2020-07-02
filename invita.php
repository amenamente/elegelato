<?php
session_start();
require_once 'lib/utilities.php';
require_once 'lib/config.php';
forceHTTPS();

htmlHead("Elé - Gelato d'autore | invita un amico");
/*
 * login
 */
if(!isset($_SESSION['uCode'])){
    header("Location: index.php");
    exit();
}
    openBox("trueJS");
    testJS(); ?>

	<div class="text-center">
        <a href="index.php"><img id="banner-login" src="icons/banner.png"></a><br><br>
        <h5>per ogni amico che inviti guadagni <b>1 punto</b><br>
        e fai guadagnare <b>1 punto</b><br></h5>
        <h4>il tuo <b>codice amico</b> è <span class="badge badge-primary badge-pill"><?php echo $_SESSION['friendCode'] ?></span><br></h4>
        <h6>l'invitat* dovrà inserirlo al momento della registrazione<br>
            <hr class="my-4">
            oppure condividi il seguente link<br><span class="badge badge-primary badge-pill">elegelato.it/registrati.php?amico=<?php echo $_SESSION['friendCode'] ?></span></h6><br>
        <?php row1Button("indietro", "indietro"); ?>
  	</div>
    <script type="text/javascript">
        $("#indietroButton").click( () => {
            window.location = "index.php";
        });
    </script>
<?php closeBox();