<?php
session_start();
require_once 'lib/utilities.php';
require_once 'lib/config.php';
forceHTTPS();

function tosBody () {
    echo <<<_TOS
        <legend>accettazione</legend>
        <i>GELATERIA ELE' DI ELEONORA ANTONIA MASSARI</i> (di seguito “<i>elegelato.it</i>”) è titolare del dominio <i>elegelato.it</i> (di seguito “Sito”). Detto Sito si pone come Programma Fedeltà mediante il quale l'Utente può ricevere degli omaggi o sconti. L’Utente che effettua il processo di registrazione e/o di utilizzazione del Sito accetta i presenti Termini e Condizioni di Utilizzo (TOS), la Privacy Policy e la Cookie Policy. Nel caso in cui il l’Utente non accetti i predetti Termini e Policies deve astenersi dall’utilizzare il Sito.
        Gli Utenti sono invitati a visionare periodicamente i presenti Termini e Condizioni di Utilizzo (TOS), l’Informativa sul Trattamento dei Dati Personali (Privacy Policy) e l’Informativa sull’Utilizzo dei Cookie (Cookie Policy) al fine di accertare eventuali presenze di aggiornamenti o modifiche.
        <br>Il Sito si serve di contenuti che sono di esclusiva proprietà del titolare del Sito. Nei detti contenuti vengono compresi a titolo esemplificativo e non esaustivo i marchi, i loghi, le immagini, le fotografie ed i prodotti offerti come premi del Programma Fedeltà; alcuni dei contenuti precedentemente elencati possono essere soggetti a copertura di copyright, marchi e/o altri diritti di proprietà intellettuale riconosciuti dall’ordinamento italiano ed internazionale.
        <br>L’Utente garantisce che i dati, i recapiti e le informazioni fornite ai fini del presente accordo sono esatti, veritieri, aggiornati e tali da consentire la sua identificazione e si impegna a comunicare ogni variazione dei medesimi.
        <br><i>elegelato.it</i> tratterà nel pieno rispetto delle leggi applicabili e dell’Informativa sul Trattamento dei Dati Personali (Privacy Policy) qualsiasi informazione che ha ad oggetto Dati Personali degli Utenti trasmessi elettronicamente al Sito in fase di registrazione.
        <br><br><legend>garanzie</legend>
        <i>elegelato.it</i> non offre alcuna garanzia in modo tacito o espresso circa la conformità dei contenuti del Sito alle aspettative degli Utenti in termini di somiglianza visiva, qualità e quantità del prodotto elaborato presso <i>elegelato.it</i>. Altresì <i>elegelato.it</i> non offre alcuna garanzia in modo tacito o espresso sulla prosecuzione del servizio e sull’assenza e/o prontezza della correzione di errori relativi al Sito.
        Si fa presente all'Utente che alcuni prodotti potrebbero essere rappresentati o raffigurati mediante immagini o produzioni grafiche le cui finalità sono esclusivamente illustrative.
        <br><br><legend>esclusione di responsabilità</legend>
        L’accesso al Sito, da parte degli Utenti, è effettuato autonomamente ed a esclusivo rischio di questi. Qualsiasi responsabilità correlata a qualsiasi danno o pregiudizio arrecato in qualsiasi modo agli Utenti come conseguenza dell’accesso o utilizzo del Sito, compresi virus, malware o altri contenuti elettronici malevoli presenti sul Sito, viene declinata da <i>elegelato.it</i>.
        <br>Qualsiasi servizio offerto attraverso il Sito, può essere sospeso o interrotto da <i>elegelato.it</i> in qualsiasi momento senza alcuna assunzione di responsabilità o impegno di qualsiasi natura in relazione alla suddetta sospensione o interruzione.
        <i>elegelato.it</i> non è responsabile e non assume alcun impegno in relazione alla sospensione o interruzione dei servizi offerti dal Sito a causa di azioni o omissioni di <i>elegelato.it</i> o di terzi.
        <br><i>elegelato.it</i> non è responsabile dei malfunzionamenti dei servizi a causa di non conformità e/o obsolescenza degli apparecchi dei quali l’Utente o terze parti sono dotati.
        <br>I contenuti di un qualunque altro sito web accessibile mediante collegamento ipertestuale o multimediale presente nel Sito non saranno oggetto di responsabilità di <i>elegelato.it</i>, considerando che i detti collegamenti sono presenti per fornire agli Utenti un accesso a ulteriori informazioni.
        <br><br><legend>regolamento del Programma Fedeltà</legend>
        <i>elegelato.it</i> mira a svolgere la funzione di Programma Fedeltà. Gli Utenti, previa registrazione e autenticazione, sono in grado di ottenere punti a seguito di acquisti in loco presso GELATERIA ELE' DI ELEONORA ANTONIA MASSARI (via per Maruggio s.n.c., 74020 Campomarino di Maruggio). Le modalità attraverso cui detto Programma Fedeltà viene messo in atto sono descritte attraverso le apposite sezioni del Sito.
        <br>Il punteggio attribuito all’acquisto viene calcolato dividendone l’importo sullo scontrino fiscale (troncato all’unità) per 2. Il Titolare si riserva il diritto di non attribuire detto punteggio in un momento diverso da quello immediatamente successivo all’acquisto.
        <br>I punti raccolti nel corso del Programma Fedeltà sono spendibili unicamente nei premi elencati nell’apposita sezione del Sito e attraverso le modalità che il Sito mette a disposizione. Tali premi possono subire variazioni in ogni loro parte. Il Titolare non abilita in nessun momento del Programma Fedeltà la conversione diretta o indiretta dei punti raccolti in somme di denaro.
        <br><br><legend>legge applicabile</legend>
        Tutte le controversie nascenti da rapporti di interrelazione tra Utenti e Sito saranno regolate dall’ordinamento del diritto italiano.
        <br><br><legend>previsioni ulteriori</legend>
        I presenti Termini e Condizioni di Utilizzo (TOS), congiuntamente all’Informativa sul Trattamento dei Dati Personali (Privacy Policy) e all’Informativa sull’Utilizzo dei Cookie (Cookie Policy), rappresentano l’intero accordo tra <i>elegelato.it</i> e gli Utenti del Sito che utilizzano il medesimo. Ulteriori Termini e Condizioni di Utilizzo (TOS) previsti successivamente da <i>elegelato.it</i> circa i rapporti con l’Utente utilizzatore del Sito saranno da considerarsi aggiuntivi e parte integrante dei presenti.
        Nel caso in cui un Foro competente dovesse ritenere annullabile o nulla o comunque non applicabile una disposizione del presente accordo, ciò non inficia la validità e l’efficacia dell’intero accordo, ma solo la parte interessata.
        <br><br>
        <p><small>ultima modifica: 24 luglio 2019</small></p>
_TOS;
}

htmlHead("Elé - Gelato d'autore | privacy policy");
    testJS();
    openBox("trueJS"); ?>
    <div class="text-center">
        <a href="index.php"><img id="banner-login" src="icons/banner.png"></a><br><br>
    </div>
    <?php tosBody(); ?>
    <div class="text-center">
        <?php row1Button("indietro", "indietro"); ?>
    </div>
    <script type="text/javascript">
        $("#indietroButton").click( () => {
            window.location = "index.php";
        });
    </script>
    <?php closeBox(); ?>