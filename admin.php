<?php
// UTF-8 output and internal encoding
header('Content-Type: text/html; charset=utf-8');
ini_set('default_charset','UTF-8');
if (function_exists('mb_internal_encoding')) mb_internal_encoding('UTF-8');

?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Bienvenue sur le démineur en PHP</title>
</head>
<body>
<?php
include("config.inc");

// Suppression des parties inactives depuis plus de 3 mois (si demandé)
if (isset($_GET["purge3mois"])) {
    $purgeCount = 0;
    if (file_exists("partiesEnCours.txt")) {
        $contenu = file("partiesEnCours.txt");
        $nouveauContenu = array();
        $now = time();
        foreach ($contenu as $ligne) {
            $tligne = explode(" ", $ligne);
            if (sizeof($tligne) < 2) continue;
            $f = $tligne[0];
            $filename = "demin".$f."partie.dmn";
            if (file_exists($filename)) {
                $last = filemtime($filename);
                if ($now - $last > 3600*24*90) { // 90 jours
                    unlink($filename);
                    $purgeCount++;
                    continue;
                }
            }
            $nouveauContenu[] = $ligne;
        }
        $fp = fopen("partiesEnCours.txt","w");
        fwrite($fp,implode("",$nouveauContenu));
        fclose($fp);
    }
    echo "<b>$purgeCount parties supprimées (inactives depuis plus de 3 mois).</b><br>";
}

if (isset($_GET["motDePasse"])) {
    if ($_GET["motDePasse"] == $motDePasseAdmin) {
?>
Parties en cours :<br>
<a href="admin.php?motDePasse=<?php echo urlencode($_GET["motDePasse"]); ?>&purge3mois=1">Supprimer les parties inactives depuis plus de 3 mois</a><br>
<table>
<?php
if (isset($_GET["d"])){
    // destruction d'une partie
    $contenu = file("partiesEnCours.txt");
    while(list($clef,$ligne) = each($contenu)) {
        $tligne=explode(" ",$ligne);
        if ($tligne[0] == $_GET["f"]) break;
    }
    if (isset($contenu[$clef])) {
        $contenu[$clef] = "";
        $fp = fopen("partiesEnCours.txt","w");
        fwrite($fp,implode("",$contenu));
        fclose($fp);
        unlink("demin".$_GET["f"]."partie.dmn");
    }
}

if (!file_exists("partiesEnCours.txt")) {
    echo "<tr><td>Aucune</td></tr>";
} else {
    $contenu = file("partiesEnCours.txt");
    if (sizeof($contenu) < 1) 
        echo "<tr><td>Aucune</td></tr>";
    else {
    while(list($clef,$ligne) = each($contenu)) {
        echo "<tr>";
        $tligne=explode(" ",$ligne);
        if (sizeof($tligne) < 2) continue;
        $f = $tligne[0];
        echo "<td><a href=\"afficher.php?f=$f&motDePasse=$motDePasseAdmin\">$f</a> :</td>";
        $filename = "demin".$f."partie.dmn";
        if (file_exists($filename)) {
            echo "<td><small>".date ("Y-m-d H:i:s", filectime($filename)) . " -> " . date ("Y-m-d H:i:s", filemtime($filename)) . "</small></td>";
        } else {
            echo "<td>N'existe plus?</td>";
        }
        for ($i=1; $i < sizeof($tligne); $i++)
            echo "<td>".htmlspecialchars($tligne[$i])."</td>";
        echo "<td><a href=\"admin.php?f=$f&d=1&motDePasse=$motDePasseAdmin\">Détruire</a></td>";
        echo "</tr>\n";
    }}
}
?>
</table><br>
<a href="index.php">Retour au jeu</a>
</body>
</html>
<?php die();
    }
}
?>
Mot de passe requis pour administrer...<br>
<form action="admin.php" method="GET">
<input type="password" name="motDePasse">
</form><br>
<a href="index.php">Retour au jeu</a>
</body>
</html>