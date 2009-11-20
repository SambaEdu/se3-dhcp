<?php


/**

   Fonctions Gestion des baux du DHCP
   * @Version $Id: baux.php 1646 2007-01-05 20:25:10Z plouf
   
   * @Projet LCS / SambaEdu

   * @auteurs - Eric Mercier (Academie de Versailles)	

   * @note
   
   * @Licence  Distribue sous la licence GPL
   
*/

/**

   * @Repertoire: dhcp

   * file: baux.php

*/


// loading libs and init
include "entete.inc.php";
include "ldap.inc.php";
include "ihm.inc.php";
require_once "dhcpd.inc.php";


$action=$_POST['action'];
if (is_admin("system_is_admin",$login)=="Y")
{


	//aide
	$_SESSION["pageaide"]="Le_module_DHCP#G.C3.A9rer_les_baux_et_r.C3.A9server_des_IPs";
	
	// Supprime dhcpd.leases
	if ($action=="reinit") {
		exec("/usr/bin/sudo /usr/share/se3/scripts/move_dhcp_leases.sh");
		$action="";
	}
	
	$content .= "<h1>".gettext("Baux actifs")."</h1>";
	
	// Permet de vider le fichier dhcp.leases
	$content .= "<table><tr><td>";
	$content .= "<form name=\"lease_form\" method=post action=\"baux.php\">\n";
	$content .= "<input type='hidden' name='action' value='reinit'>\n";	
	$content .= "<input type=\"submit\" name=\"button\" value=\"".gettext("R&#233;initialiser")."\">\n";	
	$content .= "</form>\n";
	$content .= "</td><td>";
	$content .= "<u onmouseover=\"return escape".gettext("('Permet de purger les baux.<br>A n\'utiliser que lorsque des baux ne sont pas purg&#233;s.')")."\"><IMG style=\"border: 0px solid ;\" src=\"../elements/images/system-help.png \"></u>\n";
	$content .= "</td></tr></table>\n";

	// Prepare HTML code
	switch($action) {
	case '' :
	case 'index' :
		$file="/var/lib/dhcp3/dhcpd.leases";
		$parser=parse_dhcpd_lease($file);
		if ($parser != "" ) {
			$content .= dhcp_form_lease($parser);
		}
		else {
		$content .= gettext("Aucun bail actif pour le moment.");
		}
		break;
	
	case 'valid' :
		$ip=$_POST['ip'];
		$mac=$_POST['mac'];
		$reservation=$_POST['reservation'];
		$name=$_POST['name'];
		$parc=$_POST['parc'];
		foreach ($ip as $keys=>$value) {
			if ($reservation[$keys]) { $content .= "<FONT color='red'>".add_reservation($ip[$keys],$mac[$keys],$name[$keys])."</FONT>";}
			if (($parc[$keys] != "none")&&($parc[$keys] != "")) { $content .= add_parc($ip[$keys],$mac[$keys],$name[$keys],$parc[$keys]);}
		}
		$file="/var/lib/dhcp3/dhcpd.leases";
		$parser=parse_dhcpd_lease($file);
		if ($parser != "" ) {
			$content .= dhcp_form_lease($parser);
		}
		else {
		$content .= gettext("Aucun bail actif pour le moment.");
		}
		dhcpd_restart();
		break;
		
	default :
		// anti  hacking
		$title = '';
		$content = '';
		return;
	}
	
	
	
	print "$content\n";
	}
else
{
print (gettext("Vous n'avez pas les droits n&#233;cessaires pour ouvrir cette page..."));
}

// Footer
include ("pdp.inc.php");


?>
