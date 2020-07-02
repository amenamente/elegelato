<?php
session_start();
require_once 'lib/utilities.php';
require_once 'lib/config.php';
forceHTTPS();

htmlHead("Elé - Gelato d'autore | raccogli");
if(!isset($_SESSION['uCode'])){
    header("Location: index.php"); 
}
    $uCode = $_SESSION['uCode'];
    $conn = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
    if ( mysqli_connect_error()) {
        die("si è verificato un errore tecnico, ci scusiamo per il disagio; riprova!");
    }
    $sql = "INSERT INTO COLLECT_TRANSACTION(uCode, outcome) VALUES (${uCode}, 'WAIT');";
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
		<h5 id="outcome">mostra il codice al <b>cassiere</b><br>per raccogliere punti<br><br></h5>
        <img id="qrCode" src=<?php echo 'actions/getQRCode.php?action=collect&oCode='.$oCode?>><br><br>
        <?php
            row1Button("indietro", "indietro");
        ?>
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
                    let message = "<b>congratulazioni</b><br>"
                    if(response['points'] === 1)
                        message += "hai raccolto <b>" + response['points'] + "</b> punto<br><br>";
                    else
                        message += "hai raccolto <b>" + response['points'] + "</b> punti<br><br>";
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
            request.open("GET", 'actions/raccogli/getCollectOutcome.php?oCode=<?php echo $oCode?>', true);
            request.send();
        }, 2000);

        function getCookie(cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i <ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }
        $("#indietroButton").click( () => {
            let lastVisited = getCookie("lastVisited");
            if(lastVisited === "premi.php"){
                window.location = "premi.php";
            } else if(lastVisited === "index.php") {
                window.location = "index.php";
            } else {
                window.location = "index.php";
            }
        });
    </script>
<?php closeBox();
    

