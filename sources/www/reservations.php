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

$action = $_POST['action'];
if (is_admin("system_is_admin", $login) == "Y") {

    //aide
    $_SESSION["pageaide"] = "Le_module_DHCP#G.C3.A9rer_les_baux_et_r.C3.A9server_des_IPs";


    $content .= "<h1>" . gettext("R&#233;servations existantes") . "</h1>";
    // Prepare HTML code
    switch ($action) {
        case '' :
        case 'index' :
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
