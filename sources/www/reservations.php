<?php


/**

   * Gestion des baux du DHCP
   * @Version $Id$
  
   * @Projet LCS / SambaEdu

   * @auteurs « GrosQuicK »  eric.mercier@crdp.ac-versailles.fr
	
   * @note
   
   * @Licence Distribue sous la licence GPL
   
*/
						
/**
   * @Repertoire: dhcp
   * file: reservations.php
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

		
	$content .= "<h1>".gettext("R&#233;servations existantes")."</h1>";
	// Prepare HTML code
	switch($action) {
	case '' :
	case 'index' :
		$content.=form_existing_reservation();
		break;

	case 'valid' :
		$ip=$_POST['ip'];
		$mac=$_POST['mac'];
		$supprimer=$_POST['supprimer'];
		$name=$_POST['name'];
		$parc=$_POST['parc'];
		foreach ($ip as $keys=>$value) {
			if ($supprimer[$keys]) { $content .= suppr_reservation($ip[$keys],$mac[$keys],$name[$keys]);}
			if (($parc[$keys] != "none")&&($parc[$keys] != "")) { $content .= add_parc($ip[$keys],$mac[$keys],$name[$keys],$parc[$keys]);}
		}
		dhcpd_restart();
		$content.=form_existing_reservation();
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
