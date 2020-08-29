<?
$nbjoueursmaximum = 8;
$nomsdejoueurs = array(1 => "Cedric","Sara","Mikael","Erwin","Cynthia","Nicolas","Papa","Maman","GP","GM");
?>
<html>
<head>
<title>Bienvenue sur le démineur en PHP</title>
<style>
.auCentre {
	text-align: center
}

</style>
<script>
function mines(){
	document.cre.nbMines.value=Math.round(0.195*document.cre.tX.value*document.cre.tY.value);
}

function caseJoueur(i) {
<?
for ($i=1;$i<=$nbjoueursmaximum ;$i++)
  echo "if(i==".$i.")	return document.all.no".$i.";\n"
?>
}

function nbjoueurs() {
	n = parseInt(document.cre.nbJoueurs.value);
	var i;
	for (i = 1; i<=<?echo $nbjoueursmaximum;?>; i++) {
		if (i > n) {
			caseJoueur(i).style.color="white";
			caseJoueur(i).style.border="none";
		} else {
			caseJoueur(i).style.color="black";
			caseJoueur(i).style.border="1px solid";
			
		}
	}
}
</script>
</head>

<body onload="nbjoueurs();mines()">
Vous êtes sur un jeu développé par Cédric, Mikaël et Erwin (son) vous permettant de jouer au démineur jusqu'à 9 joueurs. Bon jeu !
<form action="creation.php" method=POST name=cre>
<b>Création d'une partie</b><br>
Nombre de joueurs :<select name="nbJoueurs" onchange="nbjoueurs();">
	<option value=2 selected>2</option>
<?
for ($i=3;$i<=$nbjoueursmaximum;$i++)
       echo "<option value=".$i.">".$i."</option>\n";
?>
</select><br>
<?
for ($i=1;$i<=$nbjoueursmaximum;$i++)
       echo "Nom Joueur ".$i." : <input type=text id=no".$i." name=nomsJoueurs".$i." value=".$nomsdejoueurs[$i]." onfocus=\"this.value='';\"><br>\n";
?>
<!--Nom Joueur 1 : <input type=text id=no1 name=nomsJoueurs1 value=Cedric onfocus="this.value='';"><br>
Nom Joueur 2 : <input type=text id=no2 name=nomsJoueurs2 value=Sara onfocus="this.value='';"><br>
Nom Joueur 3 : <input type=text id=no3 name=nomsJoueurs3 value=Mikaël onfocus="this.value='';"><br>
Nom Joueur 4 : <input type=text id=no4 name=nomsJoueurs4 value=Erwin onfocus="this.value='';"><br>
Nom Joueur 5 : <input type=text id=no5 name=nomsJoueurs5 value=Cynthia onfocus="this.value='';"><br>-->
nombre de mines : <input type=text name=nbMines value=51 size=3 class=auCentre><br>
Taille :	<input size=3 type=text name=tX value=16 onkeyup="mines();" class=auCentre>x<input size=3 type=text name=tY value=16 onkeyup="mines();" class=auCentre><br>
	<input type=submit value="Creer !">
</form>
<b>Parties en cours</b><br>
<table>
<?
if (isset($_GET["d"])){
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
		echo "<td><a href=frames.php?f=$f>$f</a> :</td>";
		for ($i=1; $i < sizeof($tligne); $i++)
			echo "<td><a href=\"demineur.php?f=$f&j=".trim($tligne[$i])."\">".trim($tligne[$i])."</a></td>";
		echo "</tr>\n";
	}}
}

?>
</table>
<form action="demineur.php" method=GET>
<b>Entrer dans une partie non indiquée</b><br>
Nom du joueur : <input type=text name=j><br>
Partie : <input type=text name=f><br>
<input type=submit value="Entrer !">
<br>
</form><br>
<a href=admin.php>Administrer le jeu</a>
</body>
</html>