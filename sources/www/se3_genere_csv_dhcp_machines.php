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


// HTMLPurifier
require_once ("traitement_data.inc.php");


$suppr_doublons_ldap = $_POST['suppr_doublons_ldap'];


include "entete.inc.php";
include "ldap.inc.php";
include "ihm.inc.php";
include("crob_ldap_functions.php");
include "printers.inc.php";
include "fonc_parc.inc.php";

require_once ("lang.inc.php");
bindtextdomain('se3-annu',"/var/www/se3/locale");
textdomain ('se3-dhcp');

//debug_var();

 // Aide
$_SESSION["pageaide"]="Le_module_DHCP#G.C3.A9n.C3.A9rer_le_CSV_d.27apr.C3.A8s_le_contenu_de_l.27annuaire_LDAP";

echo "<h1>".gettext("G&#233;n&#233;ration de CSV pour le DHCP")."</h1>\n";

if (!is_admin("se3_is_admin",$login)=="Y")  {
	echo "<p>Vous n'&#234;tes pas autoris&#233; &#224; acc&#233;der &#224; cette page.</p>\n";
	die("</body></html>\n");
}

// Suppression des doublons
if(isset($suppr_doublons_ldap)) {
	$suppr=isset($_POST['suppr']) ? $_POST['suppr'] : NULL;

	for($i=0;$i<count($suppr);$i++) {
		//echo "suppression_computer($suppr[$i])<br />";
		echo suppression_computer($suppr[$i]);
		//echo "<hr />";
	}
	echo "<hr />\n";
}

echo "<p>Cette page est destin&#233;e &#224; g&#233;n&#233;rer un CSV d'apr&#232;s le contenu de l'annuaire LDAP.</p>\n";

search_doublons_mac('n');

echo "<p><a href='export_ldap_csv.php'>Exporter le fichier csv g&#233;n&#233;r&#233; &#224; partir de l'annuaire ldap.</a></p>";


?>
</body>
</html>