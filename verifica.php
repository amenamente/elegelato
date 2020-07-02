<?php
session_start();
require_once 'lib/utilities.php';
require_once 'lib/config.php';
forceHTTPS();

htmlHead("Elé - Gelato d'autore | verifica");
if(isset($_SESSION['uCode'])) {
    header("Location: index.php");
    exit();
} else if(isset($_POST['phone']) && isset($_POST['verificationCode'])) {
    $phone = $_POST['phone'];
    $verificationCode = strtoupper($_POST['verificationCode']);
    $conn = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
    if (mysqli_connect_error()) {
        die("si è verificato un errore tecnico, ci scusiamo per il disagio; riprova!");
    }
    if (!mysqli_autocommit($conn, false))
        die("si è verificato un errore tecnico, ci scusiamo per il disagio; riprova!");
    if ($stmt = mysqli_prepare($conn, "SELECT uCode, name, gender, friendCode FROM USER WHERE phone LIKE ? FOR UPDATE;")) {
        if (!mysqli_stmt_bind_param($stmt, "s", $phone))
            rollbackError($conn);
        if (!mysqli_stmt_execute($stmt))
            rollbackError($conn);
        if (!mysqli_stmt_bind_result($stmt, $uCode, $name, $gender, $myFriendCode))
            rollbackError($conn);
        if (!mysqli_stmt_fetch($stmt))
            $auth = false;
        else
            $auth = true;
        mysqli_stmt_close($stmt);
    } else
        rollbackError($conn);

    if($auth === true) {
        if ($stmt = mysqli_prepare($conn, "SELECT friendCode FROM VERIFICATION  WHERE uCode = ? AND verificationCode = ? FOR UPDATE;")) {
            if (!mysqli_stmt_bind_param($stmt, "is", $uCode, $verificationCode))
                rollbackError($conn);
            if (!mysqli_stmt_execute($stmt))
                rollbackError($conn);
            if (!mysqli_stmt_bind_result($stmt, $friendCode))
                rollbackError($conn);
            if (!mysqli_stmt_fetch($stmt))
                $success = 0;
            mysqli_stmt_close($stmt);
        } else
            rollbackError($conn);


        if (!isset($success)) {
            if ($stmt = mysqli_prepare($conn, "DELETE FROM VERIFICATION WHERE uCode = ? AND verificationCode = ?")) {
                if (!mysqli_stmt_bind_param($stmt, "is", $uCode, $verificationCode))
                    rollbackError($conn);
                if (!mysqli_stmt_execute($stmt))
                    rollbackError($conn);
                $success = mysqli_affected_rows($conn);
                mysqli_stmt_close($stmt);
            } else
                rollbackError($conn);
        }

        if ($success === 1) {
            if ($_POST['friendCode'] !== "NULL") {
                if ($stmt = mysqli_prepare($conn, "SELECT points FROM USER WHERE friendCode LIKE ? FOR UPDATE;")) {
                    if (!mysqli_stmt_bind_param($stmt, "s", $friendCode))
                        rollbackError($conn);
                    if (!mysqli_stmt_execute($stmt))
                        rollbackError($conn);
                    if (!mysqli_stmt_bind_result($stmt, $friendPoints))
                        rollbackError($conn);
                    if (!mysqli_stmt_fetch($stmt))
                        $friendFound = false;
                    else
                        $friendFound = true;
                    mysqli_stmt_close($stmt);
                } else
                    rollbackError($conn);
                if ($friendFound) {
                    $friendPoints = $friendPoints + 1;
                    if ($stmt = mysqli_prepare($conn, "UPDATE USER SET points = ? WHERE friendCode LIKE ?;")) {
                        if (!mysqli_stmt_bind_param($stmt, "is", $friendPoints, $friendCode))
                            rollbackError($conn);
                        if (!mysqli_stmt_execute($stmt))
                            rollbackError($conn);
                        mysqli_stmt_close($stmt);
                    } else
                        rollbackError($conn);
                    $myPoints = 1;
                } else
                    $myPoints = 0;
            } else
                $myPoints = 0;

            if ($stmt = mysqli_prepare($conn, "UPDATE USER SET points = ${myPoints}, verified = 1 WHERE uCode = ?")) {
                if (!mysqli_stmt_bind_param($stmt, "i", $uCode))
                    rollbackError($conn);
                if (!mysqli_stmt_execute($stmt))
                    rollbackError($conn);
                mysqli_stmt_close($stmt);
            } else
                rollbackError($conn);

            mysqli_commit($conn);
            mysqli_close($conn);

            if (isset($_SESSION['aCode']))
                unset($_SESSION['aCode']);
            $_SESSION['uCode'] = $uCode;
            $_SESSION['friendCode'] = $myFriendCode;
            $_SESSION['name'] = $name;
            $_SESSION['gender'] = $gender;
            header("Location: index.php");
            exit();
        } else {
            if (!mysqli_rollback($conn))
                die("si è verificato un errore tecnico, ci scusiamo per il disagio; riprova!");
            mysqli_close($conn);
        }
    } else {
        if (!mysqli_rollback($conn))
            die("si è verificato un errore tecnico, ci scusiamo per il disagio; riprova!");
        mysqli_close($conn);
    }
}
    testJS();
    openBox("trueJS"); ?>
    <div class="text-center">
        <a href="index.php"><img id="banner-login" src="icons/banner.png"></a><br><br>
    </div>
    <form method="POST" action="verifica.php" id="verificaForm">
        <fieldset>
            <legend>ci sei quasi</legend>
            <?php if(isset($auth) && $auth == false){ ?>
                <p class="text-secondary">cellulare non corretto</p>
            <?php }
                if(isset($success) && $success === 0) {
                    echo "<p class=\"text-secondary\">cellulare o codice di verifica errati, riprova</p>";
                }
                if(isset($_GET['phone']) && filter_var(isset($_GET['phone']), FILTER_VALIDATE_INT))
                    $phone = $_GET['phone'];
                if(isset($_GET['code']) && strlen($_GET['code']) === 4)
                    $code = $_GET['code'];
            ?>
            <div class="form-group">
                <label for="phone">cellulare</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="inserisci il tuo cellulare" <?php
                if(isset($phone))
                    echo "value=\"${phone}\">";
                else
                    echo ">";
                ?>
            </div>
            <div class="form-group">
                <label for="codice">codice</label>
                <input type="text" class="form-control" id="verificationCode" name="verificationCode" placeholder="inserisci il codice ricevuto via SMS" <?php
                if(isset($code))
                    echo "value=\"${code}\">";
                else
                    echo ">";
                ?>
            </div>
            <p><small>se non ricevi l'SMS con il codice di verifica faccelo sapere appena verrai a trovarci: siamo pronti ad aiutarti</small></p>
        </fieldset>
    </form><br>
    <div class="text-center">
        <?php row1Button("verifica", "verifica"); ?>
    </div>
    <script type="text/javascript">
        $("#verificaButton").click ( () => {
            $("#verificaForm").submit();
        });
        $("#phone").on("keydown", function (event) {
            if (event.key === "Enter")
                $("#verificaForm").submit();
        });
        $("#verificationCode").on("keydown", function (event) {
            if (event.key === "Enter")
                $("#verificaForm").submit();
        });
    </script>
<?php closeBox();