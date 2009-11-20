<?php


/**

   * @Import - Export les entrees machines depuis l'annuaire LDAP
   * @Version $Id$

   * @Projet LCS / SambaEdu

   * @auteurs Stephane Boireau

   * @note

   * @Licence Distribue sous la licence GPL

*/

/**
   * @Repertoire: dhcp
   * file: se3_genere_csv_dhcp_machines.php
*/



//==================================
// Generation du CSV:
if(isset($_POST['genere_csv'])) {
	$cn=isset($_POST['cn']) ? $_POST['cn'] : NULL;
	$ip=isset($_POST['ip']) ? $_POST['ip'] : NULL;
	$mac=isset($_POST['mac']) ? $_POST['mac'] : NULL;

	if((isset($ip))&&(isset($cn))&&(isset($mac))) {
		$nom_fic = "se3_dhcp_".strftime("%Y%m%d-%H%M%S").".csv";
		$now = gmdate('D, d M Y H:i:s') . ' GMT';
		header('Content-Type: text/x-csv');
		header('Expires: ' . $now);
		// lem9 & loic1: IE need specific headers
		if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) {
			header('Content-Disposition: inline; filename="' . $nom_fic . '"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		} else {
			header('Content-Disposition: attachment; filename="' . $nom_fic . '"');
			header('Pragma: no-cache');
		}
		$fd = '';
		for($i=0;$i<count($ip);$i++) {
			if((isset($ip[$i]))&&(isset($cn[$i]))&&(isset($mac[$i]))) {
				$fd.="$ip[$i];$cn[$i];$mac[$i]\n";
			}
		}
		echo $fd;
	}
	die();
}

include "entete.inc.php";
include "ldap.inc.php";
include "ihm.inc.php";
include("crob_ldap_functions.php");

require_once ("lang.inc.php");
bindtextdomain('se3-annu',"/var/www/se3/locale");
textdomain ('se3-dhcp');

 // Aide
$_SESSION["pageaide"]="Le_module_DHCP#G.C3.A9n.C3.A9rer_le_CSV_d.27apr.C3.A8s_le_contenu_de_l.27annuaire_LDAP";

echo "<h1>".gettext("G&#233;n&#233;ration de CSV pour le DHCP")."</h1>\n";

if (!is_admin("se3_is_admin",$login)=="Y")  {
	echo "<p>Vous n'&#234;tes pas autoris&#233; &#224; acc&#233;der &#224; cette page.</p>\n";
	die("</body></html>\n");
}

// Suppression des doublons
if(isset($_POST['suppr_doublons_ldap'])) {
	$suppr=isset($_POST['suppr']) ? $_POST['suppr'] : NULL;

	$tab_attr_recherche=array('cn');
	for($i=0;$i<count($suppr);$i++) {
		if(get_tab_attribut("computers","cn=$suppr[$i]",$tab_attr_recherche)) {
			if(!del_entry("cn=$suppr[$i]","computers")) {
				echo "Erreur lors de la suppression de l'entr&#233;e $suppr[$i]<br />\n";
			}
		}
	}

	// Faut-il aussi supprimer les uid=$suppr[$i]$ ?
}

echo "<p>Cette page est destin&#233;e &#224; g&#233;n&#233;rer un CSV d'apr&#232;s le contenu de l'annuaire LDAP.</p>\n";

$ldap_computer_attr=array("cn","iphostnumber","macaddress");
// Connexion au LDAP
$idconnexionldap=@ldap_connect($ldap_server,$ldap_port);

if ($idconnexionldap) {
	//$idliaisonldap=ldap_bind($idconnexionldap,$adminldap,$passadminldap);
	//If this is an "anonymous" bind, typically read-only access:
	$idliaisonldap=ldap_bind($idconnexionldap);

	echo "<p>";
	//echo "Recherche des machines dans la branche ".$dn['computers']."\n";
	echo "Recherche des machines dans la branche 'computers': \n";
	//echo "<br />\n";

	$rechercheldap=ldap_search($idconnexionldap,$dn['computers'], "macaddress=*");
	//echo "Le nombre d'entr&#233;es retourn&#233;es est: ".ldap_count_entries($idconnexionldap,$rechercheldap)."\n";
	echo ldap_count_entries($idconnexionldap,$rechercheldap)." machines trouv&#233;es\n";
	echo "</p>\n";
	echo "<p>Parcours des entr&#233;es \n";
	//echo "<br />\n";
	$info = ldap_get_entries($idconnexionldap, $rechercheldap);
	//==================================================
	/*
	echo "<p>";
	echo "\tDonn&#233;es pour ".$info["count"]." entr&#233;es:\n";
	echo "<br />\n";
	for ($i=0; $i<$info["count"]; $i++) {
		echo "<p>";
		// Il peut y avoir plusieurs attributs de meme nom pour une entr&#233;e (ex.: memberUid)
		echo "\t\tLe premier attribut 'cn' de l'entr&#233;e courante est : ". $info[$i]["cn"][0] ."\n";
		echo "<br />\n";

		for($k=0;$k<count($ldap_computer_attr);$k++){

			for($j=0;$j<$info[$i][$ldap_computer_attr[$k]]["count"];$j++){
				echo "\t\t\t$ldap_computer_attr[$k]: ".$info[$i][$ldap_computer_attr[$k]][$j]."\n";
				echo "<br />\n";
			}
		}
	}
	*/
	//==================================================

	// Recherche des doublons
	$tab_machine=array();
	$tab_mac=array();
	$tab_doublons_mac=array();
	$cpt=0;
	for ($i=0; $i<$info["count"]; $i++) {
		if((isset($info[$i]["cn"][0]))&&(isset($info[$i]["iphostnumber"][0]))&&(isset($info[$i]["macaddress"][0]))) {
			//echo $info[$i]["iphostnumber"][0].";".$info[$i]["cn"][0].";".$info[$i]["macaddress"][0]."\n";
			$tab_machine[$cpt]=array();
			$tab_machine[$cpt]['ip']=$info[$i]["iphostnumber"][0];
			$tab_machine[$cpt]['cn']=$info[$i]["cn"][0];
			$tab_machine[$cpt]['mac']=$info[$i]["macaddress"][0];

			if(in_array($info[$i]["macaddress"][0],$tab_mac)) {
				if(!in_array($info[$i]["macaddress"][0],$tab_doublons_mac)) {
					$tab_doublons_mac[]=$info[$i]["macaddress"][0];
				}
			}
			else {
				$tab_mac[]=$info[$i]["macaddress"][0];
			}
			$cpt++;
		}
	}

	// Parcourir $tab_machine et si on tombe sur une valeur dans $tab_doublons_mac, on affiche qu'il y a un pb... proposer de supprimer de l'annuaire des entr&#233;es
	// Pour le reste, remplir un autre tableau qui apres validation sert a g&#233;n&#233;rer le CSV.
	/*
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
	for($i=0;$i<count($tab_machine);$i++) {
		if(in_array($tab_machine[$i]['macaddress'],$tab_doublons_mac)) {
			echo "<input type='checkbox' name='' value='' />\n";
		}
	}
	echo "<input type='submit' name='suppr_doub_ldap' value='Supprimer' />\n";
	echo "</form>\n";
	*/

	// Tableau des doublons pour permettre la suppression
	echo "pour rechercher des doublons.";
	if(count($tab_doublons_mac)>0) {
		echo "</p>\n";
		echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo "<table border='1'>\n";
		echo "<tr class=\"menuheader\" height=\"30\">\n";
		echo "<th style='text-align:center;'>Nom Netbios</th>\n";
		echo "<th style='text-align:center;'>IP</th>\n";
		echo "<th style='text-align:center;'>MAC</th>\n";

		echo "<th style='text-align:center;'>Connexion</th>\n";

		echo "<th style='text-align:center;'>Supprimer</th>\n";
		echo "</tr>\n";
		$alt=1;
		for($i=0;$i<count($tab_doublons_mac);$i++) {
			//echo "<tr><td colspan='4'>$tab_doublons_mac[$i]</td></tr>\n";
			$alt=$alt*(-1);

			for($j=0;$j<count($tab_machine);$j++) {
				if($tab_machine[$j]['mac']==$tab_doublons_mac[$i]) {
					echo "<tr";
					if($alt==-1) {echo " style='background-color:silver;'";}
					echo ">\n";
					echo "<td style='text-align:center;'>".$tab_machine[$j]['cn']."</td>\n";
					echo "<td style='text-align:center;'>".$tab_machine[$j]['ip']."</td>\n";
					echo "<td style='text-align:center;'>".$tab_machine[$j]['mac']."</td>\n";

					$sql="SELECT * FROM connexions WHERE netbios_name='".$tab_machine[$j]['cn']."' ORDER BY logintime DESC LIMIT 1;";
					$res_connexion=mysql_query($sql);
					if(mysql_num_rows($res_connexion)==0) {
						echo "<td style='text-align:center;color:red;'>X</td>\n";
					}
					else {
						$lig_connexion=mysql_fetch_object($res_connexion);
						echo "<td style='text-align:center;'>".$lig_connexion->logintime."</td>\n";
					}

					echo "<td style='text-align:center;'><input type='checkbox' name='suppr[]' value='".$tab_machine[$j]['cn']."' /></td>\n";
					echo "</tr>\n";
				}
			}
		}
		echo "<tr><td style='text-align:center;' colspan='4'>";
		echo "<input type='submit' name='suppr_doublons_ldap' value=\"Supprimer de l'annuaire LDAP\" />\n";
		echo "<br />\n";
		echo "<b>ATTENTION:</b> Si la machine est toujours pr&#233;sente, conservez une entr&#233;e pour chaque n-uplet.<br />Ne cochez pas toutes les entr&#233;es sans discernement.\n";
		echo "</td></tr>\n";
		echo "</table>\n";
		echo "</form>\n";

		echo "<p><i>NOTE:</i> Il arrive qu'une m&#234;me machine (<i>m&#234;me adresse MAC</i>) apparaisse avec plusieurs noms dans l'annuaire LDAP.<br />Cela peut se produire lorsque l'on renomme une machine et que l'on ne fait pas le m&#233;nage dans l'annuaire.</p>\n";
	}
	else {
		echo ": Aucun doublon trouv&#233;.</p>\n";
	}

	if(count($tab_machine)>0) {
		echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo "<p>\n";
		for($j=0;$j<count($tab_machine);$j++) {
			echo "<input type='hidden' name='cn[$j]' value='".$tab_machine[$j]['cn']."' />\n";
			echo "<input type='hidden' name='ip[$j]' value='".$tab_machine[$j]['ip']."' />\n";
			echo "<input type='hidden' name='mac[$j]' value='".$tab_machine[$j]['mac']."' />\n";
		}
		echo "<input type='submit' name='genere_csv' value=\"G&#233;n&#233;rer le CSV\" />\n";
		echo "</p>\n";
		echo "</form>\n";
	}
}
else{
	echo "<p>ERREUR: La connexion au LDAP a &#233;chou&#233;.<p/>\n";
}
?>
</body>
</html>
