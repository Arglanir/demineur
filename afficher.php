<html>
<head>
<title>Bienvenue sur le démineur en PHP</title>
</head>
<body>
<?
include("fonctions.inc");

if (isset($_GET["motDePasse"])) {
	if ($_GET["motDePasse"] == $motDePasseAdmin) {
chargementVariables();
dessinerGrille(TRUE);
?>
<table><tr>
<?

for ($i=1; $i<=$nombreDeJoueurs; $i++) {
	echo "<td><center>";
	if ($joueurEnCours == $i) echo "<u>";
	echo $nomJoueur[$i];
	if ($joueurEnCours == $i) echo "</u>";
	echo "<br>";
	echo $minesDecouvertes[$i]."";
	echo "<img border=0 src=\"img/n-$i.PNG\">";
	echo "</center></td>\n";
}
?>
<td>
<? echo $nombreDeMines; ?> mines
</td>
</tr></table>
<a href="index.php">Retour au jeu</a><br>
<a href="admin.php?<? echo "?motDePasse=$motDePasseAdmin"?>">Retour à l'administration</a>

</body>
</html>
<?die();
	}
}
?>
Mot de passe requis pour administrer...<br>
<form action="afficher.php" method=GET>
<input type=password name=motDePasse>
<input type=hidden name=f value=<? echo $_GET["f"]; ?>>
</form><br>
<a href="index.php">Retour au jeu</a>
</body>
</html>