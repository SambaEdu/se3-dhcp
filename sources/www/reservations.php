<?php

/**

 * Gestion des baux du DHCP
 * @Version $Id$

 * @Projet LCS / SambaEdu

 * @auteurs  GrosQuicK   eric.mercier@crdp.ac-versailles.fr

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
include("crob_ldap_functions.php");
include "printers.inc.php";
require_once "fonc_parc.inc.php";
require_once "dhcpd.inc.php";
?>

<script type="text/javascript" src="/elements/js/wz_tooltip_new.js"></script>

<?php

$action = isset($_POST['action']) ? $_POST['action'] : '';
$suppr_doublons_ldap = isset($_POST['suppr_doublons_ldap']) ? $_POST['suppr_doublons_ldap'] : '';

if (is_admin("system_is_admin",$login)!="Y")
	die (gettext("Vous n'avez pas les droits suffisants pour acc&#233;der &#224; cette fonction")."</BODY></HTML>");


    //aide
    $_SESSION["pageaide"] = "Le_module_DHCP#G.C3.A9rer_les_baux_et_r.C3.A9server_des_IPs";


    @$content .= "<h1>" . gettext("R&#233;servations existantes") . "</h1>";

// Permet de vider les resa
	$content .= "<table><tr><td>";
	$content .= "<form name=\"lease_form\" method=post action=\"reservations.php\">\n";
	$content .= "<input type='hidden' name='action' value='cleanresa'>\n";	
	$content .= "<input type=\"submit\" name=\"button\" value=\"".gettext("Supprimer toutes les r&#233;servations ")."\" onclick=\"if (window.confirm('supression de toutes les r&#233;servations ?')) {return true;} else {return false;}\">\n";	
	$content .= "</form>\n";
	$content .= "</td><td>";
	$content .= "<u onmouseover=\"return escape".gettext("('Permet de supprimer toutes les r&#233;servations de la base. Utile par exemple en cas de changement de plan d\'adressage.')")."\"><IMG style=\"border: 0px solid ;\" src=\"../elements/images/help-info.gif \"></u>\n";
	$content .= "</td></tr></table>\n";


/*

echo "<form action=\"reservations.php\" method=\"post\">\n";
echo "<input  type=\"submit\" value=\"Supprimer toutes les r&#233;servations existantes\" onclick=\"if (window.confirm('supression de toutes les r&#233;servations ?')) {return true;} else {return false;}\"/>";
echo "</form>";*/

    // Prepare HTML code
    switch ($action) {
        case '' :
        case 'index' :
            $content.=form_existing_reservation();
            break;
	case 'cleanresa' :
		$dhcp_link=connexion_db_dhcp();
	    $query="TRUNCATE se3_dhcp";
	    mysqli_query($dhcp_link,$query);
		deconnexion_db_dhcp($dhcp_link);
	    dhcpd_restart();
            $content.=form_existing_reservation();
            break;

        case 'valid' :
            $ip = isset($_POST['ip']) ? $_POST['ip'] : '';
            $mac = isset($_POST['mac']) ? $_POST['mac'] : '';
            $localadminname = isset($_POST['localadminname']) ? $_POST['localadminname'] : '';
            $localadminpasswd = isset($_POST['localadminpasswd']) ? $_POST['localadminpasswd'] : '';
            $oldname = isset($_POST['oldname']) ? $_POST['oldname'] : '';
            $name = isset($_POST['name']) ? $_POST['name'] : '';
            $parc = isset($_POST['parc']) ? $_POST['parc'] : '';
            $action_res = isset($_POST['action_res']) ? $_POST['action_res'] : '';
			if ($ip != "")
			{
				foreach ($ip as $keys => $value)
				{
					if ($action_res[$keys] == "integrer")
					{
						if ($localadminpasswd[$keys] == "")
						{
							$localadminpasswd[$keys] = "xxx";
						}
						$content .= "<FONT color='red'>" . integre_domaine($ip[$keys], $mac[$keys], strtolower($name[$keys]), $localadminname[$keys], $localadminpasswd[$keys]) . "</FONT>";
					}
					elseif ($action_res[$keys] == "actualiser")
					{
						$content .= renomme_reservation($ip[$keys], $mac[$keys], strtolower($name[$keys]));
					}
					elseif ($action_res[$keys] == "newip")
					{
						$content .= change_ip_reservation($ip[$keys], $mac[$keys], strtolower($name[$keys]));
						//$content .= "<FONT color='red'>" . "Attention";
					}
					elseif ($action_res[$keys] == "renommer-linux")
					{
						$ret = already_exist("ipbidon", strtolower($name[$keys]), "macbidon");
						if ($ret == "")
						{
							exec("/usr/share/se3/sbin/tcpcheck 4 $ip[$keys]:22 | grep alive",$arrval,$return_value);
							if ($return_value == "1")
							{
								$content .= gettext("<p style='color:red;'>Attention  : Renommage de $oldname[$keys] impossible. La machine est injoignable en ssh :  </p>\n " );
							}
							else
							{
								$content .= renomme_linux($ip[$keys], $oldname[$keys], strtolower($name[$keys]));
								$content .= renomme_reservation($ip[$keys], $mac[$keys], strtolower($name[$keys]));
								$content .= renomme_machine_parcs(strtolower($oldname[$keys]), strtolower($name[$keys]));
							}   
						}
						else
						{
							$content .= gettext("<p style='color:red;'>Attention : Le nom $name[$keys] n'est pas valide ou existe d&#233;j&#224;");

						}
					}
					elseif ($action_res[$keys] == "reintegrer")
					{
						exec("/usr/share/se3/sbin/tcpcheck 4 $ip[$keys]:445 | grep alive",$arrval,$return_value);
						if ($return_value == "1")
						{
							$content .= gettext("<p style='color:red;'>Attention  : R&#233;int&#233;gration de $oldname[$keys] impossible. La machine est injoignable ou prot&#233;g&#233;e par un pare-feu  :  </p>\n " );
						}
						else
						{
							$content .= renomme_domaine($ip[$keys], $oldname[$keys], strtolower($name[$keys]));
						}
					}
					elseif ($action_res[$keys] == "renommer-base")
					{
						$ret = already_exist("ipbidon", strtolower($name[$keys]), "macbidon");
						if ($ret == "")
						{
							$content .= renomme_reservation($ip[$keys], $mac[$keys], strtolower($name[$keys]));
						}
						else
						{
							$content .= gettext("<p style='color:red;'>Attention : Le nom $name[$keys] n'est pas valide ou existe d&#233;j&#224;");
						}
					}
					elseif ($action_res[$keys] == "renommer")
					{
						$ret = already_exist("ipbidon", strtolower($name[$keys]), "macbidon");
						if ($ret == "")
						{
							exec("/usr/share/se3/sbin/tcpcheck 4 $ip[$keys]:445 | grep alive",$arrval,$return_value);
							if ($return_value == "1")
							{
								$content .= gettext("<p style='color:red;'>Attention : Renommage de $oldname[$keys] impossible. La machine est injoignable ou prot&#233;g&#233;e par un pare-feu  :  </p>\n " );
							}
							else
							{
								$content .= renomme_reservation($ip[$keys], $mac[$keys], strtolower($name[$keys]));
								$content .= renomme_domaine($ip[$keys], $oldname[$keys], strtolower($name[$keys]));
								$content .= renomme_machine_parcs(strtolower($oldname[$keys]), strtolower($name[$keys]));
								// $content .= "parti";
								$content .= system("/usr/bin/sudo /usr/share/se3/scripts/italc_generate.sh");
							} 
						}
						else
						{
							$content .= gettext("<p style='color:red;'>Attention : Le nom $name[$keys] n'est pas valide ou existe d&#233;j&#224;");
						}
					}
					elseif ($action_res[$keys] == "supprimer")
					{
						$content .= suppr_reservation($ip[$keys], $mac[$keys], strtolower($name[$keys]));
					}
					if (($parc[$keys] != "none") && ($parc[$keys] != ""))
					{
						$content .= add_machine_parc(strtolower($name[$keys]), $parc[$keys]);
					}
				}
			}
            dhcpd_restart();
            $content.=form_existing_reservation();
            break;

        default :
            // anti  hackingprot
            $title = '';
            $content = '';
            return;
    }
//$content .= search_doublons_mac();
// $content .= affiche_doublons_csv();

    print "$content\n";
    $filename="/tmp/emailunattended_generate";
    if (file_exists($filename))  {
        echo "<p style='color:red;'>Attention : doublons dans l'annuaire</p>\n";
        search_doublons_mac();
    }

    if(isset($suppr_doublons_ldap)) {
            $suppr=isset($_POST['suppr']) ? $_POST['suppr'] : NULL;

//             $tab_attr_recherche=array('cn');
//             for($i=0;$i<count($suppr);$i++) {
//                     if(get_tab_attribut("computers","cn=$suppr[$i]",$tab_attr_recherche)) {
//                             if(!del_entry("cn=$suppr[$i]","computers")) {
//                                     echo "Erreur lors de la suppression de l'entr&#233;e $suppr[$i]<br />\n";
//                             }
//                     }
// 
//                     // Faut-il aussi supprimer les uid=$suppr[$i]$ ? OUI
//                     if(get_tab_attribut("computers","uid=$suppr[$i]$",$tab_attr_recherche)) {
//                             if(!del_entry("uid=$suppr[$i]$","computers")) {
//                                     echo "Erreur lors de la suppression de l'entr&#233;e uid=$suppr[$i]$<br />\n";
//                             }
//                     }
//             }
    for($i=0;$i<count($suppr);$i++) {
		//echo "suppression_computer($suppr[$i])<br />";
		echo suppression_computer($suppr[$i]);
		//echo "<hr />";
	}
	echo "<hr />\n";
    }
 

// Footer
include ("pdp.inc.php");
?>