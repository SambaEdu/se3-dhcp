<?php


/**

   * Configuration du serveur dhcp
   * @Version $Id$
  
   * @Projet LCS / SambaEdu

   * @auteurs  « GrosQuicK »  eric.mercier@crdp.ac-versailles.fr
	
   * @note
   
   * @Licence  Distribue sous la licence GPL
   
*/
						

/**

   * @Repertoire: dhcp

   * file: config.php
*/


//  init html code
include "entete.inc.php";
include "ldap.inc.php";
include "ihm.inc.php";
require_once "dhcpd.inc.php";

$action=$_POST['action'];
if (is_admin("system_is_admin",$login)=="Y")
{
	
	//aide
	$_SESSION["pageaide"]="Le_module_DHCP#Configuration_du_serveur_DHCP";
	
	$content .= "<h1>".gettext("Param&#232;tres du serveur DHCP")."</h1>";
	switch($action) {
	case '' :
	case 'index' :
		$content .= dhcp_config_form("");
		$content .= dhcpd_status();
		break;
	
	case 'newconfig' :
		$error = dhcp_update_config();
		$content .= dhcp_config_form($error);
		if ($error=="") {dhcpd_restart();}
		$content .= dhcpd_status();
		break;
	case 'restart' :
		dhcpd_restart();
		$content .= dhcp_config_form("");
		$content .= dhcpd_status();
		break;
	case 'stop' :
		dhcpd_stop();
		$content .= dhcp_config_form("");
		$content .= dhcpd_status();
		break;

	default :
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
