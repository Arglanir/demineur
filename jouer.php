<?
include ("fonctions.inc");

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
<html>
<head>
<title>Démineur... partie entre <? echo $nomJoueur[1]." et ".$nomJoueur[2]; ?></title>
</head>
<body>
<center>
<?
echo $message;
?>
<br>
<table><tr>
<?

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
sur <? echo $nombreDeMines; ?> mines
</td>
</tr></table>
<br><br>

<?
dessinerGrille(TRUE);
?>
<br>
<a href="index.php">Recommencer une partie</a>
</center>
</body>
</html>