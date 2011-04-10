<?php

/**

 * Gestion des baux du DHCP
 * @Version $Id$

 * @Projet LCS / SambaEdu

 * @auteurs � GrosQuicK �  eric.mercier@crdp.ac-versailles.fr

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
require_once "fonc_parc.inc.php";
require_once "dhcpd.inc.php";
?>

<script type="text/javascript" src="/elements/js/wz_tooltip_new.js"></script>

<?php

$action = $_POST['action'];
if (is_admin("system_is_admin", $login) == "Y") {

    //aide
    $_SESSION["pageaide"] = "Le_module_DHCP#G.C3.A9rer_les_baux_et_r.C3.A9server_des_IPs";


    $content .= "<h1>" . gettext("R&#233;servations existantes") . "</h1>";

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
echo "<input  type=\"submit\" value=\"Supprimer toutes les réservations existantes\" onclick=\"if (window.confirm('supression de toutes les réservations ?')) {return true;} else {return false;}\"/>";
echo "</form>";*/

    // Prepare HTML code
    switch ($action) {
        case '' :
        case 'index' :
            $content.=form_existing_reservation();
            break;
	case 'cleanresa' :
	    $query="TRUNCATE se3_dhcp";
	    mysql_query($query);
	    dhcpd_restart();
            $content.=form_existing_reservation();
            break;

        case 'valid' :
            $ip = $_POST['ip'];
            $mac = $_POST['mac'];
            $localadminname = $_POST['localadminname'];
            $localadminpasswd = $_POST['localadminpasswd'];
            $oldname = $_POST['oldname'];
            $name = $_POST['name'];
            $parc = $_POST['parc'];
            $action_res = $_POST['action_res'];
            foreach ($ip as $keys => $value) {
                if ($action_res[$keys] == "integrer") {
                    if ($localadminpasswd[$keys] == "") {
                        $localadminpasswd[$keys] = "xxx";
                    }
                    $content .= "<FONT color='red'>" . integre_domaine($ip[$keys], $mac[$keys], strtolower($name[$keys]), $localadminname[$keys], $localadminpasswd[$keys]) . "</FONT>";
                } elseif ($action_res[$keys] == "actualiser") {
                    $content .= renomme_reservation($ip[$keys], $mac[$keys], strtolower($name[$keys]));
                } elseif ($action_res[$keys] == "newip") {
                    $content .= change_ip_reservation($ip[$keys], $mac[$keys], strtolower($name[$keys]));
                } elseif ($action_res[$keys] == "reintegrer") {
                    $content .= renomme_domaine($ip[$keys], $mac[$keys], strtolower($name[$keys]));
                } elseif ($action_res[$keys] == "renommer") {
                    $ret = already_exist("ipbidon", strtolower($name[$keys]), "macbidon");
                    if ($ret == "") {
                        $content .= renomme_reservation($ip[$keys], $mac[$keys], strtolower($name[$keys]));
                        $content .= renomme_domaine($ip[$keys], $oldname[$keys], strtolower($name[$keys]));
                        $content .= renomme_machine_parcs(strtolower($oldname[$keys]), strtolower($name[$keys]));
                    } else {
                        $ret = gettext("Le nom n'est pas valide ou existe d&#233;j&#224; : " . $name[$keys]);
                        echo $ret;
                    }
                } elseif ($action_res[$keys] == "supprimer") {
                    $content .= suppr_reservation($ip[$keys], $mac[$keys], strtolower($name[$keys]));
                }
                if (($parc[$keys] != "none") && ($parc[$keys] != "")) {
                    $content .= add_machine_parc(strtolower($name[$keys]), $parc[$keys]);
                }
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


} else {
    print (gettext("Vous n'avez pas les droits n&#233;cessaires pour ouvrir cette page..."));
}

// Footer
include ("pdp.inc.php");
?>
