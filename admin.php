<?php
session_start();
require_once 'lib/utilities.php';
require_once 'lib/config.php';
forceHTTPS();

htmlHead("Elé - Gelato d'autore | amministratore");
if(!isset($_SESSION['aCode']) && isset($_POST['phone']) && isset($_POST['password'])){
    $conn = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
    if (mysqli_connect_errno())
        die("Connessione al database fallita" . mysqli_connect_error());
    if ($stmt = mysqli_prepare($conn, "SELECT aCode, name, password, rights FROM ADMIN WHERE phone LIKE ?")) {
        $phone = $_POST['phone'];
        if (!mysqli_stmt_bind_param($stmt, "s", $phone))
            die("Binding dei parametri fallito");
        if (!mysqli_stmt_execute($stmt))
            die("Esecuzione della query fallita");
        if(!mysqli_stmt_bind_result($stmt, $aCode, $name, $password, $rights))
            die("Binding dei risultati fallito");
        $i=0;
        while (mysqli_stmt_fetch($stmt))
            $i++;
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
    if($i != 1)
        $auth = false;
    else if(!password_verify($_POST['password'], $password))
        $auth = false;
    else {
        if(isset($_SESSION['uCode']))
            unset($_SESSION['uCode']);
        $_SESSION['aCode'] = $aCode;
        $_SESSION['name'] = $name;
        $_SESSION['rights'] = $rights;
    }
}
if(!isset($_SESSION['aCode']) || (isset($auth) && $auth == false)) {
    testJS();
    openBox("trueJS");
    ?>
	<div class="text-center">
        <a href="admin.php"><img id="banner-login" src="icons/banner.png"></a><br><br>
    <?php if(isset($auth) && $auth == false){ ?>
        <p class="text-secondary">cellulare o password non corretti</p>
    <?php }?>
    </div>
    <form method="POST" action="admin.php">
      <fieldset>
      <legend>entra come amministratore</legend>
        <div class="form-group">
          <label for="phone">cellulare</label>
          <input type="text" class="form-control" id="phone" name="phone" placeholder="inserisci il tuo cellulare">
        </div>
        <div class="form-group">
          <label for="password">password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="insirisci la tua password">
        </div><br>
        <div class="form-group text-center">
            <button type="submit" class="list-group-item list-group-item-action active" id="secondary-btn">entra</button>
        </div>
      </fieldset>
    </form>

<?php closeBox();
} else {
    testJS();
    openBox("trueJS");
    ?>
    <script type="text/javascript" src="lib/ajax.js"></script>
    <script type="text/javascript" src="lib/jsqrcode/grid.js"></script>
    <script type="text/javascript" src="lib/jsqrcode/version.js"></script>
    <script type="text/javascript" src="lib/jsqrcode/detector.js"></script>
    <script type="text/javascript" src="lib/jsqrcode/formatinf.js"></script>
    <script type="text/javascript" src="lib/jsqrcode/errorlevel.js"></script>
    <script type="text/javascript" src="lib/jsqrcode/bitmat.js"></script>
    <script type="text/javascript" src="lib/jsqrcode/datablock.js"></script>
    <script type="text/javascript" src="lib/jsqrcode/bmparser.js"></script>
    <script type="text/javascript" src="lib/jsqrcode/datamask.js"></script>
    <script type="text/javascript" src="lib/jsqrcode/rsdecoder.js"></script>
    <script type="text/javascript" src="lib/jsqrcode/gf256poly.js"></script>
    <script type="text/javascript" src="lib/jsqrcode/gf256.js"></script>
    <script type="text/javascript" src="lib/jsqrcode/decoder.js"></script>
    <script type="text/javascript" src="lib/jsqrcode/qrcode.js"></script>
    <script type="text/javascript" src="lib/jsqrcode/findpat.js"></script>
    <script type="text/javascript" src="lib/jsqrcode/alignpat.js"></script>
    <script type="text/javascript" src="lib/jsqrcode/databr.js"></script>

    <div class="text-center">
		<div id="esito"></div>
		<div id="liveCamContainer">
        	<video autoplay="true" id="liveCam" controls="false" hidden></video>
    		<canvas id="qr-canvas" style="max-width: 100%"></canvas>
        </div>

        <script type="text/javascript">
            var video = document.querySelector("#liveCam");
            var canvas = document.getElementById('qr-canvas');
            var ctx = canvas.getContext('2d');
            video.addEventListener('loadedmetadata', function() {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
            });
            if (navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: { facingMode: { exact: "environment" } } })
                    .then(function (stream) {
                        video.srcObject = stream;
                    })
                    .catch(function (error) {
                        console.log("Non sono riuscito a catturare lo stream della fotocamera");
                    });
            }
            video.addEventListener('play', function() {
                var $this = this; //cache
                (function loop() {
                    if (!$this.paused && !$this.ended) {
                        ctx.drawImage($this, 0, 0);
                        setTimeout(loop, 1000 / 30); // drawing at 30fps
                    }
                })();
            }, 0);

            qrcode.callback = function(decodedData){
                let request = JSON.parse(decodedData);

                if(request['action'] === "buy") {
                    <?php if(strpos($_SESSION['rights'], 'B') !== false) { ?>
                    let ajax = ajaxRequest();
                    ajax.onreadystatechange = () => {
                        if (ajax.readyState === 4 && ajax.status === 200) {
                            alert("OK: " + ajax.responseText.toUpperCase());
                        } else if (ajax.readyState === 4 && (ajax.status === 500 || ajax.status === 400)) {
                            alert("RIPROVA: errore tecnico durante l'elaborazione della richiesta");
                        } else if (ajax.readyState === 4 && ajax.status === 410) {
                            alert("ERR_1: codice QR già utilizzato con SUCCESSO");
                        } else if (ajax.readyState === 4 && ajax.status === 404) {
                            alert("ERR_2: codice QR corrotto; riprovare con un nuovo codice");
                        }
                    };
                    ajax.open("GET", 'actions/richiedi/buy.php?oCode=' + request['oCode'], true);
                    ajax.send();
                    <?php } else {
                        echo 'alert("ERR: non sei autorizzato a dare premi")';
                    } ?>
                }
                if(request['action'] === "collect") {
                    <?php if(strpos($_SESSION['rights'], 'C') !== false) { ?>
                    let price = Number.parseInt(prompt("inserisci l'importo dell'acquisto", ""));
                    if (isNaN(price)) {
                        alert("ERR_0: importo non valido, riprova");
                    } else {
                        let ajax = ajaxRequest();
                        ajax.onreadystatechange = () => {
                            if (ajax.readyState === 4 && ajax.status === 200) {
                                alert("OK: operazione conclusa con successo");
                            } else if (ajax.readyState === 4 && (ajax.status === 500 || ajax.status === 400)) {
                                alert("RIPROVA: errore tecnico durante l'elaborazione della richiesta");
                            } else if (ajax.readyState === 4 && ajax.status === 410) {
                                alert("ERR_1: codice QR già utilizzato con SUCCESSO");
                            } else if (ajax.readyState === 4 && ajax.status === 404) {
                                alert("ERR_2: codice QR corrotto; riprovare con un nuovo codice");
                            }
                        };
                        ajax.open("GET", 'actions/raccogli/collect.php?oCode=' + request['oCode'] + "&euro=" + price, true);
                        ajax.send();
                    }
                    <?php } else {
                        echo 'alert("ERR: non sei autorizzato ad aggiungere punti")';
                    }?>
                }
            };

            let timer = setInterval(() => {
                try {
                    qrcode.decode();
                } catch (e) {
                    // ignore
                }
            }, 1000);
        </script>
     </div>
<?php closeBox();
}?>