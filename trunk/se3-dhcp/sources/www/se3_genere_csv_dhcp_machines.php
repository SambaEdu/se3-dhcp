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

		if(get_tab_attribut("computers","uid=$suppr[$i]$",$tab_attr_recherche)) {
			if(!del_entry("uid=$suppr[$i]$","computers")) {
				echo "Erreur lors de la suppression de l'entr&#233;e uid=$suppr[$i]$<br />\n";
			}
		}
	}

	// Faut-il aussi supprimer les uid=$suppr[$i]$ ?
}

echo "<p>Cette page est destin&#233;e &#224; g&#233;n&#233;rer un CSV d'apr&#232;s le contenu de l'annuaire LDAP.</p>\n";

search_doublons_mac('y');


?>
</body>
</html>
