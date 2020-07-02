<?php
session_start();
require_once 'lib/utilities.php';
require_once 'lib/config.php';
forceHTTPS();

htmlHead("Elé - Gelato d'autore | richiedi");
if(!isset($_SESSION['uCode'])){
    header("Location: index.php"); 
} else if(isset($_GET['pCode'])) {
    if(filter_var($_GET['pCode'], FILTER_VALIDATE_INT) === false)
        header("Location: index.php");
    $pCode = $_GET['pCode'];
    $uCode = $_SESSION['uCode'];
    $conn = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
    if ( mysqli_connect_error()) {
        die("si è verificato un errore tecnico, ci scusiamo per il disagio; riprova!");
    }
    $sql = "INSERT INTO PRIZE_TRANSACTION(uCode, pCode, outcome) VALUES (${uCode}, ${pCode}, 'WAIT');";
    if(! mysqli_query($conn, $sql))
        die("si è verificato un errore tecnico, ci scusiamo per il disagio; riprova!");
    $oCode = mysqli_insert_id($conn);
    if($oCode === 0) {
        die("si è verificato un errore tecnico, ci scusiamo per il disagio; riprova!");
    }
    mysqli_close($conn);

    testJS();
    openBox("trueJS"); ?>
	<div class="text-center">
        <a href="index.php"><img id="banner-login" src="icons/banner.png"></a><br><br>
		<h5 id="outcome">mostra il codice al <b>cassiere</b><br>per ricevere il premio<br><br></h5>
		<img id="qrCode" src=<?php echo 'actions/getQRCode.php?action=buy&oCode='.$oCode?>><br><br>
        <?php row1Button("indietro", "indietro");?>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/color/jquery.color-2.1.2.min.js" integrity="sha256-H28SdxWrZ387Ldn0qogCzFiUDDxfPiNIyJX7BECQkDE=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="lib/ajax.js"></script>
    <script>
        let request = ajaxRequest();
        request.onreadystatechange = () => {
            if(request.readyState === 4 && request.status === 200){
                let response = JSON.parse(request.responseText);
                if(response['outcome'] === 'OK'){
                    let message = "<b>congratulazioni</b><br>" + response['message'] + "<br><br>";
                    $(".jumbotron").animate({
                        backgroundColor: "#78C2AD"
                    }, "fast", () => {
                        $(".jumbotron").animate({
                            backgroundColor: "#fff5ee"
                        }, 1500);
                    });
                    $("#outcome").animate({
                        opacity: 0
                    }, "fast", () => {
                        $("#outcome").html(message);
                        $("#outcome").animate({
                            opacity: 1
                        }, 1000);
                    });
                    $("#qrCode").animate({
                        opacity: 0.25
                    }, 1000);
                    clearInterval(interval);
                }
            } else if(request.readyState === 4 && (request.status === 400 || request.status === 500)){
                $(".jumbotron").animate({
                    backgroundColor: "#F3969A"
                }, "fast", () => {
                    $(".jumbotron").animate({
                        backgroundColor: "#fff5ee"
                    }, 1500);
                });
                $("#outcome").animate({
                    opacity: 0
                }, "fast", () => {
                    $("#outcome").html("<b>ops</b><br>qualcosa è andato storto<br><br>");
                    $("#outcome").animate({
                        opacity: 1
                    }, 1000);
                });
                $("#qrCode").animate({
                    opacity: 0.25
                }, 1000);
                clearInterval(interval);
            }
        }
        let interval = setInterval(() => {
            request.open("GET", 'actions/richiedi/getPrizeOutcome.php?oCode=<?php echo $oCode?>', true);
            request.send();
        }, 2000);

        $("#indietroButton").click( () => {
            window.location = "premi.php";
        });
    </script>
<?php closeBox();
} else {
    header("Location: index.php"); 
}