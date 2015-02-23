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
require_once "fonc_parc.inc.php";
require_once "dhcpd.inc.php";

// HTMLpurifier
include("../se3/includes/library/HTMLPurifier.auto.php");
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);


$action = $purifier->purify($_POST['action']);

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
	$content .= "<u onmouseover=\"return escape".gettext("('Permet de purger les baux.<br>A n\'utiliser que lorsque des baux ne sont pas purg&#233;s.')")."\"><IMG style=\"border: 0px solid ;\" src=\"../elements/images/help-info.gif \"></u>\n";
	$content .= "</td></tr></table>\n";

	// Prepare HTML code
	switch($action) {
	case '' :
	case 'index' :
		$file="/var/lib/dhcp/dhcpd.leases";
		//$parser=parse_dhcpd_lease($file);
		$parser=my_parse_dhcpd_lease($file);
		if ($parser != "" ) {
			//$content .= dhcp_form_lease($parser);
			$content .= my_dhcp_form_lease($parser);
		}
		else {
		$content .= gettext("Aucun bail actif pour le moment.");
		}
		break;
	
	case 'valid' :
		$ip=$purifier->purify($_POST['ip']);
		$mac=$purifier->purify($_POST['mac']);
		$action_res=$purifier->purify($_POST['action_res']);
		$name=$purifier->purify($_POST['name']);
		$oldname=$purifier->purify($_POST['name']);
		$parc=$purifier->purify($_POST['parc']);	    
                $localadminname=$purifier->purify($_POST['localadminname']);
                $localadminpasswd=$purifier->purify($_POST['localadminpasswd']);
                
		foreach ($ip as $keys=>$value) {
			if ($action_res[$keys]=="reserver") { 
			    $content .= add_reservation($ip[$keys],$mac[$keys],strtolower($name[$keys]));
                            
			}
			elseif ($action_res[$keys]=="integrer") { 
//			    $content .= "<FONT color='red'>".add_reservation($ip[$keys],$mac[$keys],strtolower($name[$keys]))."</FONT>";
			    if ($localadminpasswd[$keys] == "") { $localadminpasswd[$keys]="xxx"; }
			    $content .= "<FONT color='red'>".integre_domaine($ip[$keys],$mac[$keys],strtolower($name[$keys]),$localadminname[$keys],$localadminpasswd[$keys])."</FONT>";
			}
			elseif ($action_res[$keys]=="renommer") {
//			    $content .= add_reservation($ip[$keys],$mac[$keys],strtolower($name[$keys]));
		        $content .= renomme_domaine($ip[$keys],strtolower($oldname[$keys]),strtolower($name[$keys]));
		    }
			if (($parc[$keys] != "none")&&($parc[$keys] != "")) { $content .= add_machine_parc(strtolower($name[$keys]),$parc[$keys]);}
		}
		$file="/var/lib/dhcp/dhcpd.leases";
		//$parser=parse_dhcpd_lease($file);
		$parser=my_parse_dhcpd_lease($file);
		if ($parser != "" ) {
			//$content .= dhcp_form_lease($parser);
			$content .= my_dhcp_form_lease($parser);
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
