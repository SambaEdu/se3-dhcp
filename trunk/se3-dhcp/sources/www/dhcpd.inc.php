<?php

   /**

   * Fonctions du serveur DHCP
   * @Version $Id$

   * @Projet LCS / SambaEdu

   * @auteurs « GrosQuicK »  eric.mercier@crdp.ac-versailles.fr
   * @auteurs Plouf

   * @note Ce fichier de fonction doit etre appele par un include

   */

/**

   * @Repertoire: dhcp
   * file: dhcp.inc.php

*/


/**

* Affiche la conf du serveur DHCP

* @Parametres $error : Message d'erreur

* @Return Affichage HTML

*/

function dhcp_config_form($error)
{
	require_once ("ihm.inc.php");
	// Recuperation des donnees dans la base SQL
	$query = "SELECT * from params where name REGEXP '^dhcp*' ";
	$result = mysql_query($query);
	$ret = "<table>\n";
	// Menu select pour les vlan
	$nbr_vlan = dhcp_vlan_test();
	if($nbr_vlan > 0) {
		$i = 1;
		$ret .= "<form name=\"configuration\" method=\"post\" action=\"config.php\">\n";
	 	$ret .= "<tr><td>";
		$ret .= gettext("Vlan");
		$ret .= "</td><td>";
	 	$ret .= ": <select name=\"vlan\" onchange=submit()>";
		$ret .= "<option value=\"0\">D&#233;faut</option>";
		while ($i <= $nbr_vlan) {
         		$ret .= "<option ";
			if ($_POST['vlan'] == $i) { $ret .= "selected"; }
			$ret .= " value=\"$i\">vlan $i</option>";
			$i++;
		}
		$ret .= "</td><td></td></tr>\n";
		$ret .= "</form>\n";
	}

	// formulaire
	$ret .= "<form name=\"configuration\" method=\"post\" action=\"config.php\">\n";
	while ($resultat=mysql_fetch_assoc($result)) {
		$dhcp[$resultat['name']]["value"]=$resultat['value'];
		$dhcp[$resultat['name']]["descr"]=$resultat['descr'];
	}
	// dhcp_iface : interface d'ecoute du dhcp
	$ret .= "<tr><td>".gettext($dhcp["dhcp_iface"]["descr"])."</td><td>\n";
	$ret .= ": <input type=\"text\" SIZE=5 name=\"dhcp_iface\" value=\"".$dhcp["dhcp_iface"]["value"]."\" maxlength=\"5\"";
	if ($_POST['vlan'] > 0) { $ret .= " disabled "; }
	$ret .= ">";
	$ret .= "<b>".$error['dhcp_iface']."</b>";
	$ret .= "</td></tr>\n";

	// dhcp_domain_name : Nom du domaine
	$ret .= "<tr><td>".gettext($dhcp["dhcp_domain_name"]["descr"])."</td><td>\n";
	$ret .= ": <input type=\"text\" SIZE=60 name=\"dhcp_domain_name\" value=\"".$dhcp["dhcp_domain_name"]["value"]."\" maxlength=\"55\"";
	if ($_POST['vlan'] > 0) { $ret .= " disabled "; }
	$ret .= ">";
	$ret .= "<b>".$error['dhcp_domain_name']."</b>";
	$ret .= "</td></tr>\n";

	// dhcp_in_boot : dhcp start oon boot ? 0 ou 1
	$ret .= "<tr><td>".gettext($dhcp["dhcp_on_boot"]["descr"])."</td><td>\n";
	if ( $dhcp["dhcp_on_boot"]["value"]==1 ) {$CHECKED="CHECKED";} else {$CHECKED="";}
	$ret .= ": <input type=\"checkbox\" name=\"dhcp_on_boot\" $CHECKED ";
	if ($_POST['vlan'] > 0) { $ret .= " disabled "; }
	$ret .= ">";
	$ret .= "<b>".$error['dhcp_on_boot']."</b>";
	$ret .= "</td></tr>\n";

	// dhcp_max_lease : bail maximal
	$ret .= "<tr><td>".gettext($dhcp["dhcp_max_lease"]["descr"])."</td><td>\n";
	$ret .= ": <input type=\"text\" SIZE=10 name=\"dhcp_max_lease\" value=\"".$dhcp["dhcp_max_lease"]["value"]."\" maxlength=\"10\"";
	if ($_POST['vlan'] > 0) { $ret .= " disabled "; }
	$ret .= ">";
	$ret .= "<b>".$error['dhcp_max_lease']."</b>";
	$ret .= "</td></tr>\n";


	// dhcp_default_lease : bail par defaut
	$ret .= "<tr><td>".gettext($dhcp["dhcp_default_lease"]["descr"])."</td><td>\n";
	$ret .= ": <input type=\"text\" SIZE=10 name=\"dhcp_default_lease\" value=\"".$dhcp["dhcp_default_lease"]["value"]."\" maxlength=\"10\"";
	if ($_POST['vlan'] > 0) { $ret .= " disabled "; }
	$ret .= ">";
	$ret .= "<b>".$error['dhcp_default_lease']."</b>";
	$ret .= "</td></tr>\n";

	// dhcp_ntp : Serveur NTP
	$ret .= "<tr><td>".gettext($dhcp["dhcp_ntp"]["descr"])."</td><td>\n";
	$ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_ntp\" value=\"".$dhcp["dhcp_ntp"]["value"]."\"  maxlength=\"15\"";
	if ($_POST['vlan'] > 0) { $ret .= " disabled "; }
	$ret .= ">";
	$ret .= "<b>".$error['dhcp_ntp']."</b>";
	$ret .= "</td></tr>\n";

	// dhcp_wins : Serveur WINS
	$ret .= "<tr><td>".gettext($dhcp["dhcp_wins"]["descr"])."</td><td>\n";
	$ret .= ": <input type=\"text\" SIZE=20 name=\"dhcp_wins\" value=\"".$dhcp["dhcp_wins"]["value"]."\"maxlength=\"30\"";
	if ($_POST['vlan'] > 0) { $ret .= " disabled "; }
	$ret .= ">";
	$ret .= "<b>".$error['dhcp_wins']."</b>";
	$ret .= "</td></tr>\n";

	// dhcp_dns_server_prim : Serveur DNS primaire
	$ret .= "<tr><td>".gettext($dhcp["dhcp_dns_server_prim"]["descr"])."</td><td>\n";
	$ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_dns_server_prim\" value=\"".$dhcp["dhcp_dns_server_prim"]["value"]."\"maxlength=\"15\"";
	if ($_POST['vlan'] > 0) { $ret .= " disabled "; }
	$ret .= ">";
	$ret .= "<b>".$error['dhcp_dns_server_prim']."</b>";
	$ret .= "</td></tr>\n";

	// dhcp_dns_server_sec : Serveur DNS secondaire
	$ret .= "<tr><td>".gettext($dhcp["dhcp_dns_server_sec"]["descr"])."</td><td>\n";
	$ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_dns_server_sec\" value=\"".$dhcp["dhcp_dns_server_sec"]["value"]."\" maxlength=\"15\"";
	if ($_POST['vlan'] > 0) { $ret .= " disabled "; }
	$ret .= ">";
	$ret .= "<b>".$error['dhcp_dns_server_sec']."</b>";
	$ret .= "</td></tr>\n";

	// partie reserve si on a des vlan

	if ($_POST['vlan'] > 0) {
		// Adresse du reseau
		$ret .= "<tr><td>".gettext("Adresse de r&#233;seau ");
		$ret .= gettext(" du vlan ").$_POST['vlan'];
		$ret .= "</td><td>\n";
		$dhcp_reseau_vlan = "dhcp_reseau_".$_POST['vlan'];
		$ret .= ": <input type=\"text\" SIZE=15 name=\"$dhcp_reseau_vlan\" value=\"".$dhcp["$dhcp_reseau_vlan"]["value"]."\" maxlength=\"15\">";


		// Masque du reseau
		$ret .= "<tr><td>".gettext("Masque de r&#233;seau ");
		$ret .= gettext(" du vlan ").$_POST['vlan'];
		$ret .= "</td><td>\n";
		$dhcp_masque_vlan = "dhcp_masque_".$_POST['vlan'];
		$ret .= ": <input type=\"text\" SIZE=15 name=\"$dhcp_masque_vlan\" value=\"".$dhcp["$dhcp_masque_vlan"]["value"]."\" maxlength=\"15\">";

	}
	$ret .= "<b>".$error['dhcp_gateway']."</b>";
	$ret .= "</td></tr>\n";


	// dhcp_gateway : PASSERELLE

	if ($_POST['vlan'] > 0) {
		$ret .= "<tr><td>".gettext($dhcp["dhcp_gateway"]["descr"]);
		$ret .= gettext(" du vlan ").$_POST['vlan'];
		$ret .= "</td><td>\n";
		$dhcp_gateway_vlan = "dhcp_gateway_".$_POST['vlan'];
		$ret .= ": <input type=\"text\" SIZE=15 name=\"$dhcp_gateway_vlan\" value=\"".$dhcp["$dhcp_gateway_vlan"]["value"]."\" maxlength=\"15\">";
		$ret .= "<b>".$error['dhcp_gateway']."</b>";
		$ret .= "</td></tr>\n";
	} else {
		if ($nbr_vlan=="0") {
			$ret .= "<tr><td>".gettext($dhcp["dhcp_gateway"]["descr"]);
			$ret .= "</td><td>\n";
			$ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_gateway\" value=\"".$dhcp["dhcp_gateway"]["value"]."\" maxlength=\"15\">";
			$ret .= "<b>".$error['dhcp_gateway']."</b>";
			$ret .= "</td></tr>\n";
		}
	}

	// dhcp_begin_range : Debut de la plage
	if ($_POST['vlan'] > 0) {
		$ret .= "<tr><td>".gettext($dhcp["dhcp_begin_range"]["descr"]);
		$ret .= gettext(" du vlan ").$_POST['vlan'];
		$ret .= "</td><td>\n";
		$dhcp_begin_range_vlan = "dhcp_begin_range_".$_POST['vlan'];
		$ret .= ": <input type=\"text\" SIZE=15 name=\"$dhcp_begin_range_vlan\" value=\"".$dhcp["$dhcp_begin_range_vlan"]["value"]."\" maxlength=\"15\">";
		$ret .= "<b>".$error['dhcp_begin_range']."</b>";
		$ret .= "</td></tr>\n";
	} else {
		if ($nbr_vlan=="0") {
			$ret .= "<tr><td>".gettext($dhcp["dhcp_begin_range"]["descr"]);
			$ret .= "</td><td>\n";
			$ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_begin_range\" value=\"".$dhcp["dhcp_begin_range"]["value"]."\" maxlength=\"15\">";
			$ret .= "<b>".$error['dhcp_begin_range']."</b>";
			$ret .= "</td></tr>\n";
		}
	}

	// dhcp_end_range : Fin de la plage
	if ($_POST['vlan'] > 0) {
		$ret .= "<tr><td>".gettext($dhcp["dhcp_end_range"]["descr"]);
		$ret .= gettext(" du vlan ").$_POST['vlan'];
		$ret .= "</td><td>\n";
		$dhcp_end_range_vlan = "dhcp_end_range_".$_POST['vlan'];
		$ret .= ": <input type=\"text\" SIZE=15 name=\"$dhcp_end_range_vlan\" value=\"".$dhcp["$dhcp_end_range_vlan"]["value"]."\" maxlength=\"15\"";
		$ret .= "<b>".$error['dhcp_end_range']."</b>";
		$ret .= "</td></tr>\n";
	} else {
		if ($nbr_vlan=="0") {
			$ret .= "<tr><td>".gettext($dhcp["dhcp_end_range"]["descr"]);
			$ret .= "</td><td>\n";
			$ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_end_range\" value=\"".$dhcp["dhcp_end_range"]["value"]."\" maxlength=\"15\">";
			$ret .= "<b>".$error['dhcp_end_range']."</b>";
			$ret .= "</td></tr>\n";
		}
	}

	$ret .= "<tr><td></td><td></td></tr>\n";

	// dhcp_tftp_server : SERVER TFTP
	$ret .= "<tr><td>".gettext($dhcp["dhcp_tftp_server"]["descr"])."</td><td>\n";
	$ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_tftp_server\" value=\"".$dhcp["dhcp_tftp_server"]["value"]."\" maxlength=\"15\"";
	if ($_POST['vlan'] > 0) { $ret .= " disabled "; }
	$ret .= ">";
	$ret .= "<b>".$error['dhcp_tftp_server']."</b>";
	$ret .= "</td></tr>\n";

	// dhcp_unatt_filename fichier de boot PXE
	$ret .= "<tr><td>".gettext($dhcp["dhcp_unatt_filename"]["descr"])."</td><td>\n";
	$ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_unatt_filename\" value=\"".$dhcp["dhcp_unatt_filename"]["value"]."\" ";
	if ($_POST['vlan'] > 0) { $ret .= " disabled "; }
	$ret .= ">";
	$ret .= "<b>".$error['dhcp_unatt_filename']."</b>";
	$ret .= "</td></tr>\n";

	$ret .= "<tr><td></td><td></td></tr>\n";

	// UNATTENDED
	// dhcp_unattended_server
	//$ret .= "<tr><td>".gettext($dhcp["dhcp_unatt_server"]["descr"])."</td><td>\n";
	//$ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_unatt_server\" value=\"".$dhcp["dhcp_unatt_server"]["value"]."\" maxlength=\"15\">";
	//$ret .= "<b>".$error['dhcp_unatt_server']."</b>";
	//$ret .= "</td></tr>\n";
	// dhcp_unatt_login
	$ret .= "<tr><td>".gettext($dhcp["dhcp_unatt_login"]["descr"])."</td><td>\n";
	$ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_unatt_login\" value=\"".$dhcp["dhcp_unatt_login"]["value"]."\" ";
	if ($_POST['vlan'] > 0) { $ret .= " disabled "; }
	$ret .= ">";
	$ret .= "<b>".$error['dhcp_unatt_login']."</b>";
	$ret .= "</td></tr>\n";

	// dhcp_unatt_pass
	$ret .= "<tr><td>".gettext($dhcp["dhcp_unatt_pass"]["descr"])."</td><td>\n";
	$ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_unatt_pass\" value=\"".$dhcp["dhcp_unatt_pass"]["value"]."\" ";
	if ($_POST['vlan'] > 0) { $ret .= " disabled "; }
	$ret .= ">";
	$ret .= "<b>".$error['dhcp_unatt_pass']."</b>";
	$ret .= "</td></tr>\n";



	$ret .= "</table>";
	$ret .= "<input type='hidden' name='action' value='newconfig'>\n";
	$ret .= "<input type='hidden' name='vlan' value='".$_POST['vlan']."'>\n";
	$ret .= "<input type=\"submit\" name=\"button\" value=\"".gettext("Modifier")."\">\n";
	$ret .= "</form>\n";
	exec("sudo /usr/share/se3/scripts/makedhcpdconf state",$state);
	if ($state[0]=="1") {
		$ret .= "<form name=\"stop\" method=\"post\" action=\"config.php\">\n";
		$ret .= "<input type='hidden' name='action' value='stop'>\n";
		$ret .= "<input type=\"submit\" name=\"button\" value=\"".gettext("Stopper le serveur dhcp")."\">\n";
		$ret .="</form>";
	}
	else {
		$ret .= "<form name=\"stop\" method=\"post\" action=\"config.php\">\n";
		$ret .= "<input type='hidden' name='action' value='restart'>\n";
		$ret .= "<input type=\"submit\" name=\"button\" value=\"".gettext("Red&#233;marrer le serveur dhcp")."\">\n";
		$ret .="</form>";
	}


return $ret;
}


/**
* Mise a jour de la conf du dhcp dans la base SQL

* @Parametres

* @Return Erreur SQL

*/

function dhcp_update_config()  // insert range in option service table
{
	require_once ("ihm.inc.php");
	$error="";

	if($_POST['vlan'] > 0) {
		//verif si le chanmp existe dans la table sinon on le cree

		$dhcp_begin="dhcp_begin_range_".$_POST['vlan'];
		dhcp_vlan_champ($dhcp_begin);
		$dhcp_end="dhcp_end_range_".$_POST['vlan'];
		dhcp_vlan_champ($dhcp_end);
		$dhcp_gateway_vlan="dhcp_gateway_".$_POST['vlan'];
		dhcp_vlan_champ($dhcp_gateway_vlan);
		$dhcp_reseau="dhcp_reseau_".$_POST['vlan'];
		dhcp_vlan_champ($dhcp_reseau);
		$dhcp_masque="dhcp_masque_".$_POST['vlan'];
		dhcp_vlan_champ($dhcp_masque);
	} else {
		$dhcp_begin="dhcp_begin_range";
		$dhcp_end="dhcp_end_range";
		$dhcp_gateway_vlan="dhcp_gateway";
	}

	if ((set_ip_in_lan($_POST["$dhcp_begin"])) || ($_POST["$dhcp_begin"]=="")) {
		$update_query = "UPDATE params SET value='".$_POST["$dhcp_begin"]."' WHERE name='$dhcp_begin'";
		mysql_query($update_query);
	}
	else {
		$error["$dhcp_begin"]=gettext("Cette addresse n'est pas valide : ".$_POST["$dhcp_begin"]);
	}


	if ((set_ip_in_lan($_POST["$dhcp_end"]) || ($_POST["$dhcp_end"])=="")) {
		$update_query = "UPDATE params SET value='".$_POST["$dhcp_end"]."' WHERE name='$dhcp_end'";
		mysql_query($update_query);
	}
	else {
		$error["$dhcp_end"]=gettext("Cette adresse n'est pas valide : ".$_POST["$dhcp_end"]);
	}


	if ((set_ip_in_lan($_POST["$dhcp_gateway_vlan"])) || ($_POST["$dhcp_gateway_vlan"]=="")) {
		$update_query = "UPDATE params SET value='".$_POST["$dhcp_gateway_vlan"]."' WHERE name='$dhcp_gateway_vlan'";
		mysql_query($update_query);
	}
	else {
		$error["$dhcp_gateway_vlan"]=gettext("Cette adresse n'est pas valide : ".$_POST["$dhcp_gateway_vlan"]);
	}

	if ((set_ip_in_lan($_POST["$dhcp_reseau"])) || ($_POST["$dhcp_reseau"]=="")) {
		$update_query = "UPDATE params SET value='".$_POST["$dhcp_reseau"]."' WHERE name='$dhcp_reseau'";
		mysql_query($update_query);
	}
	else {
		$error["$dhcp_reseau"]=gettext("Cette addresse n'est pas valide : ".$_POST["$dhcp_reseau"]);
	}


	if ((set_ip_in_lan($_POST["$dhcp_masque"])) || ($_POST["$dhcp_masque"]=="")) {
		$update_query = "UPDATE params SET value='".$_POST["$dhcp_masque"]."' WHERE name='$dhcp_masque'";
		mysql_query($update_query);
	}
	else {
		$error["$dhcp_masque"]=gettext("Cette addresse n'est pas valide : ".$_POST["$dhcp_masque"]);
	}

	// Si on est dans la conf des vlan cette partie n'est pas modifiable

	if($_POST['vlan'] < 1) {
		if (set_ip_in_lan($_POST['dhcp_dns_server_prim'])) {
			$update_query = "UPDATE params SET value='".$_POST['dhcp_dns_server_prim']."' WHERE name='dhcp_dns_server_prim'";
			mysql_query($update_query);
		} else {
			$error["dhcp_dns_server_prim"]=gettext("Cette adresse n'est pas valide :".$_POST['dhcp_dns_server_prim']);
		}

		if ((set_ip_in_lan($_POST['dhcp_dns_server_sec']))||($_POST['dhcp_dns_server_sec']=="")) {
			$update_query = "UPDATE params SET value='".$_POST['dhcp_dns_server_sec']."' WHERE name='dhcp_dns_server_sec'";
			mysql_query($update_query);
		}
		else {
			$error["dhcp_dns_server_sec"]=gettext("Cette adresse n'est pas valide : ".$_POST['dhcp_dns_server_sec']);
		}


		if (preg_match('/,/',$_POST['dhcp_wins'])) {
			list($wins_ip_1,$wins_ip_2) = preg_split('/,/',$_POST['dhcp_wins']);
			if((set_ip_in_lan($wins_ip_1)) && (set_ip_in_lan($wins_ip_2))) {
				$update_query = "UPDATE params SET value='".$_POST['dhcp_wins']."' WHERE name='dhcp_wins'";
				 mysql_query($update_query);
			} else {
				$error["dhcp_wins"]=gettext("Une des adresses n'est pas valide : ".$_POST['dhcp_wins']);
			}
		} elseif ((set_ip_in_lan($_POST['dhcp_wins']))||($_POST['dhcp_wins']=="")) {
			$update_query = "UPDATE params SET value='".$_POST['dhcp_wins']."' WHERE name='dhcp_wins'";
			mysql_query($update_query);
		}
		else {
			$error["dhcp_wins"]=gettext("Cette adresse n'est pas valide : ".$_POST['dhcp_wins']);
		}

		if ((set_ip_in_lan($_POST['dhcp_ntp']))||($_POST['dhcp_ntp']=="")) {
			$update_query = "UPDATE params SET value='".$_POST['dhcp_ntp']."' WHERE name='dhcp_ntp'";
			mysql_query($update_query);
		}
		else {
			$error["dhcp_ntp"]=gettext("Cette adresse n'est pas valide : ".$_POST['dhcp_ntp']);
		}
		if (preg_match("/^[0-9]+$/",$_POST['dhcp_max_lease'])) {
			$update_query = "UPDATE params SET value='".$_POST['dhcp_max_lease']."' WHERE name='dhcp_max_lease'";
			mysql_query($update_query);
		}
		else {
			$error["dhcp_max_lease"]=gettext("Ce n'est pas un nombre valide : ".$_POST['dhcp_max_lease']);
		}

		if (preg_match("/^[0-9]+$/",$_POST['dhcp_default_lease'])) {
			$update_query = "UPDATE params SET value='".$_POST['dhcp_default_lease']."' WHERE name='dhcp_default_lease'";
			mysql_query($update_query);
		}
		else {
			$error["dhcp_default_lease"]=gettext("Ce n'est pas un nombre valide : ".$_POST['dhcp_default_lease']);

		}
		if (preg_match("/^eth[0-9]+$/",$_POST['dhcp_iface']) || preg_match("/^bond[0-9]+$/",$_POST['dhcp_iface'])) {
			$update_query = "UPDATE params SET value='".$_POST['dhcp_iface']."' WHERE name='dhcp_iface'";
			mysql_query($update_query);
		}
		else {
			$error["dhcp_iface"]=gettext("Ce n'est pas une interface valide : ".$_POST['dhcp_iface']);
		}
		if ($_POST['dhcp_on_boot']) {$value="1";} else {$value="0";}
		$update_query = "UPDATE params SET value='".$value."' WHERE name='dhcp_on_boot'";
		mysql_query($update_query);



		$update_query = "UPDATE params SET value='".$_POST['dhcp_domain_name']."' WHERE name='dhcp_domain_name'";
		mysql_query($update_query);


		// TFTP SERVER
		if ((set_ip_in_lan($_POST['dhcp_tftp_server']))||($_POST['dhcp_tftp_server']=="")) {
			$update_query = "UPDATE params SET value='".$_POST['dhcp_tftp_server']."' WHERE name='dhcp_tftp_server'";
			mysql_query($update_query);
		} else {
			$error["dhcp_tftp_server"]=gettext("Cette entr&#233;e n'est pas valide :").$_POST['dhcp_tftp_server'];
		}
		// unatt
		if ((set_ip_in_lan($_POST['dhcp_unatt_server']))||($_POST['dhcp_unatt_server']=="")) {
			$update_query = "UPDATE params SET value='".$_POST['dhcp_unatt_server']."' WHERE name='dhcp_unatt_server'";
			mysql_query($update_query);
		} else {
			$error["dhcp_tftp_server"]=gettext("Cette entr&#233;e n'est pas valide :").$_POST['dhcp_unatt_server'];
		}
		$update_query = "UPDATE params SET value='".$_POST['dhcp_unatt_login']."' WHERE name='dhcp_unatt_login'";
		mysql_query($update_query);
		$update_query = "UPDATE params SET value='".$_POST['dhcp_unatt_pass']."' WHERE name='dhcp_unatt_pass'";
		mysql_query($update_query);
		$update_query = "UPDATE params SET value='".$_POST['dhcp_unatt_filename']."' WHERE name='dhcp_unatt_filename'";
		mysql_query($update_query);

	}
	return $error;
}




	/**
	*  Test si l'adresse IP appartient au reseau local

	* @Parametres $ip : Adresse IP a tester

	* @Return  TRUE si oui - FLASE si non

	*/

function set_ip_in_lan($ip)
{
if (preg_match("/^(((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]{1}[0-9]|[1-9]).)".
	"{1}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]).)".
	"{2}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]){1}))$/",$ip)){
	return TRUE;
	} else {
	return FALSE;
	}
}


/**
* Parse le fichier dhcp.leases

* @Parametres $file : fichier dhcp.laeses

* @Return an associativ array : ["hostname"] / ("ip"] / [ "macaddr"] who are in dhcpd.lease and take ["parc"] entry if exist in ldap SORT by hostname

*/

function parse_dhcpd_lease ($file)
{
require_once("includes/ldap.inc.php");
$lease=file($file);
$compteur_clients=0;
$client["macaddr"][$compteur_clients]="";
$client["macaddr"][$compteur_clients]="";
$client["hostname"][$compteur_clients]="";
// $client["ip"][$compteur_clients]=$ip[0];
foreach ($lease as $compteur => $ligne)
	{
	if (preg_match("/^lease/",$ligne))  // for each "lease" we take IP / Mac Addr / hostname
		{
		preg_match ("/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/",$ligne,$ip);   // take IP
		$macaddr[0]=gettext("unresolved");
		$clienthostname[0]=gettext("unresolved");
		while (! preg_match("/^}/",$lease[$compteur]))
		{
			if (preg_match("/hardware ethernet/",$lease[$compteur])) // take mac addr
			{
				preg_match ("/[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}/",$lease[$compteur],$macaddr);
			}
			if (preg_match("/client-hostname/",$lease[$compteur])) // take name
			{
				preg_match ("/\"(.*)\"/",$lease[$compteur],$clienthostname);
				$clienthostname[0]=preg_replace("/\"/","",$clienthostname[0]);
			}
			$compteur=$compteur+1;
		}
		if ((! in_array($macaddr[0],$client["macaddr"]))&&($macaddr[0] != gettext("unresolved") )&&(! registred($macaddr[0]))) {
			$client["macaddr"][$compteur_clients]=$macaddr[0];
			$client["hostname"][$compteur_clients]=$clienthostname[0];

			if ($client["hostname"][$compteur_clients]==gettext("unresolved")) {
				$list_computer=search_machines("(&(cn=*)(ipHostNumber=$ip[0]))","computers");
				if ( count($list_computer)>1) {
					$resolutiondunom="doublon_ldap";
					$client["hostname"][$compteur_clients]=$resolutiondunom;

				} elseif ( count($list_computer)>0) {
					$resolutiondunom=$list_computer[0]['cn'];
					$client["hostname"][$compteur_clients]=$resolutiondunom;
				}
			}
			$client["ip"][$compteur_clients]=$ip[0];
			$client["parc"][$compteur_clients]=search_parcs ($clienthostname[0]);
			$compteur_clients++;
			}
		}
	}
	if (is_array($client["ip"])) {
		array_multisort($client["hostname"],SORT_ASC,$client["ip"],SORT_ASC,$client["macaddr"],SORT_ASC,$client["parc"]);
	}
	else {$client="";}
return $client;
}


/**
* MAKE a form with lease info get in dhcpd.lease

* @Parametres $parser : tableau : ip mac hostname

* @Return Affichage HTML d'un form a partir du dhcp.leases

*/


function dhcp_form_lease($parser)
{
	require_once("includes/ldap.inc.php");

	$content .= "<script type='text/javascript'>
function checkAll_baux(){
	champs_input=document.getElementsByTagName('input');
	for(i=0;i<champs_input.length;i++){
		type=champs_input[i].getAttribute('type');
		if(type==\"checkbox\"){
			champs_input[i].checked=true;
		}
	}
}
function UncheckAll_baux(){
	champs_input=document.getElementsByTagName('input');
	for(i=0;i<champs_input.length;i++){
		type=champs_input[i].getAttribute('type');
		if(type==\"checkbox\"){
			champs_input[i].checked=false;
		}
	}
}
</script>\n";

	$content .= "<form name=\"lease_form\" method=post action=\"baux.php\">\n";
	$content .= "<table border=\"1\" width=\"80%\">\n";
	$content .="<tr class=\"menuheader\"><td align=\"center\"><b>\n".gettext("Adresse IP");
	$content .= "</b></td><td align=\"center\"><b>\n".gettext("Adresse MAC");
	$content .="</b></td><td align=\"center\"><b>\n".gettext("Nom NETBIOS");
	$content .="</b></td><td align=\"center\"><b>\n".gettext("Parc(s)");
	$content .="</b></td><td align=\"center\"><b>\n".gettext("R&#233;server");
	//$content .= "</b></td></tr>\n";
	$content .= "</b><br />\n";
	$content .= "<a href='javascript: checkAll_baux();'><img src='../elements/images/enabled.png' width='20' height='20' border='0' alt='Tout cocher' /></a> / <a href='javascript:UncheckAll_baux();'><img src='../elements/images/disabled.png' width='20' height='20' border='0' alt='Tout d&#233;cocher' /></a>\n";
	$content .= "</td></tr>\n";
	foreach ($parser["ip"] as $keys=>$value) {
		if (! is_recorded_in_dhcp_database($parser["ip"][$keys],$parser["macaddr"][$keys],$parser["hostname"][$keys])) {
			$content .= "<tr><td>\n";
			$content .= "<input type=\"text\" maxlength=\"15\" SIZE=\"15\" value=\"".$parser["ip"][$keys]."\"  name=\"ip[$keys]\" >\n";
			$content .= "</td><td>\n";
			$content .= "<input type=\"text\" maxlength=\"17\" SIZE=\"17\" value=\"".$parser["macaddr"][$keys]."\"  name=\"mac[$keys]\" >\n";;
			$content .= "</td><td>\n";
			$content .= "<input type=\"text\" maxlength=\"20\" SIZE=\"20\" value=\"".$parser["hostname"][$keys]."\"  name=\"name[$keys]\" >\n";
			$content .="</td><td align=\"left\">\n";
			// Est-ce que cette machine est integree ?
			if (is_array(search_machines("(cn=".$parser["hostname"][$keys].")", "computers"))) {
				if (isset($parser["parc"][$keys])) {
					foreach($parser["parc"][$keys] as $keys2=>$value2) {

					$content.="<a href=../parcs/show_parc.php?parc=".$parser["parc"][$keys][$keys2]["cn"].">".$parser["parc"][$keys][$keys2]["cn"]."</a><br>\n";
					}
				}
				// ajouter a un parc dans lequel la machine n'est  pas ?
				$content .= add_to_parc($parser["parc"][$keys],$keys);
			}
			else { // this computer is not recorded on the domain
			$content.="<FONT color='red'>".gettext("Non int&#233;gr&#233;e au domaine")."</FONT>\n";
			}
			//
			$content .="</td><td align=\"center\">\n";
			$content .="<input type=checkbox name=\"reservation[$keys]\">\n";
			$content .="</td></tr>\n";
		}
	}
	$content .= "</table>\n";
	$content .= "<input type='hidden' name='action' value='valid'>\n";
	//$content .= "<input type=\"submit\" name=\"button\" value=\"".gettext("valider")."\">\n";
	$content .= "<input type=\"submit\" name=\"button\" value=\"".gettext("Valider les r&#233;servations")."\">\n";
	$content .= "</form>";
return $content;
}


/**
* form to modify entry in dhcpd reservation

* @Parametres $error : Message d'erreur

* @Return Affichage HTML d'un form

*/

function form_existing_reservation()
{
	require_once("includes/ldap.inc.php");
	require_once ("ihm.inc.php");
	// Recuperation des donnees dans la base SQL

	if (($_GET['order']=="") || ($_GET['order']=="ip")) {
		$query = "SELECT * FROM `se3_dhcp` ORDER BY INET_ATON(IP) ASC";

	} else {
		$query = "SELECT * FROM `se3_dhcp` ORDER BY ".$_GET['order']." ASC";
	}
	$result = mysql_query($query);
	//
	$clef=0;

	$content .= "<script type='text/javascript'>
function checkAll_reservations(){
	champs_input=document.getElementsByTagName('input');
	for(i=0;i<champs_input.length;i++){
		type=champs_input[i].getAttribute('type');
		if(type==\"checkbox\"){
			champs_input[i].checked=true;
		}
	}
}
function UncheckAll_reservations(){
	champs_input=document.getElementsByTagName('input');
	for(i=0;i<champs_input.length;i++){
		type=champs_input[i].getAttribute('type');
		if(type==\"checkbox\"){
			champs_input[i].checked=false;
		}
	}
}
</script>\n";

	$content .= "<form name=\"lease_form\" method=post action=\"reservations.php\">\n";
	$content .= "<table border=\"1\" width=\"80%\">\n";
	$content .="<tr class=\"menuheader\"><td align=\"center\"><b>\n<a href=\"reservations.php?order=ip\">".gettext("Adresse IP")."</a>";
	$content .= "</b></td><td align=\"center\"><b>\n<a href=\"reservations.php?order=mac\">".gettext("Adresse MAC")."</a>";
	$content .="</b></td><td align=\"center\"><b>\n<a href=\"reservations.php?order=name\">".gettext("Nom NETBIOS")."</a>";
	$content .="</b></td><td align=\"center\"><b>\n".gettext("Parc(s)");
	$content .="</b></td><td align=\"center\"><b>\n".gettext("Supprimer");
	$content .= "</b><br />\n";
	$content .= "<a href='javascript: checkAll_reservations();'><img src='../elements/images/enabled.png' width='20' height='20' border='0' alt='Tout cocher' /></a> / <a href='javascript:UncheckAll_reservations();'><img src='../elements/images/disabled.png' width='20' height='20' border='0' alt='Tout d&#233;cocher' /></a>\n";
	$content .= "</td></tr>\n";

	while ($row = mysql_fetch_assoc($result)) {
			$content .= "<tr><td align=\"center\">\n";
			//$content .= "<input type=\"text\" maxlength=\"15\" SIZE=\"15\" value=\"".$row["ip"]."\"  name=\"ip[$clef]\" readonly>\n";
			$content .= "<input type=\"hidden\" maxlength=\"15\" SIZE=\"15\" value=\"".$row["ip"]."\"  name=\"ip[$clef]\" readonly>\n";
			$content .= $row["ip"]."\n";
			$content .= "</td><td align=\"center\">\n";
			//$content .= "<input type=\"text\" maxlength=\"17\" SIZE=\"17\" value=\"".$row["mac"]."\"  name=\"mac[$clef]\" readonly>\n";;
			$content .= "<input type=\"hidden\" maxlength=\"17\" SIZE=\"17\" value=\"".$row["mac"]."\"  name=\"mac[$clef]\" readonly>\n";;
			$content .= $row["mac"]."\n";
			$content .= "</td><td align=\"center\">\n";
			//$content .= "<input type=\"text\" maxlength=\"20\" SIZE=\"20\" value=\"".$row["name"]."\"  name=\"name[$clef]\" readonly>\n";
			$content .= "<input type=\"hidden\" maxlength=\"20\" SIZE=\"20\" value=\"".$row["name"]."\"  name=\"name[$clef]\" readonly>\n";
			$content .= $row["name"]."\n";
			$content .="</td><td align=\"center\">\n";
			// Est-ce que cette machine est integree ?
			$parc[$clef]=search_parcs ($row["name"]);
			if (is_array(search_machines("(cn=".$row["name"].")", "computers"))) {
				if (isset($parc[$clef])) {
					foreach($parc[$clef] as $keys2=>$value2) {
						$content.="<a href=../parcs/show_parc.php?parc=".$parc[$clef][$keys2]["cn"].">".$parc[$clef][$keys2]["cn"]."</a><br>\n";
					}
				}
				// ajouter a un parc dans lequel la machine n'est  pas ?
				$content .= add_to_parc($parc[$clef],$clef);
			}
			else { // this computer is not recorded on the domain
			$content.="<FONT color='red'>".gettext("Non int&#233;gr&#233;e au domaine")."</FONT>\n";
			}
			$content .="</td><td align=\"center\">\n";
			$content .="<input type=checkbox name=\"supprimer[$clef]\">\n";
			$content .="</td></tr>\n";
		$clef++;
	}
	$content .= "</table>\n";
	$content .= "<input type='hidden' name='action' value='valid'>\n";
	//$content .= "<input type=\"submit\" name=\"button\" value=\"".gettext("valider")."\">\n";
	//$content .= "<input type=\"submit\" name=\"button\" value=\"".gettext("Supprimer les r&#233;servations coch&#233;es")."\">\n";
	$content .= "<input type=\"submit\" name=\"button\" value=\"".gettext("Valider les ajouts &#224; des parcs / Supprimer les r&#233;servations coch&#233;es")."\"> \n";
	$content .= "</form>";

return $content;
}


// Return select form whith parc where host is not recorded

/**
* Return select form whith parc where host is not recorded

* @Parametres $parcs parc dans lequel on veut ajouter -  $keys

* @Return Affichage HTML d'un select avec la liste des parcs

*/

function add_to_parc($parcs,$keys)
{
	require_once("includes/ldap.inc.php");
	$liste_parcs = search_machines("objectclass=groupOfNames","parcs");
	if (is_array($liste_parcs)) {
		$ret .="<SELECT  name=\"parc[$keys]\">";
		$ret .="<OPTION value=\"none\">".gettext("Ajouter &#224; un parc");
		foreach ($liste_parcs as $keys=>$value) {
			if (is_array($parcs)) {
				foreach($parcs as $keys2=>$value2) {
					$parc_tab[]=$parcs[$keys2]["cn"];
				}
			}
			else {$parc_tab[]="";}
			if (! in_array($value['cn'],$parc_tab)) {$ret .="<OPTION value=\"".$value['cn']."\">".$value['cn']."\n";}
		}
		$ret .= "</SELECT>";
	}
	return $ret;
}

/**
* Verifie si l'entree est dans la base SQL

* @Parametres $ip - $mac - $hostname

* @Return True - False

*/

function is_recorded_in_dhcp_database($ip,$mac,$hostname)
{
	require_once ("ihm.inc.php");
	// Recuperation des donnees dans la base SQL
	$query = "SELECT * from se3_dhcp where ip='$ip' and mac='$mac' and name='$hostname'";
	$result = mysql_query($query);
	$resultat=mysql_fetch_assoc($result);
	if ($resultat=="") {return FALSE;} else {return TRUE;}
}


/**
*Test la presence d'une adresse MAC dans la table se3_dhcp

* @Parametres  $mac

* @Return  True - False

*/

function registred($mac)
{
	require_once ("ihm.inc.php");
	$query = "SELECT * FROM `se3_dhcp` WHERE mac='$mac'";
	$result = mysql_query($query);
	$resultat=mysql_fetch_assoc($result);
	if ($resultat=="") {return FALSE;} else {return TRUE;}
}

// add entry in se3_dhcp mysql table for reservation

/**
* add entry in se3_dhcp mysql table for reservation

* @Parametres $ip - $mac - $name

* @Return $error

*/

function add_reservation($ip,$mac,$name)
{
	require_once ("ihm.inc.php");
	$error="";
	if (set_ip_in_lan($ip)) {
		$error=already_exist($ip,$name,$mac);
		if ( $error == "" ) {
			$insert_query = "INSERT INTO `se3_dhcp` (`ip`, `mac`, `name`) VALUES ('$ip', '$mac', '$name')";
			mysql_query($insert_query);
		}
	}
	else {
		$error=gettext("Cette addresse n'est pas valide : ".$ip);
	}
return $error;
}


/**
* Test si une reservation existe deja pour cette machine

* @Parametres $ip : ip de la machine - $name : nom de la machine - $mac : adresse mac de la machine

* @Return Affichage HTML si la machine existe deja

*/

function already_exist($ip,$name,$mac)
{
	require_once ("ihm.inc.php");
	// Recuperation des donnees dans la base SQL
	$query = "SELECT * from se3_dhcp where ip='$ip'";
	$result = mysql_query($query);
	$resultat=mysql_fetch_assoc($result);
	if ($resultat=="") {$error="";} else {$error=gettext("Cette adresse ip est d&#233;j&#224; utilis&#233;e :".$ip)."\n<br />";}

	$query = "SELECT * from se3_dhcp where mac='$mac'";
	$result = mysql_query($query);
	$resultat=mysql_fetch_assoc($result);
	if ($resultat=="") {$error.="";} else {$error.=gettext("Cette adresse mac est d&#233;j&#224; utilis&#233;e :".$mac)."\n<br />";}

	$query = "SELECT * from se3_dhcp where name='$name'";
	$result = mysql_query($query);
	$resultat=mysql_fetch_assoc($result);
	if ($resultat=="") {$error.="";} else {$error.=gettext("Ce nom est d&#233;j&#224; utilis&#233; :".$name)."\n<br />";}
	return $error;
}


/**
* Supprime une reservation

* @Parametres $ip : Ip de la machine - $mac : Adresse mac de la machine - $name : Nom de la machine

* @Return Message d'erreur SQL en cas de non suppression

*/

function suppr_reservation($ip,$mac,$name)
{
	require_once ("ihm.inc.php");
	$error="Suppression de l'entr&#233;e pour $ip<br>";
	$suppr_query = "DELETE FROM `se3_dhcp` where `ip` = '$ip' AND `mac` = '$mac' AND  `name` = '$name'";
	mysql_query($suppr_query);
return $error;
}


/**
* Ajoute une machine dans un parc

* @Parametres $ip : ip de la machine - $mac : adresse mac - $name : nom de la machine - $parc : Parc dans lequel on veut ajouter la machine

* @Return Affichage HTML d'un message d'ajout

*/

function add_parc($ip,$mac,$name,$parc)
{
	include("includes/config.inc.php");
	// include_once("includes/ldap.inc.php");
	$ret .= "Ajout de l'ordinateur $name au parc <U>$parc</U><BR>";
	$cDn = "cn=".$name.",".$computersRdn.",".$ldap_base_dn;
	$pDn = "cn=".$parc.",".$parcsRdn.",".$ldap_base_dn;
	exec ("/usr/share/se3/sbin/groupAddEntry.pl \"$cDn\" \"$pDn\"");
	// #NJ 10-2004 reconstruction des partages imprimantes par parc
	exec ("/usr/share/se3/sbin/printers_group.pl");
	return $ret;
}



/**
* Indique l'etat du serveur DHCP

* @Parametres
* @Return Affichage HTML sur l'etat

*/

function dhcpd_status()
{
	exec("sudo /usr/share/se3/scripts/makedhcpdconf state",$ret);
	if ($ret[0] == "1"){
		$content .= gettext("Le serveur DHCP est : ")."<FONT color='green'>".gettext("actif")."</FONT>";
	}

	else {
		$content .= gettext("Le serveur DHCP est : ")."<FONT color='red'>".gettext("inactif")."</FONT>";
	}
	return $content;
}


/**
* Redemarre le serveur DHCP

* @Parametres

* @Return

*/


function dhcpd_restart()
{
	exec("sudo /usr/share/se3/scripts/makedhcpdconf",$ret);
}


/**
* Stop le serveur DHCP

* @Parametres
* @Return

*/

function dhcpd_stop()
{
	exec("sudo /usr/share/se3/scripts/makedhcpdconf stop",$ret);
}


/**
* Valide le nom d'une machine

* @Parametres  $nom : Nom a valider

* @Return  0 si faux - 1 si Ok

*/

function valid_name($nom)
{
	$nom=strtoupper($nom);
	$l=strlen($nom);
	if ( $l == 0 )
	{
		print gettext("<br><I>le nom doit contenir au moins une lettre</I>");
		return 0;
	}
	if ( $l > 63 )
	{
		print gettext("<br><I>le nom $nom ne doit pas d&#233;passer 63 caract&#232;res</I>");
		return 0;
	}
        for ($i=0; $i <$l; $i++)
	{
		$c=substr($nom,$i,1);
	        if ( ! preg_match("/[a-zA-Z0-9_-]/",$c,$tab_err) )
	        {
			print gettext("<br><I>caract&#232;re $c incorrect dans hostname $nom </I>");
	        	return 0;
	        }
	}
        $prem=substr($nom,O,1);
        if ( ! preg_match("/[a-zA-Z0-9]/",$prem,$tab_err) )
        {
	        print gettext("<br><I>le nom $nom doit commencer par une lettre ou un chiffre</I>");
	        return 0;
	}
        $der=substr($nom,$l-1,1);
	if ( ! preg_match("/[a-zA-Z0-9]/",$der,$tab_err) )
	{
		print gettext("<br><I>le nom $nom doit finir par une lettre ou un chiffre</I>");
	        return 0;
	}
        return 1;
}



/**
* validation adresse MAC

* @Parametres  $mac adresse MAC a tester
* @Return  True si OK - False si adresse MAC pas correcte

*/

function valid_mac($mac)
{
	$tab_mac=explode(':',$mac); /* transforme adresse mac en tableau de 6 octets */
	if ( count($tab_mac)<>6) {
		print gettext("<br><I>Attention : une adresse MAC doit avoir la forme xx:xx:xx:xx:xx:xx</I>");
		return(0);
	}
	$mac=strtoupper($mac);
	$l=strlen($mac);
	for ($i=0; $i <$l; $i++) {
		$c=substr($mac,$i,1);
		if ( ! preg_match("/[A-F0-9:]/",$c,$tab_err) ) {
			print gettext("<br><I>caract&#232;re $c incorrect dans adresse mac $mac <I>");
			return 0;
		}
	}
	return 1;
}




/**
* Retourne une adresse MAC formatee en completant par des zeros a gauche


* @Parametres $ch_mac: Adresse MAC a traiter
* @Return Retourne une adresse MAC formatee en completant par des zeros a gauche, sinon retourne chaine vide

*/

function format_mac($ch_mac)
{
	$ch_mac=strtoupper($ch_mac);
	$mac_retour="";
	$tab_mac=explode(':',$ch_mac); /* transforme l'adresse mac en tableau de 4 chaines */
	if ( count($tab_mac) <> 6 ) {
		$z=count($tab_mac);
		print gettext("<br><I>Attention : une adresse mac doit avoir la forme xx:xx:xx:xx:xx:xx</I>");
		return("");
	} else 	{
		for ($i=0; $i <6; $i++) {
			while ( strlen($tab_mac[$i])<2 ) $tab_mac[$i]='0'.$tab_mac[$i];
	  		$mac_retour=$mac_retour.$tab_mac[$i];
	  		if ( $i <5 ) $mac_retour=$mac_retour.':'; /* on ajoute un point sauf au dernier */
		}
		/* verification caracteres valides */
		if ( ! preg_match("/[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}/",$mac_retour,$tab_err) ) {
        		print gettext("<br><I>Caract&#232;res interdits dans $mac_retour</I>");
        		return("");
  		}

		return($mac_retour);
	}
}



/**
* Validation liste hostname

* @Parametres $liste_name Nom separes par des espaces

* @Return  False et message d'erreur - True si Ok

* @note la liste doit etre une suite de noms de host separes par un espace

*/


function valid_list_name($liste_name)
{
	$liste_name=trim($liste_name);   /* supprime espaces a droite et a gauche */
	if ($liste_name == "" ) return 1;
 	$tab_name=explode(' ',$liste_name); /* transforme la liste de noms en tableau de noms */
	$nb_name=count($tab_name);
	for ( $i=0;$i<$nb_name;$i++ ) {
		$name=$tab_name[$i];
		if ( ! valid_name($name) ) {
			print gettext("<I>nom $name incorrect</I>");
			return 0;
		}
	}
  	return 1;
}




/**
* Importe dans la base SQL les imports a partit d'un csv

* @Parametres $tableau : l'import cvs des adresses  IP, Nom, MAC

* @Return Affichage HTML du resultat

*/


function traite_tableau($tableau)
{
	$nb_lignes=count($tableau);
	$separ=";";
	$z=0;
	$erreur=0;  // si erreur est vrai en sortie de boucle, annuler transaction
        $faux_nom=1;    // si jamais le nom n'est pas renseigne, on l'invente
                       // avec un numero
        while ($z < $nb_lignes ) {
		// sauter eventuelle ligne vide
	        // c'est souvent le cas pour la derniere ligne du presse-papier
	        if ($tableau[$z] == "" ) break;
		 // decoupage de chaque ligne a partir du separateur |
	         $tab_ligne=explode($separ,$tableau[$z]);
		 $ip=trim($tab_ligne[0]);
		 if ( ! set_ip_in_lan($ip) ) {
		 	print("<br>");
			print gettext("Erreur sur adresse ip : $tab_ligne[0]" );
			$ligne=$z+1;
			print(" Ligne n° $ligne");
			$erreur=1;
			$z++;
			continue;
		}
//		$ip=format_ip($ip);
		if ( $ip == "" ) {
			print("<br>");
			print gettext("Erreur sur adresse ip : $tab_ligne[0]" );
		        $ligne=$z+1;
		        print gettext(" Ligne n° $ligne");
		        $erreur=1;
			$z++;
		        continue;
		}
		$nom=trim($tab_ligne[1]);
		if ( ! valid_name($nom) ) {
			print("<br>");
			print gettext("Erreur sur hostname : $tab_ligne[1] " );
			$ligne=$z+1;
			print gettext("Ligne n° $ligne");
			$erreur=1;
			$z++;
			continue;
		}
		$mac=trim($tab_ligne[2]);
		if ( ! valid_mac($mac) )
		{
			print("<br>");
			print gettext("Erreur sur adresse mac : $tab_ligne[2] " );
		        $ligne=$z+1;
		        print gettext("Ligne n° $ligne");
		        $erreur=1;
			$z++;
		        continue;
		}
		$mac=format_mac($mac);
		if ( $mac == "" )
		{
			print("<br>");
			print gettext("Erreur sur adresse mac : $tab_ligne[2] " );
		        $ligne=$z+1;
		        print gettext("Ligne n° $ligne");
		        $erreur=1;
			$z++;
		        continue;
		}

		require_once ("ihm.inc.php");
		// Recuperation des donnees dans la base SQL
		$query = mysql_query("SELECT * from se3_dhcp where mac='$mac'");
		$v_count = mysql_num_rows($query);

		if ($v_count <> 0)
		{
			print("<br>");
			print gettext("Adresse mac $mac d&#233;ja utilis&#233;e ");
		        $ligne=$z+1;
		        print gettext(" Ligne n° $ligne");
		        $erreur=1;
			$z++;
		        continue;
		}
		$query = mysql_query("SELECT * from se3_dhcp where name='$nom'");
		$v_count = mysql_num_rows($query);
		if ($v_count <> 0)
// 		if (strtolower($query) == strtolower($nom))
		{
			print("<br>");
			print gettext("Hostname $nom d&#233;ja utilis&#233; ");
		        $ligne=$z+1;
		        print gettext(" Ligne n° $ligne");
		        $erreur=1;
			$z++;
		        continue;
		}
		$nominmaj = strtolower($nom);
		$query = mysql_query("SELECT * from se3_dhcp where name='$nominmaj'");
		$v_count = mysql_num_rows($query);
		if ($v_count <> 0)
		{
			print("<br>");
			print gettext("Hostname $nominmaj d&#233;ja utilis&#233; ");
		        $ligne=$z+1;
		        print gettext(" Ligne n° $ligne");
		        $erreur=1;
			$z++;
		        continue;
		}
		$query = mysql_query("SELECT * from se3_dhcp where ip='$ip'");
		$v_count = mysql_num_rows($query);
		if ($v_count <> 0)
		{
			print("<br>");
			print gettext("Adresse ip $ip d&#233;ja utilis&#233;e ");
		        $ligne=$z+1;
		        print gettext(" Ligne n° $ligne");
		        $erreur=1;
			$z++;
		        continue;
		}
        	// verifier si ip present dans la plage des disponibles
		$query = "SELECT * from se3_dhcp where ip='$ip'";
		$v_count = mysql_query($query);
		if ($v_count == 0)
		{
			print("<br>L'adresse $ip n'est pas dans la plage disponible ");
		        $ligne=$z+1;
		        print gettext(" Ligne n° $ligne");
		        $erreur=1;
			$z++;
		        continue;
		}
        	if ( $nom == "" )
	        {
			$nom="X".$faux_nom;
			$faux_nom++;
		}
        	if ( ! valid_name($nom) )
	        {
			print("<br>");
			print gettext("Nom $nom incorrect ");
			$ligne=$z+1;
			print gettext(" Ligne n° $ligne");
			$erreur=1;
			$z++;
			continue;
		}

	        $ip_int=ip2long($ip);
	        if ( ( $ip_int >= $dyn_first_int ) && ( $ip_int <= $dyn_last_int ) )
	        {
		        print ("<br><P><font color=#FF0000>$ip </font>: ");
			print gettext("r&#233;serv&#233;e en ip dynamique ");
	                $erreur=1;
			$z++;
	                continue;
	        }
	        // tout est ok, on insere la ligne
	        $v_query="insert into se3_dhcp values('NULL','$ip','$mac','$nom')";
		$v_count = mysql_query($v_query);
		if ( ! $v_query )
		        exit;
	          $z++;
	        }
        	if ( $erreur ) {
			print("<br><br><b>".gettext("Erreurs durant le traitement")."</b><br>");
		}

		print gettext("Traitement termin&#233;<br>");
		print gettext("Nb de lignes trait&#233;es : $z");
		dhcpd_restart();
		$mac="";
		$ip="";
		$nom="";
}




/**
* Fonctions: Test la presence de dhcp_vlan dans la table params et en retourne la valeur

* @Parametres $dhcp_vlan_valeur : Contenu de dhcp_vlan

* @Return - 0 si pas de vlan - n nombre de vlan

*/

function dhcp_vlan_test() {
	include ("config.inc.php");
	// si la variable dhcp_vlan n'est pas definie on cree l'entree dans la base sql
	if ($dhcp_vlan == "") {
	        $resultat=mysql_query("INSERT into params set id='NULL', name='dhcp_vlan', value='0', srv_id='0',descr='Nombre de vlan',cat='7'");
		return 0;

	} else {
		return $dhcp_vlan;
	}
}


/**
* Verifie l'existance des champs dans la table params pour les vlans

* @Parametres $nom_champ : Nom du champ a tester
* @Return


*/

function dhcp_vlan_champ($nom_champ)
{
	require_once ("config.inc.php");
	if ($$nom_champ == "") {
		$resultat=mysql_query("INSERT into params set id='NULL', name='$nom_champ', value='', srv_id='',descr='',cat='7'");
        }

}

?>
