<?
include("fonctions.inc");

creationPartie();

ecritureFichier();

?><html>
<head>
<title>Cr�ation de la partie</title>
</head>
<body>
Partie cr��e !<br>
Numero de la partie : <? echo $_GET["f"]; ?><br>
Joueur qui commence : <? echo $nomJoueur[$joueurEnCours]; ?><br>
<?
for ($i=1; $i<=$nombreDeJoueurs; $i++) {
	$lien=$site."demineur.php?j=".$nomJoueur[$i]."&f=".$_GET["f"];
?>
Lien pour <? echo $nomJoueur[$i]; ?> : <a href="<? echo $lien; ?>"><? echo $lien; ?></a><br>
<?
}
?>
<a href="frames.php?f=<? echo $_GET["f"]; ?>">Lien pour tous sur une m�me fen�tre</a><br>
<!--
<?
	for($i = 1; $i <= $tailleX; $i++) {
		for($j = 1; $j <= $tailleY; $j++)
			echo $tableauMines[$i][$j];
		echo "<br>\n";
	}

?>
-->
</body>
</html>