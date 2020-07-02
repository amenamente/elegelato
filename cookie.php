<?php
session_start();
require_once 'lib/utilities.php';
require_once 'lib/config.php';
forceHTTPS();

htmlHead("Elé - Gelato d'autore | cookie policy");
    testJS();
    openBox("trueJS"); ?>
    <div class="text-center">
        <a href="index.php"><img id="banner-login" src="icons/banner.png"></a>
    </div>
    <a href="https://www.iubenda.com/privacy-policy/34552188/cookie-policy" class="iubenda-white no-brand iubenda-embed iub-body-embed" title="Cookie Policy">Cookie Policy</a><script type="text/javascript">(function (w,d) {var loader = function () {var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0]; s.src="https://cdn.iubenda.com/iubenda.js"; tag.parentNode.insertBefore(s,tag);}; if(w.addEventListener){w.addEventListener("load", loader, false);}else if(w.attachEvent){w.attachEvent("onload", loader);}else{w.onload = loader;}})(window, document);</script>
    <div class="text-center">
        <?php row1Button("indietro", "indietro"); ?>
    </div>
    <script type="text/javascript">
        $("#indietroButton").click( () => {
            window.location = "index.php";
        });
    </script>
    <?php closeBox(); ?>