<?php
session_start();
require_once 'lib/utilities.php';
require_once 'lib/config.php';
forceHTTPS();
setcookie("lastVisited", "index.php");
htmlHead("Elé - Gelato d'autore | home");
/*
 * login
 */
if(!isset($_SESSION['uCode']) && isset($_POST['phone']) && isset($_POST['password'])){
    $conn = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
    if (mysqli_connect_errno())
        die("Connessione al database fallita" . mysqli_connect_error());
    if ($stmt = mysqli_prepare($conn, "SELECT uCode, phone, name, password, gender, friendCode, verified FROM USER WHERE phone LIKE ?")) {
        $phone = $_POST['phone'];
        if (!mysqli_stmt_bind_param($stmt, "s", $phone))
            die("si è verificato un errore tecnico, ci scusiamo per il disagio; riprova!");
        if (!mysqli_stmt_execute($stmt))
            die("si è verificato un errore tecnico, ci scusiamo per il disagio; riprova!");
        if(!mysqli_stmt_bind_result($stmt, $uCode, $phone, $name, $password, $gender, $friendCode, $verified))
            die("si è verificato un errore tecnico, ci scusiamo per il disagio; riprova!");
        $auth = true;
        if(!mysqli_stmt_fetch($stmt))
            $auth = false;
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
    if($auth !== false && !password_verify($_POST['password'], $password))
        $auth = false;
    else if($auth !== false && ((bool)$verified) === false) {
        header("Location: verifica.php?phone=${phone}");
        exit();
    } else if ($auth !== false) {
        if(isset($_SESSION['aCode']))
            unset($_SESSION['aCode']);
        $_SESSION['uCode'] = $uCode;
        $_SESSION['friendCode'] = $friendCode;
        $_SESSION['name'] = $name;
        $_SESSION['gender'] = $gender;
    }
}
if(!isset($_SESSION['uCode']) || (isset($auth) && $auth == false)) {
/*
 * guest
 */
    testJS(); 
    openBox("trueJS"); ?>
    <div class="text-center">
        <a href="index.php"><img id="banner-login" src="icons/banner.png"></a><br><br>
    </div>
    <legend>accedi o registrati</legend>
    <?php if(isset($auth) && $auth == false){ ?>
        <p class="text-secondary">cellulare o password non corretti</p>
    <?php } ?>
    <form method="POST" action="index.php" id="signinForm">
    	<fieldset>
        	<div class="form-group">
          		<label>cellulare</label>
          		<input type="text" class="form-control" id="phone" name="phone" placeholder="inserisci il tuo cellulare">
        	</div>
        	<div class="form-group">
          		<label>password</label>
          		<input type="password" class="form-control" id="password" name="password" placeholder="inserisci la tua password">
        	</div><br>
      	</fieldset>
	</form>
    <div class="text-center">
        <?php row2Button("entra", "entra", "registrati", "registrati");?>
    </div>
    <script type="text/javascript">
        $("#entraButton").click( () => {
           $("#signinForm").submit();
        });
        $("#registratiButton").click( () => {
            window.location = "registrati.php";
        });
        $("#phone").on("keydown", function (event) {
            if (event.key === "Enter")
                $("#signinForm").submit();
        });
        $("#password").on("keydown", function (event) {
            if (event.key === "Enter")
                $("#signinForm").submit();
        });
    </script>
<?php closeBox();
} else if (isset($_SESSION['uCode'])){
    /*
     * user
     */
    testJS();
    openBox("trueJS"); ?>

	<div class="text-center">
        <a href="index.php"><img id="banner-login" src="icons/banner.png"></a><br><br>
		<h4>benvenut<?php
		if($_SESSION['gender'] === "M")
		    echo "o ";
		else if ($_SESSION['gender'] === "F")
		    echo "a ";
		else
		    echo "* ";
		echo "<b>${_SESSION['name']}</b></h4>";
		$availPoints = getUserPoints($_SESSION['uCode']);
		?>
		<h5>hai raccolto <?php echo "<b>${availPoints}";
		    if($availPoints === 1)
		        echo "</b> punto<br><br>";
		    else
		        echo "</b> punti<br><br>";
            echo "</h5>";
            row2Button("premi", "scopri i premi", "raccogli", "raccogli punti");
            echo "<br>";
            row2Button("invita", "invita un amico", "modifica", "modifica il profilo");
            echo "<br>";
            row1Button("esci", "esci");
        ?>
    </div>
    <script type="text/javascript">
        $("#premiButton").click( () => {
            window.location = "premi.php";
        });
        $("#raccogliButton").click( () => {
            window.location = "raccogli.php";
        });
        $("#modificaButton").click( () => {
            window.location = "modifica.php";
        });
        $("#invitaButton").click( () => {
            window.location = "invita.php";
        });
        $("#esciButton").click( () => {
            window.location = "logout.php";
        });
    </script>
<?php closeBox();
}?>
