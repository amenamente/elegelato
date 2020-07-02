<?php
session_start();
require_once 'lib/utilities.php';
require_once 'lib/config.php';
forceHTTPS();
setcookie("lastVisited", "premi.php");
htmlHead("Elé - Gelato d'autore | premi");
if(!isset($_SESSION['uCode'])){
    header("Location: index.php");
    exit();
}
    testJS();
    openBox("trueJS");
    $availPoints = getUserPoints($_SESSION['uCode']);
    ?>
    <div class="text-center">
        <a href="index.php"><img id="banner-login" src="icons/banner.png"></a><br><br>
        <h4>hai raccolto <?php echo "<b>${availPoints}";
            if($availPoints === 1)
                echo "</b> punto";
            else
                echo "</b> punti";
            ?></h4>
        <h6>raccogli punti e <span class="badge badge-primary badge-pill">sblocca</span> i premi<br>
            che potrai ritirare con un click
    </div><br>
    <?php
        $conn = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
        $sql = ("SELECT * FROM PRIZE WHERE hide = 0 ORDER BY points ASC;");
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            echo '<table class="table table-hover">
                      <thead style="background-color: AntiqueWhite">
                        <tr>
                          <th scope="col">premio</th>
                          <th scope="col">punti</th>
                        </tr>
                      </thead><tbody>';
            while($row = mysqli_fetch_assoc($result)){
				if($row['points'] > $availPoints)
				    echo '<tr><th scope="row">' . $row['prize'] . '</th>
                           <td>' . $row['points']  . '</td></tr>';
				else {
				    echo '<tr class="table-primary" onclick="document.location=\'richiedi.php?pCode=' . $row['pCode'] . '\'">
                            <th scope="row" style="color: #fff">' . $row['prize'] . '</th>
                            <td style="color: #fff">' . $row['points'] . '</td></tr>';
				}
            }				
            echo '</tbody></table> ';
        } else {
            die("si è verificato un errore tecnico, ci scusiamo per il disagio; riprova!");
        }
        mysqli_close($conn)?>
    <div class="text-center">
        <?php row2Button("raccogli", "raccogli punti", "indietro", "indietro"); ?>
    </div>
    <script type="text/javascript">
        $("#raccogliButton").click( () => {
            window.location = "raccogli.php";
        });
        $("#indietroButton").click( () => {
            window.location = "index.php";
        });
    </script>
<?php closeBox();