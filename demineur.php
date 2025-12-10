<?php
// Ensure UTF-8 for output and internal string handling
header('Content-Type: text/html; charset=utf-8');
ini_set('default_charset','UTF-8');
if (function_exists('mb_internal_encoding')) mb_internal_encoding('UTF-8');

include("fonctions.inc");

chargementVariables();

if (verifierSiGagnant()) {
	header("Location: jouer.php?"."j=".$_GET["j"]."&f=".$_GET["f"]."&s=".$_GET["s"]);
}
else jouerEtEffacerSons();
$nMaxMines=0;
for($i=1;$i<=$nombreDeJoueurs;$i++) {
	$nMaxMines = max($nMaxMines,$minesDecouvertes[$i]);
}
if ($nMaxMines>$minesDecouvertes[$numeroJoueur] && $bombeJoueur[$numeroJoueur] > 0)
	$bombeAutorisee = TRUE;
else
	$bombeAutorisee = FALSE;

$etat_bombe = 0;
if (isset($_GET["b"])) {
	if ($bombeAutorisee)
		$etat_bombe = 1;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<!-- Responsive viewport for mobile -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php appliquerStyleJeu($tailleY); ?>
<?php if (!$doitJouer) { ?>
<script>
setTimeout("window.location = window.location;",5000);
// demande de permission d'afficher des notifications
Notification.requestPermission( function(status) {
  console.log("Permission de notifier: " + status); // les notifications ne seront affichées que si "autorisées"
});
</script>
<?php } else { ?>
<script>
// envoi d'une notification
Notification.requestPermission( function(status) {
  console.log("Permission de notifier: " + status);
  var n = new Notification("Démineur", {body: "C'est à toi de jouer !"});
});
</script>
<?php } ?>
<title>Démineur... partie entre <?php echo $nomJoueur[1]." et ".$nomJoueur[2]; ?></title>
<script language="Javascript">
function joueEn(x,y) {
	document.f.x.value = x;
	document.f.y.value = y;
	document.f.submit();
}

</script>
</head>
<body>
<center>
<?php
if ($doitJouer) {
	echo "C'est à toi de jouer !";
} else {
	if ($nombreDeJoueurs > 2)
		echo "Attente du coup des adversaires...";
	else
		echo "Attente du coup de l'adversaire...";

}
?>
<br>
<table><tr>
<?php

for ($i=1; $i<=$nombreDeJoueurs; $i++) {
	echo "<td><center>";
	if ($joueurEnCours == $i) echo "<u>";
	if ($numeroJoueur == $i) echo "<i>";
	echo $nomJoueur[$i];
	if ($numeroJoueur == $i) echo "</i>";
	if ($joueurEnCours == $i) echo "</u>";
	echo "<br>";
	echo $minesDecouvertes[$i]."";
	echo "<img border=0 src=\"img/n-$i.png\">";
	echo "</center></td>\n";
}
?>
<td>
sur <?php echo $nombreDeMines; ?> mines
</td>
</tr></table>
<br><br>

<?php
dessinerGrille();

if ($etat_bombe==0 && $bombeAutorisee) {
?>
<a href="demineur.php?<?php echo "j=".$_GET["j"]."&f=".$_GET["f"]."&s=".$_GET["s"]."&b=1";  ?>">Activer la bombe</a><br>
<?php
} else if (!$bombeAutorisee) {
?>
Bombe non autorisée
<?php
} else {
?>
<a href="demineur.php?<?php echo "j=".$_GET["j"]."&f=".$_GET["f"]."&s=".$_GET["s" ];  ?>">Désactiver la bombe</a><br>
<?php }?>
<form name="f" action="jouer.php?<?php echo "j=".$_GET["j"]."&f=".$_GET["f"]."&s=".$_GET["s"];  ?>" method="POST">
<input type="hidden" name="j" value=<?php echo $numeroJoueur; ?>>
<input type="hidden" name="x"><input type="hidden" name="y">
<input type="hidden" name="b" value=<?php echo $etat_bombe; ?>>
</form>
</center>
<?php
if ($_GET["s"] == "00"){
?>
<a href="demineur.php?<?php echo "j=".$_GET["j"]."&f=".$_GET["f"]."&s=11";  ?>">Sons désactivés</a><br/>
<?php
} else {
?>
<a href="demineur.php?<?php echo "j=".$_GET["j"]."&f=".$_GET["f"]."&s=00";  ?>">Sons activés</a><br/>
<?php
}
?>
<br/><a href="afficher.php?f=<?php echo $_GET["f"]; ?>">administrer la partie</a><br/>

<?php echo $message; ?>
</body>
</html>