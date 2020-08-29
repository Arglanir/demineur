<?
include("fonctions.inc");

chargementVariables();
$_GET["j"]=$nomJoueur[1];

?><html>
<head>
<title>partie de démineur</title>
</head>
<frameset cols="<? for($i = 1; $i <= $nombreDeJoueurs; $i++) {echo round(100/$nombreDeJoueurs)."%".($i==$nombreDeJoueurs?"":",");}; ?>">
<?
for ($i=1; $i<=$nombreDeJoueurs; $i++) {
	$lien=$site."demineur.php?f=".$_GET["f"]."&j=".$nomJoueur[$i];
?>
<frame name="<? echo $nomJoueur[$i]; ?>" src="<? echo $lien; ?>">
<?
}


?>
</body>
</html>