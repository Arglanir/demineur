<?
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
?><html>
<head>
<? if (!$doitJouer) { ?>
<script>
setTimeout("window.location = window.location;",5000);
</script>
<?}?>
<title>Démineur... partie entre <? echo $nomJoueur[1]." et ".$nomJoueur[2]; ?></title>
<script language=Javascript>
function joueEn(x,y) {
	document.f.x.value = x;
	document.f.y.value = y;
	document.f.submit();
}

</script>
</head>
<body>
<center>
<?
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
dessinerGrille();

if ($etat_bombe==0 && $bombeAutorisee) {
?>
<a href="demineur.php?<? echo "j=".$_GET["j"]."&f=".$_GET["f"]."&s=".$_GET["s"]."&b=1";  ?>">Activer la bombe</a><br>
<?
} else if (!$bombeAutorisee) {
?>
Bombe non autorisée
<?
} else {
?>
<a href="demineur.php?<? echo "j=".$_GET["j"]."&f=".$_GET["f"]."&s=".$_GET["s"];  ?>">Desactiver la bombe</a><br>
<?}?>
<form name=f action="jouer.php?<? echo "j=".$_GET["j"]."&f=".$_GET["f"]."&s=".$_GET["s"];  ?>" method=POST>
<input type=hidden name=j value=<? echo $numeroJoueur; ?>>
<input type=hidden name=x><input type=hidden name=y>
<input type=hidden name=b value=<? echo $etat_bombe; ?>>
</form>
</center>
<?
if ($_GET["s"] == "00"){
?>
<a href="demineur.php?<? echo "j=".$_GET["j"]."&f=".$_GET["f"]."&s=11";  ?>">Sons désactivés</a><br/>
<?
} else {
?>
<a href="demineur.php?<? echo "j=".$_GET["j"]."&f=".$_GET["f"]."&s=00";  ?>">Sons activés</a><br/>
<?
}
?>
<br/><a href="afficher.php?f=<? echo $_GET["f"]; ?>">administrer la partie</a><br/>

<? echo $message; ?>
</body>
</html>