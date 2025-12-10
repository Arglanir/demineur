<?php
include ("fonctions.inc");

// UTF-8 output and internal encoding
header('Content-Type: text/html; charset=utf-8');
ini_set('default_charset','UTF-8');
if (function_exists('mb_internal_encoding')) mb_internal_encoding('UTF-8');

chargementVariables();

if (isset($_POST["x"])) {
	traitementCoup();
	$var = verifierSiGagnant();
	ecritureFichier();
	if (!$var)
		header("Location: demineur.php?"."j=".$_GET["j"]."&f=".$_GET["f"]."&s=".$_GET["s"]);
	else
		jouerEtEffacerSons();
}
else {
	$var = verifierSiGagnant();
if (!$var) {
	header("Location: demineur.php?"."j=".$_GET["j"]."&f=".$_GET["f"]."&s=".$_GET["s"]);die();
}
	jouerEtEffacerSons();
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Démineur... partie entre <?php echo $nomJoueur[1]." et ".$nomJoueur[2]; ?></title>
<?php appliquerStyleJeu($tailleY); ?>
</head>
<body>
<center>
<?php
echo $message;
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
dessinerGrille(TRUE);
?>
<br>
<a href="index.php">Recommencer une partie</a>
</center>
</body>
</html>