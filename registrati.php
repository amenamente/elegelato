<?php
session_start();
require_once 'lib/utilities.php';
require_once 'lib/config.php';
forceHTTPS();

htmlHead("Elé - Gelato d'autore | registrati");
if(isset($_SESSION['uCode'])) {
    header("Location: index.php");
} else if (isset($_POST['name']) && isset($_POST['yearOfBirth']) && isset($_POST['gender']) && isset($_POST['city']) && isset($_POST['friendCode']) && isset($_POST['phone']) && isset($_POST['password']) && isset($_POST['hCode'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $password = password_hash($_POST['password'],PASSWORD_BCRYPT);

    if (preg_match("/^[0-9]{4}$/", $_POST['yearOfBirth']))
        $yearOfBirth = $_POST['yearOfBirth'];
    else
        http_response_code(400);
    if(date_create('now') < date_create($_POST['yearOfBirth']))
        http_response_code(400);
    if(date_create('1902') > date_create($_POST['yearOfBirth']))
        http_response_code(400);


    if (preg_match("/^[MFO]{1}$/", $_POST['gender']))
        $gender = $_POST['gender'];
    else
        http_response_code(400);

    if (filter_var($_POST['city'], FILTER_VALIDATE_INT))
        $cCode = $_POST['city'];
    else
        http_response_code(400);

    if (filter_var($_POST['hCode'], FILTER_VALIDATE_INT))
        $hCode = $_POST['hCode'];
    else
        http_response_code(400);

    if (preg_match("/^([+]39)?((313)|(32[03789])|(33[013456789])|(34[0256789])|(36[0368])|(37[037])|(38[0389])|(39[0123]))([\d]{7})$/", $_POST['phone']))
        $phone = $_POST['phone'];
    else
        http_response_code(400);
    $conn = mysqli_connect(DBHOST, DBUSER, DBPWD, DBNAME);
    if (mysqli_connect_error()) {
        die("Connessione al database fallita");
    }

    if ($stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM USER WHERE phone LIKE ?;")) {
        if (!mysqli_stmt_bind_param($stmt, "i", $phone))
            die("Binding dei parametri fallito");
        if (!mysqli_stmt_execute($stmt))
            die("Esecuzione della query fallita");
        if(!mysqli_stmt_bind_result($stmt, $count))
            die("Binding dei risultati fallito");
        if(! mysqli_stmt_fetch($stmt))
            die("Fetch del risultato fallito");
        mysqli_stmt_close($stmt);
    } else
        die("Preparazione dello statement fallita");
    if($count === 1) {
        http_response_code(400);
    }
    if(! mysqli_autocommit($conn, false))
        die("Disabilitazione dell'autocommit fallita");
    if($_POST['friendCode'] !== "") {
        $friendCode = strtoupper($_POST['friendCode']);
        if ($stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM USER WHERE friendCode LIKE ?;")) {
            if (!mysqli_stmt_bind_param($stmt, "s", $friendCode))
                rollbackError($conn);
            if (!mysqli_stmt_execute($stmt))
                rollbackError($conn);
            if (!mysqli_stmt_bind_result($stmt, $friendFound))
                rollbackError($conn);
            if (!mysqli_stmt_fetch($stmt))
                rollbackError($conn);
            mysqli_stmt_close($stmt);
        } else
            rollbackError($conn);
    }

    $friendCodeCreated = false;
    for($i = 0; !$friendCodeCreated; $i++){
        $myFriendCode = hash("md5", "${phone}${i}");
        $myFriendCode = strtoupper(substr($myFriendCode,0,6));
        if ($stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM USER WHERE friendCode LIKE ? FOR UPDATE;")) {
            if (!mysqli_stmt_bind_param($stmt, "s", $myFriendCode))
                rollbackError($conn);
            if (!mysqli_stmt_execute($stmt))
                rollbackError($conn);
            if(!mysqli_stmt_bind_result($stmt, $count))
                rollbackError($conn);
            if(!mysqli_stmt_fetch($stmt))
                rollbackError($conn);
            mysqli_stmt_close($stmt);
        } else
            rollbackError($conn);
        if($count === 0)
            $friendCodeCreated = true;
    }

    $salt = 102018;
    $verificationCode = hash("md5", "${name}${phone}${salt}");
    $verificationCode = strtoupper(substr($verificationCode,0,4));
    $signupDate = date("Y-m-d");
    if(isset($_POST['marketing']) && $_POST['marketing'] === "on") {
        if($friendFound === 1) {
            if ($stmt = mysqli_prepare($conn, "INSERT INTO USER(phone, password, friendCode, invitedBy, name, gender, cCode, yearOfBirth, signupDate, points, hCode, marketing, verified) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, 1, 0);")) {
                if (!mysqli_stmt_bind_param($stmt, "ssssssissi", $phone, $password, $myFriendCode, $friendCode, $name, $gender, $cCode, $yearOfBirth, $signupDate, $hCode))
                    rollbackError($conn);
            } else
                rollbackError($conn);
        } else {
            if ($stmt = mysqli_prepare($conn, "INSERT INTO USER(phone, password, friendCode, name, gender, cCode, yearOfBirth, signupDate, points, hCode, marketing, verified) VALUES(?, ?, ?, ?, ?, ?, ?, ?, 0, ?, 1, 0);")) {
                if (!mysqli_stmt_bind_param($stmt, "sssssissi", $phone, $password, $myFriendCode, $name, $gender, $cCode, $yearOfBirth, $signupDate, $hCode))
                    rollbackError($conn);
            } else
                rollbackError($conn);
        }
    } else {
        if($friendFound === 1) {
            if ($stmt = mysqli_prepare($conn, "INSERT INTO USER(phone, password, friendCode, invitedBy, name, gender, cCode, yearOfBirth, signupDate, points, hCode, marketing, verified) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, 0, 0);")) {
                if (!mysqli_stmt_bind_param($stmt, "ssssssissi", $phone, $password, $myFriendCode, $friendCode, $name, $gender, $cCode, $yearOfBirth, $signupDate, $hCode))
                    rollbackError($conn);
            } else
                rollbackError($conn);
        } else {
            if ($stmt = mysqli_prepare($conn, "INSERT INTO USER(phone, password, friendCode, name, gender, cCode, yearOfBirth, signupDate, points, hCode, marketing, verified) VALUES(?, ?, ?, ?, ?, ?, ?, ?, 0, ?, 0, 0);")) {
                if (!mysqli_stmt_bind_param($stmt, "sssssissi", $phone, $password, $myFriendCode, $name, $gender, $cCode, $yearOfBirth, $signupDate, $hCode))
                    rollbackError($conn);
            } else
                rollbackError($conn);
        }
    }

    if (!mysqli_stmt_execute($stmt))
        rollbackError($conn);
    $uCode = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    $lastSMS = date("Y-m-d H:i:s");
    if($friendFound === 1) {
        if ($stmt = mysqli_prepare($conn, "INSERT INTO VERIFICATION(uCode, verificationCode, friendCode, lastSMS) VALUES(?, ?, ?, ?);")) {
            if (!mysqli_stmt_bind_param($stmt, "isss", $uCode, $verificationCode, $friendCode, $lastSMS))
                rollbackError($conn);
        } else
            rollbackError($conn);
    } else {
        if ($stmt = mysqli_prepare($conn, "INSERT INTO VERIFICATION(uCode, verificationCode, lastSMS) VALUES(?, ?, ?);")) {
            if (!mysqli_stmt_bind_param($stmt, "iss", $uCode, $verificationCode, $lastSMS))
                rollbackError($conn);
        } else
            rollbackError($conn);
    }
    if (!mysqli_stmt_execute($stmt))
        rollbackError($conn);
    mysqli_stmt_close($stmt);

    // invia l'SMS con il codice di verifica
    $phoneSMS = "+39${phone}";
    $recipients = [$phoneSMS];
    $url = "https://gatewayapi.com/rest/mtsms";
    $api_token = "jBT4Hys8QWS91xbiV8sFLlWOuK9S7gNiyvGhqX14kxriZFKIDRGrJtgfHKO0hzoW";
    $json = [
        'sender' => "elegelato",
        'message' => "Clicca qui per completare la registrazione: elegelato.it/verifica.php?code=${verificationCode}\nCODICE: ${verificationCode}",
        'recipients' => [],
    ];
    foreach ($recipients as $msisdn) {
        $json['recipients'][] = ['msisdn' => $msisdn];
    }
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
    curl_setopt($ch,CURLOPT_USERPWD, $api_token.":");
    curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($json));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    /*
    print($result);
    $json = json_decode($result);
    print_r($json->ids);
    */

    mysqli_commit($conn);
    mysqli_close($conn);

    header("Location: verifica.php?phone=${phone}");
    exit();
} else {
    testJS();
    openBox("trueJS"); ?>
    <script type="text/javascript" src="lib/ajax.js"></script>
    <script type="text/javascript">
        let existingPhone = false;
        let correctYear = true;
        let equalPassword = false;
        let redBorder = function (fieldId) {
            $("#" + fieldId).css('border-color', 'var(--secondary)');
            $("#" + fieldId).css('border-width', '2px');
        };
        let noBorder = function (fieldId) {
            $("#" + fieldId).css('border-color', '');
        };
        let radioUnchecked = function () {
            $("#maleLabel").css('color', 'var(--secondary)');
            $("#maleLabel").css('font-weight', 'Bold');
            $("#femaleLabel").css('color', 'var(--secondary)');
            $("#femaleLabel").css('font-weight', 'Bold');
            $("#otherLabel").css('color', 'var(--secondary)');
            $("#otherLabel").css('font-weight', 'Bold');
            return false;
        };
        let radioChecked = function () {
            $("#maleLabel").css('color', 'var(--grey)');
            $("#maleLabel").css('font-weight', '');
            $("#femaleLabel").css('color', 'var(--grey)');
            $("#femaleLabel").css('font-weight', '');
            $("#otherLabel").css('color', 'var(--grey)');
            $("#otherLabel").css('font-weight', '');
            return true;
        };
        let checkEmptyField = function (fieldId) {
            if($("#" + fieldId).val() === "") {
                redBorder(fieldId);
                return false;
            } else {
                noBorder(fieldId);
                return true;
            }
        };
        let checkPhone = function (fieldId) {
            let exp = /([+]39)?((313)|(32[03789])|(33[013456789])|(34[0256789])|(36[0368])|(37[037])|(38[0389])|(39[0123]))([\d]{7})$/g;
            let phoneNo = $("#" + fieldId).val();
            let index = phoneNo.search(exp);
            if(index !== -1) {
                noBorder(fieldId);
                $("#" + fieldId).val(phoneNo.substr(index, phoneNo.length - index));
                return true;
            }
            redBorder(fieldId);
            return false;
        };
        let checkExistingPhone = function (fieldId, existingPhoneWarning) {
            let ajaxPhone = ajaxRequest();
            ajaxPhone.onreadystatechange = () => {
                if (ajaxPhone.readyState === 4 && ajaxPhone.status === 200) {
                    let arr = JSON.parse(ajaxPhone.responseText);
                    if (arr['existing'] === true) {
                        redBorder("phone");
                        existingPhone = true;
                        $("#" + existingPhoneWarning).html("questo cellulare è già associato ad un account<br>");
                        return false;
                    } else {
                        noBorder("phone");
                        existingPhone = false;
                        $("#" + existingPhoneWarning).html("");
                        return true;
                    }
                }
            };
            ajaxPhone.open("GET", `actions/registrati/existingPhone.php?phone=` + $("#" + fieldId).val(), true);
            ajaxPhone.send();
        };
        let checkEqualPassword = function (fieldId, fieldId2, passwordWarning) {
            if($("#" + fieldId).val() !== $("#" + fieldId2).val()) {
                redBorder("password-conf");
                equalPassword = false;
                $("#" + passwordWarning).html("le password non coincidono<br>");
                return false;
            } else {
                noBorder("password-conf");
                equalPassword = true;
                $("#" + passwordWarning).html("");
                return true;
            }
        };
        let checkDate = function (fieldId) {
            if(isNaN($("#" + fieldId).val()) || Number.parseInt($("#" + fieldId).val()) < 1902){
                redBorder("yearOfBirth");
                correctYear = false;
                return false;
            }
            let insertedDate = new Date($("#" + fieldId).val());
            if(((Date.now() - insertedDate) < 0)) {
                redBorder("yearOfBirth");
                correctYear = false;
                return false;
            } else {
                noBorder("yearOfBirth");
                correctYear = true;
                return true;
            }
        }
    </script>
    <div class="text-center">
        <a href="index.php"><img id="banner-login" src="icons/banner.png"></a><br><br>
    </div>
    <form method="POST" action="registrati.php" id="signupForm">
        <fieldset>
            <legend>qualcosa su di te</legend>
            <div class="form-group">
                <label for="name">nome</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="inserisci il tuo nome">
                <script type="text/javascript">$("#name").change( () => { checkEmptyField("name"); });</script>
            </div>
            <div class="form-group">
                <label for="year">anno di nascita</label>
                <input type="text" class="form-control" id="yearOfBirth" name="yearOfBirth">
                <script type="text/javascript">
                    $("#yearOfBirth").change( () => {
                        if(checkEmptyField("yearOfBirth")) {
                            checkDate("yearOfBirth");
                        }
                    });
                </script>
            </div>
            <label for="gender">sesso</label>
            <div class="form-group">
                <div class="custom-control custom-radio">
                    <input type="radio" id="customRadio1" name="gender" class="custom-control-input" value="M">
                    <label class="custom-control-label" for="customRadio1" id="maleLabel">maschile</label>
                </div>
                <div class="custom-control custom-radio">
                    <input type="radio" id="customRadio2" name="gender" class="custom-control-input" value="F">
                    <label class="custom-control-label" for="customRadio2" id="femaleLabel">femminile</label>
                </div>
                <div class="custom-control custom-radio">
                    <input type="radio" id="customRadio3" name="gender" class="custom-control-input" value="O">
                    <label class="custom-control-label" for="customRadio3" id="otherLabel">altro</label>
                </div>
                <script type="text/javascript">
                    $("#customRadio1").change( () => { radioChecked() } );
                    $("#customRadio2").change( () => { radioChecked() } );
                    $("#customRadio3").change( () => { radioChecked() } );
                </script>
            </div>
            <div class="form-group">
                <label for="province">provincia di residenza</label>
                <select class="custom-select" id="province">
                    <option value=""></option>
                </select>
                <script type="text/javascript">$("#province").change( () => { checkEmptyField("province"); });</script>
            </div>
            <div class="form-group">
                <label for="city">comune di residenza</label>
                <select class="custom-select" name="city" id="city">
                    <option value=""></option>
                </select>
                <script type="text/javascript">$("#city").change( () => { checkEmptyField("city"); });</script>
            </div>
            <div class="form-group">
                <label for="howdoyouknow">come ci hai conosciuti</label>
                <select class="custom-select" name="hCode" id="howdoyouknow">
                    <option value=""></option>
                </select>
                <script type="text/javascript">$("#howdoyouknow").change( () => { checkEmptyField("howdoyouknow"); });</script>
            </div>
            <div class="form-group">
                <label for="friendCode">codice amico</label>
                <?php
                if(isset($_GET['amico']))
                    echo '<input class="form-control" id="friendCode" name="friendCode" type="text" readonly="" value="', $_GET['amico'], '">';
                else
                    echo '<input type="text" class="form-control" id="friendCode" name="friendCode" placeholder="se hai un codice amico inseriscilo qui, altrimenti ignora questo campo">';
                ?>
            </div>
            <legend>dati di accesso</legend>
            <div class="form-group">
                <label for="phone">cellulare</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="inserisci il tuo cellulare">
                <small class="form-text text-secondary" id="existingPhoneWarning"></small>
                <small class="form-text text-muted">
                    lo useremo solamente per la <b>verifica della tua identità</b> e il <b>recupero della password</b> tramite SMS<br>
                    non lo useremo per finalità promozionali senza il tuo consenso
                </small>
                <script type="text/javascript">
                    $("#phone").change( () => {
                        if(checkEmptyField("phone")) {
                            let phone = checkPhone("phone");
                            if (phone !== false) {
                                checkExistingPhone("phone", "existingPhoneWarning");
                            } else {
                                $("#existingPhoneWarning").html("");
                            }
                        }
                    });
                </script>
            </div>
            <div class="form-group">
                <label for="password">password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="scegli una password"><br>
                <input type="password" class="form-control" id="password-conf" placeholder="inserisci nuovamente la password scelta">
                <small class="form-text text-secondary" id="passwordWarning"></small>
                <script type="text/javascript">
                    $("#password").change( () => {
                        checkEmptyField("password");
                    });
                    $("#password-conf").change( () => {
                        if(checkEmptyField("password-conf")){
                            checkEqualPassword("password", "password-conf", "passwordWarning");
                        }
                    });
                </script>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="customSwitch1">
                    <label class="custom-control-label" for="customSwitch1" id="terms"> accetto
                        la <a target="_blank" rel="noopener noreferrer" href="privacy.php" style="text-decoration: none">privacy policy</a>,
                        la <a target="_blank" rel="noopener noreferrer" href="cookie.php" style="text-decoration: none">cookie policy</a>
                        e i <a target="_blank" rel="noopener noreferrer" href="tos.php" style="text-decoration: none">termini e le condizioni di utilizzo</a>
                    </label>
                </div>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" name="marketing" id="customSwitch2">
                    <label class="custom-control-label" for="customSwitch2"> mi piacerebbe ricevere occasionalmente
                        offerte esclusive e promozioni</label>
                </div>
            </div>
            <br>
            <div class="text-center">
                <?php row2Button("registrati", "registrati", "indietro", "indietro");?>
            </div>
        </fieldset>
    </form>
    <script type="text/javascript">
        const cityList = $("#city");
        const provinceList = $("#province");
        const howDoYouKnow = $("#howdoyouknow");
        let ajaxHowDoYouKnow = ajaxRequest();
        ajaxHowDoYouKnow.onreadystatechange = () => {
            if(ajaxHowDoYouKnow.readyState === 4 && ajaxHowDoYouKnow.status === 200){
                let arr = JSON.parse(ajaxHowDoYouKnow.responseText);
                for(stuff in arr)
                    howDoYouKnow.append(`<option value="${arr[stuff]['hCode']}">${arr[stuff]['description']}</option>`);
            }
        };
        ajaxHowDoYouKnow.open("GET", `actions/registrati/getHowDoYouKnow.php`, true);
        ajaxHowDoYouKnow.send();
        let ajaxCities = ajaxRequest();
        ajaxCities.onreadystatechange = () => {
            if(ajaxCities.readyState === 4 && ajaxCities.status === 200){
                let arr = JSON.parse(ajaxCities.responseText);
                for(stuff in arr)
                    cityList.append(`<option value="${JSON.parse(arr[stuff])['cCode']}">${JSON.parse(arr[stuff])['comune']}</option>`);
            }
        };
        let ajaxProvinces = ajaxRequest();
        ajaxProvinces.onreadystatechange = () => {
            if(ajaxProvinces.readyState === 4 && ajaxProvinces.status === 200){
                let arr = JSON.parse(ajaxProvinces.responseText);
                for(stuff in arr)
                    provinceList.append(`<option value="${arr[stuff]}">${arr[stuff]}</option>`);
            }
        };
        ajaxProvinces.open("GET", "actions/registrati/getCities.php?provinces", true);
        ajaxProvinces.send();
        provinceList.change(() => {
            cityList.html("");
            cityList.append(`<option value=""></option>`);
            ajaxCities.open("GET", `actions/registrati/getCities.php?province=${provinceList.val()}`, true);
            ajaxCities.send();
        });


        $("#registratiButton").click( () => {
            let correct = true;
            correct &= checkEmptyField("name");
            correct &= checkEmptyField("province");
            correct &= checkEmptyField("city");
            correct &= checkEmptyField("howdoyouknow");
            correct &= checkEmptyField("password");
            correct &= checkEmptyField("password-conf");
            correct &= checkEqualPassword("password", "password-conf", "passwordWarning");
            if($("#phone").val() === "") {
                redBorder("phone");
                correct = false;
            }
            if($("#yearOfBirth").val() === "") {
                redBorder("yearOfBirth");
                correct = false;
            }
            if($("#customRadio1").prop('checked') === false && $("#customRadio2").prop('checked') === false && $("#customRadio3").prop('checked') === false ) {
                correct = radioUnchecked();
            } else {
                radioChecked();
            }
            if($("#customSwitch1").prop('checked') === false) {
                $("#terms").css('color', 'var(--secondary)');
                $("#terms").css('font-weight', 'Bold');
                correct = false;
            } else {
                $("#terms").css('color', 'var(--grey)');
                $("#terms").css('font-weight', '');
            }

            if(correct && !existingPhone && correctYear && equalPassword) {
                $("#signupForm").submit();
            }
        });
        $("#indietroButton").click( () => {
            window.location = "index.php";
        });

    </script>
<?php closeBox();
}