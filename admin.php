<html>
<head>
<title>Bienvenue sur le démineur en PHP</title>
</head>
<body>
<?
include("config.inc");

if (isset($_GET["motDePasse"])) {
	if ($_GET["motDePasse"] == $motDePasseAdmin) {
?>
Parties en cours :<br>
<table>
<?
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
	echo "<tr><td>Aucune</tr></td>";
} else {
	$contenu = file("partiesEnCours.txt");
	if (sizeof($contenu) < 1) 
		echo "<tr><td>Aucune</tr></td>";
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
			echo "<td>$tligne[$i]</a></td>";
		echo "<td><a href=\"admin.php?f=$f&d=1&motDePasse=$motDePasseAdmin\">Détruire</a></td>";
		echo "</tr>\n";
	}}
}

?>
</table><br>
<a href="index.php">Retour au jeu</a>
</body>
</html>
<?die();
	}
}
?>
Mot de passe requis pour administrer...<br>
<form action=admin.php method=GET>
<input type=password name=motDePasse>
</form><br>
<a href="index.php">Retour au jeu</a>
</body>
</html>