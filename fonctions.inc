<?php /* fichier qui contient les fonctions importantes et les variables globales... include("fonctions.inc") */
// Ensure UTF-8 internal encoding
if (!ini_get('default_charset') || strtolower(ini_get('default_charset')) !== 'utf-8') {
    ini_set('default_charset', 'UTF-8');
}
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

$nombreDeJoueurs = 0;            $joueurEnCours = 0;    $nomJoueur = array();    $bombeJoueur = array();
$minesDecouvertes = array();    $nombreDeMines = 0;    $tailleX = 0;            $tailleY = 0;
$tableauMines = array();        $tableauDecouvert = array();
$numeroJoueur = 0;                $tempsReel = 0;
$tailleBombe = 0;
$tableauSons = array();
$tableauCoups = array();//Derniers coups joués
$var = FALSE;
$joueMine = FALSE; $ATOUS = 0;

$message = "";        $doitJouer = 0;    $partieChargee = 0;

include("config.inc");

//global $nombreDeJoueurs, $joueurEnCours, $nomJoueur, $bombeJoueur, $minesDecouvertes, $nombreDeMines, $tailleX, $tailleY, $tableauMines, $tableauDecouvert, $message, $doitJouer, $partieChargee, $numeroJoueur, $tempsReel;


function chargementVariables() {    //lecture du fichier "demin".$_GET["f"]."partie.dmn"
    global $ATOUS, $nombreDeJoueurs, $joueurEnCours, $nomJoueur, $bombeJoueur, $minesDecouvertes, $nombreDeMines, $tailleX, $tailleY, $tableauMines, $tableauDecouvert, $message, $doitJouer, $partieChargee, $numeroJoueur, $tempsReel, $tableauSons, $tableauCoups;
    if (file_exists("demin".$_GET["f"]."partie.dmn")) {
        $contenu = file("demin".$_GET["f"]."partie.dmn");
        $ligne = 0;
//    Nombre de Joueurs
        $nombreDeJoueurs = 0 + $contenu[$ligne++];
//    Joueur en cours
        $joueurEnCours = 0 + $contenu[$ligne++];
//    Nom joueur 
        for($i = 1; $i <= $nombreDeJoueurs; $i++)
            $nomJoueur[$i] = trim($contenu[$ligne++]);
//    Bombe            $bombeJoueur[1]        0 : utilisée / 1 : disponible
        for($i = 1; $i <= $nombreDeJoueurs; $i++)
            $bombeJoueur[$i] = 0 + $contenu[$ligne++];
//    Mines découvertes par 1    $minesDecouvertes[1]
        for($i = 1; $i <= $nombreDeJoueurs; $i++)
            $minesDecouvertes[$i] =  0 + $contenu[$ligne++];
//    Nombre de mines
        $nombreDeMines =  0 + $contenu[$ligne++];
//    Taille x
        $tailleX =  0 + $contenu[$ligne++];
//    Taille y
        $tailleY =  0 + $contenu[$ligne++];
//    tableau des mines     $tableauMines[][]        1 une mine, 0 aucune
        for($i = 1; $i <= $tailleX; $i++) {
            $tableau = explode(" ",$contenu[$ligne]);
            for($j = 1; $j <= $tailleY; $j++)
                $tableauMines[$i][$j] = 0 + $tableau[$j-1];
            $ligne++;
        }
//    tableau découvert    $tableauDecouvert[][]    0-8 numéros, 9 pas découvert, -1 & -2 : mine du joueur
        for($i = 1; $i <= $tailleX; $i++) {
            $tableau = explode(" ",$contenu[$ligne]);
            for($j = 1; $j <= $tailleY; $j++)
                $tableauDecouvert[$i][$j] = 0 + $tableau[$j-1];
            $ligne++;
        }
//    Chargement des derniers coups joués
        for($i = 1; $i <= $nombreDeJoueurs; $i++) {
            $temp = explode(",",trim($contenu[$ligne++]));
            $tableauCoups[$i][0] = 0+$temp[0];
            $tableauCoups[$i][1] = 0+$temp[1];
        }    
//    Chargement des sons à jouer
        for($i = 1; $i <= $nombreDeJoueurs; $i++)
            $tableauSons[$i] = trim($contenu[$ligne++]);

        $partieChargee = 1;
        
        $numeroJoueur = @array_search($_GET["j"],$nomJoueur,FALSE);
        $doitJouer = ($numeroJoueur == $joueurEnCours || $tempsReel == 1);
    } else {
        $message .= "<p class=message>Erreur à l'ouverture de la partie ".$_GET["f"].".</p>";
        $partieChargee = 0;
        $doitJouer = 0;
    }
}

function jouerEtEffacerSons() {
    global $ATOUS, $numeroJoueur, $tableauSons, $message;
    if ($numeroJoueur > 0) {
        if($tableauSons[$numeroJoueur] != "") {
            $tableau = explode(";",$tableauSons[$numeroJoueur]);
            for($i = 0; $i < count($tableau); $i++) {
                if ($_GET["s"]!="00")
                $message .= "<EMBED src=\"". $tableau[$i] . "\" HIDDEN=TRUE AUTOSTART=TRUE LOOP=0>\n";
            }
            $tableauSons[$numeroJoueur] = "";
            //r��criture du fichier pour ne plus avoir � jouer le son
            ecritureFichier();
        }
    }
}

function rajouterSonAJouer($sonAJouer,$aQui) {
    global $ATOUS, $tableauSons, $numeroJoueur, $nombreDeJoueurs, $message;
    if($aQui==$ATOUS) {
        for ($i = 1; $i <= $nombreDeJoueurs; $i++) {
           if($tableauSons[$i]=="") $tableauSons[$i]=$sonAJouer; else $tableauSons[$i] .=";".$sonAJouer;
        }
    }
    else {
    if($tableauSons[$aQui]=="") $tableauSons[$aQui]=$sonAJouer; else $tableauSons[$aQui] .=";".$sonAJouer;
    }
}
function ecritureFichier()	{	//r��criture dans le fichier
    global $ATOUS, $nombreDeJoueurs, $joueurEnCours, $nomJoueur, $bombeJoueur, $minesDecouvertes, $nombreDeMines, $tailleX, $tailleY, $tableauMines, $tableauDecouvert, $message, $doitJouer, $partieChargee, $numeroJoueur, $tempsReel, $tableauSons, $tableauCoups;

	$contenu = "";
//	Nombre de Joueurs
	$contenu .=	$nombreDeJoueurs . "\n";
//	Joueur en cours
	$contenu .=	$joueurEnCours . "\n";
//	Nom joueur 
	for($i = 1; $i <= $nombreDeJoueurs; $i++)
		$contenu .=	$nomJoueur[$i] . "\n";
//	Bombe			$bombeJoueur[1]		0 : utilis�e / 1 : disponible
	for($i = 1; $i <= $nombreDeJoueurs; $i++)
		$contenu .=	$bombeJoueur[$i]  . "\n";
//	Mines d�couvertes par 1	$minesDecouvertes[1]
	for($i = 1; $i <= $nombreDeJoueurs; $i++)
		$contenu .=	$minesDecouvertes[$i] . "\n";
//	Nombre de mines
	$contenu .=	$nombreDeMines . "\n";
//	Taille x
	$contenu .=	$tailleX . "\n";
//	Taille y
	$contenu .=	$tailleY . "\n";
//	tableau des mines 	$tableauMines[][]		1 une mine, 0 aucune
	for($i = 1; $i <= $tailleX; $i++) {
		for($j = 1; $j <= $tailleY; $j++)
			$contenu .=	$tableauMines[$i][$j] . " ";
		$contenu .=	"\n";
	}
//	tableau d�couvert	$tableauDecouvert[][]	0-8 num�ros, 9 pas d�couvert, -1 & -2 : mine du joueur
	for($i = 1; $i <= $tailleX; $i++) {
		for($j = 1; $j <= $tailleY; $j++)
			$contenu .=	$tableauDecouvert[$i][$j]." ";
		$contenu .=	"\n";
	}
//	Derniers coups jou�s
	for($i = 1; $i <= $nombreDeJoueurs; $i++)
		$contenu .= $tableauCoups[$i][0].",".$tableauCoups[$i][1]."\n"; 
//	tableau des sons
	for($i = 1; $i <= $nombreDeJoueurs; $i++)
		$contenu .= $tableauSons[$i]."\n";
	$fp = fopen("demin".$_GET["f"]."partie.dmn","w");
	fwrite($fp,$contenu);
	fclose($fp);
}


function creationPartie(){ //creation d`un fichier "demin".$_GET["f"]."partie.dmn"
	global $ATOUS, $nombreDeJoueurs, $joueurEnCours, $nomJoueur, $bombeJoueur, $minesDecouvertes, $nombreDeMines, $tailleX, $tailleY, $tableauMines, $tableauDecouvert, $message, $doitJouer, $partieChargee, $numeroJoueur, $tempsReel, $tableauSons, $tableauCoups;

	mt_srand((double)microtime()*1000000);
	$_GET["f"] = mt_rand(100000,999999);
	
//	Nombre de Joueurs
	$nombreDeJoueurs = 0 + $_POST["nbJoueurs"];
//	Joueur en cours
	$joueurEnCours = mt_rand(1,$nombreDeJoueurs);
//	Nom joueur 
	for($i = 1; $i <= $nombreDeJoueurs; $i++) {
	    $name = trim($_POST["nomsJoueurs".$i]);
	    $name = preg_replace('/[^\w\-]/u', '', $name);
	    if ($name === '') {
	        echo "Le nom '".$name."' (extrait de '".$_POST["nomsJoueurs".$i]."') n'est pas un nom correct.";
	        die();
	    }
		$nomJoueur[$i] = $name;
	}
//	Bombe			$bombeJoueur[1]		0 : utilis�e / 1 : disponible
	for($i = 1; $i <= $nombreDeJoueurs; $i++)
		$bombeJoueur[$i] = 1;
//	Mines d�couvertes par 1	$minesDecouvertes[1]
	for($i = 1; $i <= $nombreDeJoueurs; $i++)
		$minesDecouvertes[$i] =  0;
//	Nombre de mines
	$nombreDeMines =  0 + $_POST["nbMines"];
//	Taille x
	$tailleX =  0 + $_POST["tX"];
//	Taille y
	$tailleY =  0 + $_POST["tY"];
//	tableau des mines 	$tableauMines[][]		1 une mine, 0 aucune
	for($i = 1; $i <= $tailleX; $i++)
		for($j = 1; $j <= $tailleY; $j++)
			$tableauMines[$i][$j] = 0;
	for($i = 0; $i < $nombreDeMines; $i++) {
		while ($tableauMines[$x=mt_rand(1,$tailleX)][$y=mt_rand(1,$tailleY)] != 0)
			0;
		$tableauMines[$x][$y] = 1;
	}

//	tableau d�couvert	$tableauDecouvert[][]	0-8 num�ros, 9 pas d�couvert, -1 & -2 : mine du joueur
	for($i = 1; $i <= $tailleX; $i++)
		for($j = 1; $j <= $tailleY; $j++)
			$tableauDecouvert[$i][$j] = 9;
	$ligneDeNoms = "";
	for($i = 1; $i <= $nombreDeJoueurs; $i++)
		$ligneDeNoms .= $nomJoueur[$i]." ";
		
	for($i = 1; $i <= $nombreDeJoueurs; $i++)
		for($j = 0; $j <= 1; $j++)
			$tableauCoups[$i][$j] = 0;
	
	for($i = 1; $i <= $nombreDeJoueurs; $i++)
		$tableauSons[$i] = "";

	$fp = fopen("partiesEnCours.txt","a");
	fwrite($fp,$_GET["f"]." ".$ligneDeNoms."\n");
	fclose($fp);
}

function mineEn($x, $y){
	global $ATOUS, $nombreDeJoueurs, $joueurEnCours, $nomJoueur, $bombeJoueur, $minesDecouvertes, $nombreDeMines, $tailleX, $tailleY, $tableauMines, $tableauDecouvert, $message, $doitJouer, $partieChargee, $numeroJoueur, $tempsReel, $tableauSons, $tableauCoups;
	if ($x > $tailleX || $y > $tailleY || $x < 1 || $y < 1) return 0;
	if ($tableauMines[$x][$y]==1) return 1;
	return 0;
}

function coupEn($x, $y) {
	global $ATOUS, $nombreDeJoueurs, $joueurEnCours, $nomJoueur, $bombeJoueur, $minesDecouvertes, $nombreDeMines, $tailleX, $tailleY, $tableauMines, $tableauDecouvert, $message, $doitJouer, $partieChargee, $numeroJoueur, $tempsReel, $tableauSons, $tableauCoups, $joueMine;
	if ($x > $tailleX || $y > $tailleY || $x < 1 || $y < 1) return FALSE;
	if ($tableauDecouvert[$x][$y] != 9) return FALSE;
	if ($tableauMines[$x][$y]==1) {// mine trouv�e !
		$joueMine = TRUE;
		$minesDecouvertes[$numeroJoueur] += 1;
		$tableauDecouvert[$x][$y] = - $numeroJoueur;
		return TRUE;
	} else {
		$temp = 0;
		for ($i = $x-1; $i <=$x+1; $i++)
			for ($j = $y-1; $j <=$y+1; $j++)
				$temp += mineEn($i, $j);
		if ($temp > 0) {
			$tableauDecouvert[$x][$y] = $temp;
		} else {
			$tableauDecouvert[$x][$y] = 0;
			for ($i = $x-1; $i <=$x+1; $i++)
				for ($j = $y-1; $j <=$y+1; $j++)
					coupEn($i,$j);
		}
	}
	return FALSE;
}

function traitementCoup() {
	global $ATOUS, $nombreDeJoueurs, $joueurEnCours, $nomJoueur, $bombeJoueur, $minesDecouvertes, $nombreDeMines, $tailleX, $tailleY, $tableauMines, $tableauDecouvert, $message, $doitJouer, $partieChargee, $numeroJoueur, $tempsReel, $tableauSons, $tailleBombe, $tableauCoups, $joueMine;
	$j = 0+$_POST["j"];	$x = 0+$_POST["x"];	$y = 0+$_POST["y"]; $b = 0+$_POST["b"];
	$joueMine = FALSE;
	if ($x > $tailleX || $y > $tailleY || $x < 1 || $y < 1) {
		$message .= "<p class=message>Erreur : coordonn�es incorrectes ($x,$y) !</p>";
	} else if ($j != $joueurEnCours) {
		$message .= "<p class=message>Ce n'est pas � toi de jouer !</p>";
	} else if ($b==1) {
		for ($i = $x - $tailleBombe; $i<$x+$tailleBombe+1; $i++)
			for ($j = $y - $tailleBombe; $j<$y+$tailleBombe+1; $j++)
				coupEn($i, $j);
		$bombeJoueur[$joueurEnCours] = 0;
		$joueurEnCours %= $nombreDeJoueurs;
		$joueurEnCours += 1;
		//Sauvegarde la position
		$tableauCoups[$numeroJoueur][0]= $x; $tableauCoups[$numeroJoueur][1]= $y;
		//Jouer le son Bombe chez tout le monde
		rajouterSonAJouer("bombe.wav",$ATOUS);
	} else if ($tableauDecouvert[$x][$y] != 9) {
		$message .= "<p class=message>Case d�j� d�couverte</p>";
	} else { // juste un coup
		//Sauvegarde la position
		$tableauCoups[$numeroJoueur][0] = $x; $tableauCoups[$numeroJoueur][1] = $y;
		//$message .= "Case jou�e : $tableauCoups[$numeroJoueur][0], $tableauCoups[$numeroJoueur][1]<br>";
		if (!coupEn($x, $y)) {
			$joueurEnCours %= $nombreDeJoueurs;
			$joueurEnCours += 1;
			//Jouer le son Coup chez tout le monde
			rajouterSonAJouer("coup.wav",$ATOUS);
			}
		else {
			//Jouer le son Mine chez tout le monde
			rajouterSonAJouer("mine.wav",$ATOUS);	
		}
	}
}

function devant($i,$j) {
	global $ATOUS, $nombreDeJoueurs, $joueurEnCours, $nomJoueur, $bombeJoueur, $minesDecouvertes, $nombreDeMines, $tailleX, $tailleY, $tableauMines, $tableauDecouvert, $message, $doitJouer, $partieChargee, $numeroJoueur, $tempsReel, $tableauSons, $tableauCoups;
	if (!$doitJouer) return "";
	if ($tableauDecouvert[$i][$j] != 9) return "";
	return "<a href=\"#\" onclick=\"joueEn($i,$j);\">";
}
function derriere($i,$j) {
	global $ATOUS, $nombreDeJoueurs, $joueurEnCours, $nomJoueur, $bombeJoueur, $minesDecouvertes, $nombreDeMines, $tailleX, $tailleY, $tableauMines, $tableauDecouvert, $message, $doitJouer, $partieChargee, $numeroJoueur, $tempsReel, $tableauSons, $tableauCoups;
	if (!$doitJouer) return "";
	if ($tableauDecouvert[$i][$j] != 9) return "";
	return "</a>";
}
function image($numero) {
	return "<img border=0 src=\"img/n$numero.png\">";
}
//Position d'un curseur pr�c�dent
function coupPrecedent($Joueur,$y) {
   global $largeurCase;
return "<img src='img/j-$Joueur.gif' border=0 width=16 style='position:relative; left: -".($largeurCase * $y)." px'>";
}
function videComplementaire() {
return "<img src='img/vide.gif' border=0 width=16>";
}
function appliquerStyleJeu($tailleY = 10) {
    echo '<style>
    /* Responsive grid and images */
    @media (max-width: 600px) {
      table, tr, td { width: 100% !important; }
      img { max-width: 100vw; height: auto; }
      .demineur-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        width: 100vw;
        max-width: 100vw;
      }
      .demineur-grid img {
        width: calc(95vw / ' . intval($tailleY) . ');
        min-width: 16px;
        max-width: 100vw;
        height: auto;
      }
    }
    .case-container {
      position: relative;
      display: inline-block;
      line-height: 0;
      vertical-align: top;
    }
    .case-img {
      width: 100%;
      height: auto;
      display: block;
    }
    .player-img {
      position: absolute;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
    }
    </style>';
}

function imageEntoureeOuPas($numero, $Joueur) {
    // Responsive: use a span container with two stacked images
    $imgNum = "<img src='img/n$numero.png' class='case-img'>";
    if (!$Joueur) {
        return "<span class='case-container'>$imgNum</span>";
    }
    $imgPlayer = "<img src='img/j-$Joueur.gif' class='player-img'>";
    return "<span class='case-container'>$imgNum$imgPlayer</span>";
}

function dessinerGrille($toutAfficher = FALSE) {
    global $ATOUS, $nombreDeJoueurs, $joueurEnCours, $nomJoueur, $bombeJoueur, $minesDecouvertes, $nombreDeMines, $tailleX, $tailleY, $tableauMines, $tableauDecouvert, $message, $doitJouer, $partieChargee, $numeroJoueur, $tempsReel, $tableauSons, $tableauCoups;
    $modifier = " width=16 height=16";
    for($i = 1; $i <= $tailleX; $i++)
        for($j = 1; $j <= $tailleY; $j++)
            $tableauCoupsPrecedents[$i][$j] = 0;
    for($i = 1; $i <= $nombreDeJoueurs; $i++)
        $tableauCoupsPrecedents[$tableauCoups[$i][0]][$tableauCoups[$i][1]] = $i;
    echo '<div class="demineur-grid" style="line-height:0;">';
    for($i = 1; $i <= $tailleX; $i++) {
        echo '<div style="display:inline-block;width:100%">';
        for($j = 1; $j <= $tailleY; $j++) {
            if ($toutAfficher) {
                echo "".image(($tableauDecouvert[$i][$j]!=9 ? $tableauDecouvert[$i][$j] : ($tableauMines[$i][$j] == 1?10:9)))."";
            } else {
                echo "".devant($i,$j).imageEntoureeOuPas($tableauDecouvert[$i][$j],$tableauCoupsPrecedents[$i][$j]).derriere($i,$j)."";
            }
        }
        echo '</div>';
    }
    echo '</div>';
}

function verifierSiGagnant() {
	global $ATOUS, $nombreDeJoueurs, $joueurEnCours, $nomJoueur, $bombeJoueur, $minesDecouvertes, $nombreDeMines, $minesAvantAlarme, $tailleX, $tailleY, $tableauMines, $tableauDecouvert, $message, $doitJouer, $partieChargee, $numeroJoueur, $tempsReel, $tableauSons, $tableauCoups, $joueMine;
	$unGagne = FALSE;
	$minesRestantes = $nombreDeMines;
	for($i=1;$i<=$nombreDeJoueurs;$i++) {
		$minesRestantes -= $minesDecouvertes[$i];
	}
	$nMaxMines = 0;
	$nSuivant = 0;
	for($i=1;$i<=$nombreDeJoueurs;$i++) {
		if ($minesDecouvertes[$i] > $nSuivant && $minesDecouvertes[$i] < $nMaxMines)
			$nSuivant = $minesDecouvertes[$i];
		if ($minesDecouvertes[$i] > $nMaxMines && $nSuivant < $nMaxMines)
			$nSuivant = $nMaxMines;
		$nMaxMines = max($nMaxMines,$minesDecouvertes[$i]);
	}
	$joueurAyantLePlusDeMines = array_keys($minesDecouvertes,$nMaxMines);
	if ($minesRestantes != 0) {
		if($minesDecouvertes[$numeroJoueur]==$nMaxMines && ($nMaxMines >= $nSuivant + $minesRestantes - 2*$minesAvantAlarme+1) && $joueMine && ($nMaxMines <= $nSuivant + $minesRestantes)) {
			for($i = 1; $i <= $nombreDeJoueurs; $i++)
				if($minesDecouvertes[$i] < $nMaxMines)	rajouterSonAJouer("alarme.wav",$i);
		}
		if (sizeof($joueurAyantLePlusDeMines) != 1) {
			$unGagne = FALSE;
		} else {
			$unGagne = ($nMaxMines > $nSuivant + $minesRestantes);
		}
	} else {
		$unGagne = TRUE;
	}
	if ($unGagne) {
		$plusieurs = (sizeof($joueurAyantLePlusDeMines) >= 2);
		$message .= "<p>Le".($plusieurs?"s":"")." joueur".($plusieurs?"s":"")." ";
		while(list($keys,$values)=each($joueurAyantLePlusDeMines))
			$message .= $nomJoueur[$values]." ".($plusieurs && $keys+1<sizeof($joueurAyantLePlusDeMines)?"& ":"");
		$message .= ($plusieurs?"ont":"a")." gagn� !</p>";
		if($minesDecouvertes[$numeroJoueur]==$nMaxMines) {
			rajouterSonAJouer("applaudissement.wav",$numeroJoueur);
		}
		else{
			rajouterSonAJouer("perdu.wav",$numeroJoueur);
		}
		return TRUE;
	}
	$message .= "<p>$minesRestantes mines restantes, max : $nMaxMines, suivant : $nSuivant</p>";
	return $unGagne;
}
?>