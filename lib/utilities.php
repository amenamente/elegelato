<?php
function htmlHead($pageName) {
echo <<<_HEADER
    <!DOCTYPE html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="author" content="Francesco Chimienti">
    <meta name="theme-color" content="#FAEBD7">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>$pageName</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" href="icons/favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="icons/favicon.ico" type="image/x-icon"/>
    <script type="text/javascript">
    var _iub = _iub || [];
    _iub.csConfiguration = {"cookiePolicyInOtherWindow":true,"gdprAppliesGlobally":false,"lang":"it","siteId":1638056,"cookiePolicyId":34552188, "banner":{ "acceptButtonDisplay":true,"customizeButtonDisplay":true,"acceptButtonColor":"#5d4038","acceptButtonCaptionColor":"#ffffff","customizeButtonColor":"#ffffff","customizeButtonCaptionColor":"#5a5a5a","position":"bottom","textColor":"#ffffff","backgroundColor":"#a0887e" }};
    </script><script type="text/javascript" src="//cdn.iubenda.com/cs/iubenda_cs.js" charset="UTF-8" async></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </head>
    <body>
_HEADER;
}

function openBox($id) { ?>
	<div class="container" id="<?php echo $id?>">
    	<div class="row">
    		<div class="col-lg-2"></div>
    		<div class="col-lg-8">
    			<div class="jumbotron" id="mainContainer">
<?php }
?>

<?php
function closeBox() {
    echo <<<_POLICIES
    </div>
        <div class="text-center">
            <p><small>GELATERIA ELE' DI ELEONORA ANTONIA MASSARI<br>
                P.IVA:03122280732<br>
                <a target="_blank" rel="noopener noreferrer" href="tos.php" style="text-decoration: none">TOS</a> ― 
                <a target="_blank" rel="noopener noreferrer" href="privacy.php" style="text-decoration: none">privacy policy</a> ― 
                <a target="_blank" rel="noopener noreferrer" href="cookie.php" style="text-decoration: none">cookie policy</a>
                <br>© 2019 Francesco Chimienti
            </small></p>
        </div>
    </div></div></div>
    </body>
_POLICIES;
}

function row2Button ($first, $firstDesc, $second, $secondDesc) {
    echo <<<_2BUTTON
    <div class="row">
        <div class="col-1"></div>
        <div class="col-5">
            <img id="${first}Button" class="iconButton" src="icons/${first}.png" alt="${firstDesc}"><br>
            <h6>${firstDesc}</h6>
        </div>
        <div class="col-5">
            <img id="${second}Button" class="iconButton" src="icons/${second}.png" alt="${secondDesc}"><br>
            <h6>${secondDesc}</h6>
        </div>
    </div>
_2BUTTON;
}

function row1Button ($first, $firstDesc) {
    echo <<<_1BUTTON
    <div class="row">
        <div class="col-12">
            <img id="${first}Button" class="iconButton" src="icons/${first}.png" alt="${firstDesc}"><br>
            <h6>${firstDesc}</h6>
        </div>
    </div>
_1BUTTON;

}

function getUserPoints ($uCode) {
    $conn = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
    if (mysqli_connect_error())
        die("si è verificato un errore tecnico, ci scusiamo per il disagio; riprova!");
    $sql = "SELECT * FROM USER WHERE uCode = ${uCode};";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $availPoints = $row['points'];
    } else
        die("si è verificato un errore tecnico, ci scusiamo per il disagio; riprova!");
    mysqli_close($conn);
    return $availPoints;
}

function testJS () { ?>
    <noscript>
    <?php
    openBox("falseJS"); ?>
   		<div class="text-center"><h4>abbiamo bisogno di JavaScript per darti il meglio</h4><br>
   		<h5>ecco cosa puoi fare</h5></div>
   		<ol>
   			<li>scaricare e usare <b>Google Chrome</b> oppure <b>Apple Safari</b></li>
   			<li>controllare se sono disponibili aggiornamenti per il tuo web browser</li>
   			<li>assicurarti che JavaScript non sia disabilitato</li>
   		</ol>
	<?php closeBox(); ?>
	<style type="text/css">
        #trueJS { display:none; }
    </style>
	</noscript>
<?php }

function fromEuroToPoints ($euro) {
    return $euro / 2;
}

function rollbackError($conn) {
    if(! mysqli_rollback($conn) )
        die("Rollback della transazione fallito");;
    mysqli_close($conn);
    http_response_code(500);
    echo "si è verificato un errore tecnico, ci scusiamo per il disagio; riprova!";
    exit();
}


function forceHTTPS () {
    /*
    if(empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] !== "on") {
        header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        exit();
    }
    return;
    */
}