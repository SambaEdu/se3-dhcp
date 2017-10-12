<?php

/**

 * Fonctions du serveur DHCP
 * @Version $Id$

 * @Projet LCS / SambaEdu

 * @auteurs GrosQuicK   eric.mercier@crdp.ac-versailles.fr
 * @auteurs Plouf

 * @note Ce fichier de fonction doit etre appele par un include

 */
 
/**

* Fonction de connexion/deconnexion a la database

**/

function connexion_db_dhcp()
{
	global $dbhost,$dbuser,$dbpass, $dbname;
	$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	mysqli_set_charset($link, "utf8");
	return $link;
}

function deconnexion_db_dhcp($link)
{
	mysqli_close($link);
}
 
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
	global $vlan_actif;
    // Recuperation des donnees dans la base SQL
	$dhcp_link=connexion_db_dhcp();
    $query = "SELECT * from params where name REGEXP '^dhcp*' ";
    $result = mysqli_query($dhcp_link,$query);
    $ret = "<table>\n";
    // Menu select pour les vlan
    $nbr_vlan = dhcp_vlan_test();
    if ($nbr_vlan > 0)
	{
        $i = 1;
        $ret .= "<form name=\"configuration\" method=\"post\" action=\"config.php\">\n";
        $ret .= "<tr><td>";
        $ret .= gettext("Vlan");
        $ret .= "</td><td>";
        $ret .= ": <select name=\"vlan\" onchange=submit()>";
        $ret .= "<option value=\"0\">D&#233;faut</option>";
        while ($i <= $nbr_vlan)
		{
            $ret .= "<option ";
            if ($vlan_actif == $i)
			{
                $ret .= "selected";
            }
            $ret .= " value=\"$i\">vlan $i</option>";
            $i++;
        }
        $ret .= "</td><td></td></tr>\n";
        $ret .= "</form>\n";
    }

    // formulaire
    $ret .= "<form name=\"configuration\" method=\"post\" action=\"config.php\">\n";
    while ($resultat = mysqli_fetch_assoc($result))
	{
        $dhcp[$resultat['name']]["value"] = $resultat['value'];
        $dhcp[$resultat['name']]["descr"] = $resultat['descr'];
    }
	mysqli_free_result($result);
	deconnexion_db_dhcp($dhcp_link);
    // dhcp_iface : interface d'ecoute du dhcp
    $ret .= "<tr><td>" . gettext($dhcp["dhcp_iface"]["descr"]) . "</td><td>\n";
    $ret .= ": <input type=\"text\" SIZE=5 name=\"dhcp_iface\" value=\"" . $dhcp["dhcp_iface"]["value"] . "\" maxlength=\"5\"";
    if ($vlan_actif > 0)
	{
        $ret .= " disabled ";
    }
    $ret .= ">";
	if (isset($error['dhcp_iface']))
	{
	    $ret .= "<b>" . $error['dhcp_iface'] . "</b>";
	}
    $ret .= "</td></tr>\n";

    // dhcp_domain_name : Nom du domaine
    $ret .= "<tr><td>" . gettext($dhcp["dhcp_domain_name"]["descr"]) . "</td><td>\n";
    $ret .= ": <input type=\"text\" SIZE=60 name=\"dhcp_domain_name\" value=\"" . $dhcp["dhcp_domain_name"]["value"] . "\" maxlength=\"55\"";
    if ($vlan_actif > 0)
	{
        $ret .= " disabled ";
    }
    $ret .= ">";
    if (isset($error['dhcp_domain_name']))
	{
            $ret .= "<b>" . $error['dhcp_domain_name'] . "</b>";
    }
    $ret .= "</td></tr>\n";

    // dhcp_in_boot : dhcp start oon boot ? 0 ou 1
    $ret .= "<tr><td>" . gettext($dhcp["dhcp_on_boot"]["descr"]) . "</td><td>\n";
    if ($dhcp["dhcp_on_boot"]["value"] == 1)
	{
        $CHECKED = "CHECKED";
    }
	else
	{
        $CHECKED = "";
    }
    $ret .= ": <input type=\"checkbox\" name=\"dhcp_on_boot\" $CHECKED ";
    if ($vlan_actif > 0)
	{
        $ret .= " disabled ";
    }
    $ret .= ">";
    if (isset($error['dhcp_iface']))
	{
		$ret .= "<b>" . $error['dhcp_on_boot'] . "</b>";
    }
    $ret .= "</td></tr>\n";

    // dhcp_max_lease : bail maximal
    $ret .= "<tr><td>" . gettext($dhcp["dhcp_max_lease"]["descr"]) . "</td><td>\n";
    $ret .= ": <input type=\"text\" SIZE=10 name=\"dhcp_max_lease\" value=\"" . $dhcp["dhcp_max_lease"]["value"] . "\" maxlength=\"10\"";
    if ($vlan_actif > 0)
	{
        $ret .= " disabled ";
    }
    $ret .= ">";
    if (isset($error['dhcp_max_lease']))
	{
		$ret .= "<b>" . $error['dhcp_max_lease'] . "</b>";
	}
    $ret .= "</td></tr>\n";


    // dhcp_default_lease : bail par defaut
    $ret .= "<tr><td>" . gettext($dhcp["dhcp_default_lease"]["descr"]) . "</td><td>\n";
    $ret .= ": <input type=\"text\" SIZE=10 name=\"dhcp_default_lease\" value=\"" . $dhcp["dhcp_default_lease"]["value"] . "\" maxlength=\"10\"";
    if ($vlan_actif > 0)
	{
        $ret .= " disabled ";
    }
    $ret .= ">";
    if (isset($error['dhcp_default_lease']))
	{
		$ret .= "<b>" . $error['dhcp_default_lease'] . "</b>";
    }
    $ret .= "</td></tr>\n";

    // dhcp_ntp : Serveur NTP
    $ret .= "<tr><td>" . gettext($dhcp["dhcp_ntp"]["descr"]) . "</td><td>\n";
    $ret .= ": <input type=\"text\" SIZE=20 name=\"dhcp_ntp\" value=\"" . $dhcp["dhcp_ntp"]["value"] . "\"  maxlength=\"20\"";
    if ($vlan_actif > 0)
	{
        $ret .= " disabled ";
    }
    $ret .= ">";
    if (isset($error['dhcp_ntp']))
	{
		$ret .= "<b>" . $error['dhcp_ntp'] . "</b>";
    }

    // dhcp_wins : Serveur WINS
    $ret .= "<tr><td>" . gettext($dhcp["dhcp_wins"]["descr"]) . "</td><td>\n";
    $ret .= ": <input type=\"text\" SIZE=20 name=\"dhcp_wins\" value=\"" . $dhcp["dhcp_wins"]["value"] . "\"maxlength=\"30\"";
    if ($vlan_actif > 0)
	{
        $ret .= " disabled ";
    }
    $ret .= ">";
    if (isset($error['dhcp_wins']))
	{
		$ret .= "<b>" . $error['dhcp_wins'] . "</b>";
    }
    $ret .= "</td></tr>\n";

    // dhcp_dns_server_prim : Serveur DNS primaire
    $ret .= "<tr><td>" . gettext($dhcp["dhcp_dns_server_prim"]["descr"]) . "</td><td>\n";
    $ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_dns_server_prim\" value=\"" . $dhcp["dhcp_dns_server_prim"]["value"] . "\"maxlength=\"15\"";
    if ($vlan_actif > 0)
	{
        $ret .= " disabled ";
    }
    $ret .= ">";
    if (isset($error['dhcp_dns_server_prim']))
	{
		$ret .= "<b>" . $error['dhcp_dns_server_prim'] . "</b>";
    }
    $ret .= "</td></tr>\n";

    // dhcp_dns_server_sec : Serveur DNS secondaire
    $ret .= "<tr><td>" . gettext($dhcp["dhcp_dns_server_sec"]["descr"]) . "</td><td>\n";
    $ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_dns_server_sec\" value=\"" . $dhcp["dhcp_dns_server_sec"]["value"] . "\" maxlength=\"15\"";
    if ($vlan_actif > 0)
	{
        $ret .= " disabled ";
    }
    $ret .= ">";
    if (isset($error['dhcp_dns_server_sec']))
	{
		$ret .= "<b>" . $error['dhcp_dns_server_sec'] . "</b>";
    }
    $ret .= "</td></tr>\n";

    // partie reserve si on a des vlan

    if ($vlan_actif > 0)
	{
        // Adresse du reseau
        $ret .= "<tr><td>" . gettext("Adresse de r&#233;seau ");
        $ret .= gettext(" du vlan ") . $vlan_actif;
        $ret .= "</td><td>\n";
        $dhcp_reseau_vlan = "dhcp_reseau_" . $vlan_actif;
        $ret .= ": <input type=\"text\" SIZE=15 name=\"$dhcp_reseau_vlan\" value=\"" . $dhcp["$dhcp_reseau_vlan"]["value"] . "\" maxlength=\"15\">";


        // Masque du reseau
        $ret .= "<tr><td>" . gettext("Masque de r&#233;seau ");
        $ret .= gettext(" du vlan ") . $vlan_actif;
        $ret .= "</td><td>\n";
        $dhcp_masque_vlan = "dhcp_masque_" . $vlan_actif;
        $ret .= ": <input type=\"text\" SIZE=15 name=\"$dhcp_masque_vlan\" value=\"" . $dhcp["$dhcp_masque_vlan"]["value"] . "\" maxlength=\"15\">";
    }
    if (isset($error['dhcp_gateway']))
	{
		$ret .= "<b>" . $error['dhcp_gateway'] . "</b>";
    }
    $ret .= "</td></tr>\n";


    // dhcp_gateway : PASSERELLE

    if ($vlan_actif > 0)
	{
        $ret .= "<tr><td>" . gettext($dhcp["dhcp_gateway"]["descr"]);
        $ret .= gettext(" du vlan ") . $vlan_actif;
        $ret .= "</td><td>\n";
        $dhcp_gateway_vlan = "dhcp_gateway_" . $vlan_actif;
        $ret .= ": <input type=\"text\" SIZE=15 name=\"$dhcp_gateway_vlan\" value=\"" . $dhcp["$dhcp_gateway_vlan"]["value"] . "\" maxlength=\"15\">";
        if (isset($error['dhcp_gateway']))
		{
                $ret .= "<b>" . $error['dhcp_gateway'] . "</b>";
        }
        $ret .= "</td></tr>\n";
    }
	else
	{
        if ($nbr_vlan == "0")
		{
            $ret .= "<tr><td>" . gettext($dhcp["dhcp_gateway"]["descr"]);
            $ret .= "</td><td>\n";
            $ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_gateway\" value=\"" . $dhcp["dhcp_gateway"]["value"] . "\" maxlength=\"15\">";
            if (isset($error['dhcp_gateway']))
			{
				$ret .= "<b>" . $error['dhcp_gateway'] . "</b>";
            }
            $ret .= "</td></tr>\n";
        }
    }

    // dhcp_ip_min : Debut de la plage de reservations
    if ($vlan_actif > 0)
	{
        $ret .= "<tr><td>" . gettext($dhcp["dhcp_ip_min"]["descr"]);
        $ret .= gettext(" du vlan ") . $vlan_actif;
        $ret .= "</td><td>\n";
        $dhcp_ip_min_vlan = "dhcp_ip_min_" . $vlan_actif;
//		if ($dhcp["$dhcp_ip_min_vlan"]["value"] == "") { $dhcp["$dhcp_ip_min_vlan"]["value"] = 
        $ret .= ": <input type=\"text\" SIZE=15 name=\"$dhcp_ip_min_vlan\" value=\"" . $dhcp["$dhcp_ip_min_vlan"]["value"] . "\" maxlength=\"15\">";
        if (isset($error['dhcp_ip_min']))
		{
            $ret .= "<b>" . $error['dhcp_ip_min'] . "</b>";
        }
        $ret .= "</td></tr>\n";
    }
	else
	{
        if ($nbr_vlan == "0")
		{
            $ret .= "<tr><td>" . gettext($dhcp["dhcp_ip_min"]["descr"]);
            $ret .= "</td><td>\n";
            $ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_ip_min\" value=\"" . $dhcp["dhcp_ip_min"]["value"] . "\" maxlength=\"15\">";
            if (isset($error['dhcp_ip_min']))
			{
				$ret .= "<b>" . $error['dhcp_ip_min'] . "</b>";
            }
            $ret .= "</td></tr>\n";
        }
    }

    // dhcp_begin_range : Debut de la plage
    if ($vlan_actif > 0)
	{
        $ret .= "<tr><td>" . gettext($dhcp["dhcp_begin_range"]["descr"]);
        $ret .= gettext(" du vlan ") . $vlan_actif;
        $ret .= "</td><td>\n";
        $dhcp_begin_range_vlan = "dhcp_begin_range_" . $vlan_actif;
        $ret .= ": <input type=\"text\" SIZE=15 name=\"$dhcp_begin_range_vlan\" value=\"" . $dhcp["$dhcp_begin_range_vlan"]["value"] . "\" maxlength=\"15\">";
        if (isset($error['dhcp_begin_range']))
		{
			$ret .= "<b>" . $error['dhcp_begin_range'] . "</b>";
        }
        $ret .= "</td></tr>\n";
    }
	else
	{
        if ($nbr_vlan == "0")
		{
            $ret .= "<tr><td>" . gettext($dhcp["dhcp_begin_range"]["descr"]);
            $ret .= "</td><td>\n";
            $ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_begin_range\" value=\"" . $dhcp["dhcp_begin_range"]["value"] . "\" maxlength=\"15\">";
            if (isset($error['dhcp_begin_range']))
			{
				$ret .= "<b>" . $error['dhcp_begin_range'] . "</b>";
            }
            $ret .= "</td></tr>\n";
        }
    }

    // dhcp_end_range : Fin de la plage
    if ($vlan_actif > 0)
	{
        $ret .= "<tr><td>" . gettext($dhcp["dhcp_end_range"]["descr"]);
        $ret .= gettext(" du vlan ") . $vlan_actif;
        $ret .= "</td><td>\n";
        $dhcp_end_range_vlan = "dhcp_end_range_" . $vlan_actif;
        $ret .= ": <input type=\"text\" SIZE=15 name=\"$dhcp_end_range_vlan\" value=\"" . $dhcp["$dhcp_end_range_vlan"]["value"] . "\" maxlength=\"15\"";
        if (isset($error['dhcp_end_range']))
		{
			$ret .= "<b>" . $error['dhcp_end_range'] . "</b>";
        }
        $ret .= "</td></tr>\n";
    }
	else
	{
        if ($nbr_vlan == "0")
		{
            $ret .= "<tr><td>" . gettext($dhcp["dhcp_end_range"]["descr"]);
            $ret .= "</td><td>\n";
            $ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_end_range\" value=\"" . $dhcp["dhcp_end_range"]["value"] . "\" maxlength=\"15\">";
            if (isset($error['dhcp_end_range']))
			{
				$ret .= "<b>" . $error['dhcp_end_range'] . "</b>";
            }
            $ret .= "</td></tr>\n";
        }
    }

    $ret .= "<tr><td></td><td></td></tr>\n";
    // Option autre
    if ($vlan_actif > 0)
	{
        $ret .= "<tr><td>" . gettext($dhcp["dhcp_extra_option"]["descr"]);
        $ret .= gettext(" du vlan ") . $vlan_actif;
        $ret .= "</td><td>\n";
        $dhcp_extra_option_vlan = "dhcp_extra_option_" . $vlan_actif;
        $ret .= ": <input type=\"text\" SIZE=30 name=\"$dhcp_extra_option_vlan\" value=\"" . $dhcp["$dhcp_extra_option_vlan"]["value"] . "\" maxlength=\"30\"";
        if (isset($error['dhcp_extra_option']))
		{
			$ret .= "<b>" . $error['dhcp_extra_option'] . "</b>";
        }
        $ret .= "</td></tr>\n";
    }
	else
	{
        if ($nbr_vlan == "0")
		{
            $ret .= "<tr><td>" . gettext($dhcp["dhcp_extra_option"]["descr"]);
            $ret .= "</td><td>\n";
            $ret .= ": <input type=\"text\" SIZE=30 name=\"dhcp_extra_option\" value=\"" . $dhcp["dhcp_extra_option"]["value"] . "\" maxlength=\"30\">";
            if (isset($error['dhcp_end_range']))
			{
				$ret .= "<b>" . $error['dhcp_end_range'] . "</b>";
            }
            $ret .= "</td></tr>\n";
        }
    }

    $ret .= "<tr><td></td><td></td></tr>\n";

    // dhcp_tftp_server : SERVER TFTP
    $ret .= "<tr><td>" . gettext($dhcp["dhcp_tftp_server"]["descr"]) . "</td><td>\n";
    $ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_tftp_server\" value=\"" . $dhcp["dhcp_tftp_server"]["value"] . "\" maxlength=\"15\"";
    if ($vlan_actif > 0)
	{
        $ret .= " disabled ";
    }
    $ret .= ">";
    if (isset($error['dhcp_tftp_server']))
	{
		$ret .= "<b>" . $error['dhcp_tftp_server'] . "</b>";
    }
    $ret .= "</td></tr>\n";

    // dhcp_unatt_filename fichier de boot PXE
    $ret .= "<tr><td>" . gettext($dhcp["dhcp_unatt_filename"]["descr"]) . "</td><td>\n";
    $ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_unatt_filename\" value=\"" . $dhcp["dhcp_unatt_filename"]["value"] . "\" ";
    if ($vlan_actif > 0)
	{
        $ret .= " disabled ";
    }
    $ret .= ">";
    if (isset($error['dhcp_unatt_filename']))
	{
		$ret .= "<b>" . $error['dhcp_unatt_filename'] . "</b>";
    }
    $ret .= "</td></tr>\n";

    $ret .= "<tr><td></td><td></td></tr>\n";

    // UNATTENDED
    // dhcp_unattended_server
    //$ret .= "<tr><td>".gettext($dhcp["dhcp_unatt_server"]["descr"])."</td><td>\n";
    //$ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_unatt_server\" value=\"".$dhcp["dhcp_unatt_server"]["value"]."\" maxlength=\"15\">";
    //$ret .= "<b>".$error['dhcp_unatt_server']."</b>";
    //$ret .= "</td></tr>\n";
    // dhcp_unatt_login
    $ret .= "<tr><td>" . gettext($dhcp["dhcp_unatt_login"]["descr"]) . "</td><td>\n";
    $ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_unatt_login\" value=\"" . $dhcp["dhcp_unatt_login"]["value"] . "\" ";
    if ($vlan_actif > 0)
	{
        $ret .= " disabled ";
    }
    $ret .= ">";
    if (isset($error['dhcp_unatt_login']))
	{
		$ret .= "<b>" . $error['dhcp_unatt_login'] . "</b>";
    }
    $ret .= "</td></tr>\n";

    // dhcp_unatt_pass
    $ret .= "<tr><td>" . gettext($dhcp["dhcp_unatt_pass"]["descr"]) . "</td><td>\n";
    $ret .= ": <input type=\"text\" SIZE=15 name=\"dhcp_unatt_pass\" value=\"" . $dhcp["dhcp_unatt_pass"]["value"] . "\" ";
    if ($vlan_actif > 0)
	{
        $ret .= " disabled ";
    }
    $ret .= ">";
    if (isset($error['dhcp_unatt_pass']))
	{
         $ret .= "<b>" . $error['dhcp_unatt_pass'] . "</b>";
    }
    $ret .= "</td></tr>\n";



    $ret .= "</table>";
    $ret .= "<input type='hidden' name='action' value='newconfig'>\n";
    $ret .= "<input type='hidden' name='vlan' value='" . $vlan_actif . "'>\n";
    $ret .= "<input type=\"submit\" name=\"button\" value=\"" . gettext("Modifier") . "\">\n";
    $ret .= "</form>\n";
    exec("sudo /usr/share/se3/scripts/makedhcpdconf state", $state);
    if ($state[0] == "1")
	{
        $ret .= "<form name=\"stop\" method=\"post\" action=\"config.php\">\n";
        $ret .= "<input type='hidden' name='action' value='stop'>\n";
        $ret .= "<input type=\"submit\" name=\"button\" value=\"" . gettext("Stopper le serveur dhcp") . "\">\n";
        $ret .="</form>";
    }
	else
	{
        $ret .= "<form name=\"stop\" method=\"post\" action=\"config.php\">\n";
        $ret .= "<input type='hidden' name='action' value='restart'>\n";
        $ret .= "<input type=\"submit\" name=\"button\" value=\"" . gettext("Red&#233;marrer le serveur dhcp") . "\">\n";
        $ret .="</form>";
    }
    return $ret;
}

/**
 * Mise a jour de la conf du dhcp dans la base SQL

 * @Parametres

 * @Return Erreur SQL

 */
function dhcp_update_config()
	{
	// insert range in option service table
    require_once ("ihm.inc.php");
    $error = "";
	
	Global $vlan_actif;

    if ($vlan_actif > 0)
	{
        //verif si le champ existe dans la table sinon on le cree

        $dhcp_min = "dhcp_ip_min_" . $vlan_actif;
        dhcp_vlan_champ($dhcp_min);
        $dhcp_begin = "dhcp_begin_range_" . $vlan_actif;
        dhcp_vlan_champ($dhcp_begin);
        $dhcp_end = "dhcp_end_range_" . $vlan_actif;
        dhcp_vlan_champ($dhcp_end);
        $dhcp_gateway_vlan = "dhcp_gateway_" . $vlan_actif;
        dhcp_vlan_champ($dhcp_gateway_vlan);
        $dhcp_reseau = "dhcp_reseau_" . $vlan_actif;
        dhcp_vlan_champ($dhcp_reseau);
        $dhcp_masque = "dhcp_masque_" . $vlan_actif;
        dhcp_vlan_champ($dhcp_masque);
        $dhcp_extra_option = "dhcp_extra_option_" . $vlan_actif;
        dhcp_vlan_champ($dhcp_extra_option);
    }
	else
	{
        $dhcp_min = "dhcp_ip_min";
        $dhcp_begin = "dhcp_begin_range";
        $dhcp_end = "dhcp_end_range";
        $dhcp_gateway_vlan = "dhcp_gateway";
        $dhcp_extra_option = "dhcp_extra_option";
    }
	
	$dhcp_link=connexion_db_dhcp();
	
    if ((set_ip_in_lan($_POST["$dhcp_min"])) || ($_POST["$dhcp_min"] == ""))
	{
        $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name=?");
		mysqli_stmt_bind_param($update_query,"ss", $_POST["$dhcp_min"], $dhcp_min);
		mysqli_stmt_execute($update_query);
		mysqli_stmt_close($update_query);
	}
	else
	{
        $error["$dhcp_min"] = gettext("Cette addresse n'est pas valide : " . $_POST["$dhcp_min"]);
    }

    if ((set_ip_in_lan($_POST["$dhcp_begin"])) || ($_POST["$dhcp_begin"] == ""))
	{
        $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name=?");
		mysqli_stmt_bind_param($update_query,"ss", $_POST["$dhcp_begin"], $dhcp_begin);
		mysqli_stmt_execute($update_query);
		mysqli_stmt_close($update_query);
    }
	else
	{
        $error["$dhcp_begin"] = gettext("Cette addresse n'est pas valide : " . $_POST["$dhcp_begin"]);
    }


    if ((set_ip_in_lan($_POST["$dhcp_end"]) || ($_POST["$dhcp_end"]) == ""))
	{
        $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name=?");
		mysqli_stmt_bind_param($update_query,"ss", $_POST["$dhcp_end"], $dhcp_end);
		mysqli_stmt_execute($update_query);
		mysqli_stmt_close($update_query);
    }
	else
	{
        $error["$dhcp_end"] = gettext("Cette adresse n'est pas valide : " . $_POST["$dhcp_end"]);
    }


    if ((set_ip_in_lan($_POST["$dhcp_gateway_vlan"])) || ($_POST["$dhcp_gateway_vlan"] == ""))
	{
        $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name=?");
		mysqli_stmt_bind_param($update_query,"ss", $_POST["$dhcp_gateway_vlan"], $dhcp_gateway_vlan);
		mysqli_stmt_execute($update_query);
		mysqli_stmt_close($update_query);
    }
	else
	{
        $error["$dhcp_gateway_vlan"] = gettext("Cette adresse n'est pas valide : " . $_POST["$dhcp_gateway_vlan"]);
    }
	
	if ($vlan_actif > 0)
	{		
		if ((set_ip_in_lan($_POST["$dhcp_reseau"])) || ($_POST["$dhcp_reseau"] == ""))
		{
			$update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name=?");
			mysqli_stmt_bind_param($update_query,"ss", $_POST["$dhcp_reseau"], $dhcp_reseau);
			mysqli_stmt_execute($update_query);
			mysqli_stmt_close($update_query);
		}
		else
		{
			$error["$dhcp_reseau"] = gettext("Cette addresse n'est pas valide : " . $_POST["$dhcp_reseau"]);
		}

		if ((set_ip_in_lan($_POST["$dhcp_masque"])) || ($_POST["$dhcp_masque"] == ""))
		{
			$update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name=?");
			mysqli_stmt_bind_param($update_query,"ss", $_POST["$dhcp_masque"], $dhcp_masque);
			mysqli_stmt_execute($update_query);
			mysqli_stmt_close($update_query);
		}
		else
		{
			$error["$dhcp_masque"] = gettext("Cette addresse n'est pas valide : " . $_POST["$dhcp_masque"]);
		}
	}

//	if (!($_POST["$dhcp_extra_option"]=="")) {
    $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name=?");
	mysqli_stmt_bind_param($update_query,"ss", $_POST["$dhcp_extra_option"], $dhcp_extra_option);
	mysqli_stmt_execute($update_query);
	mysqli_stmt_close($update_query);
//	}
    // Si on est dans la conf des vlan cette partie n'est pas modifiable

    if ($vlan_actif < 1)
	{
        if (set_ip_in_lan($_POST['dhcp_dns_server_prim']))
		{
            $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name='dhcp_dns_server_prim'");
			mysqli_stmt_bind_param($update_query,"s", $_POST["dhcp_dns_server_prim"]);
			mysqli_stmt_execute($update_query);
			mysqli_stmt_close($update_query);
        }
		else
		{
            $error["dhcp_dns_server_prim"] = gettext("Cette adresse n'est pas valide :" . $_POST['dhcp_dns_server_prim']);
        }

        if ((set_ip_in_lan($_POST['dhcp_dns_server_sec'])) || ($_POST['dhcp_dns_server_sec'] == ""))
		{
            $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name='dhcp_dns_server_sec'");
			mysqli_stmt_bind_param($update_query,"s", $_POST["dhcp_dns_server_sec"]);
			mysqli_stmt_execute($update_query);
			mysqli_stmt_close($update_query);
        }
		else
		{
            $error["dhcp_dns_server_sec"] = gettext("Cette adresse n'est pas valide : " . $_POST['dhcp_dns_server_sec']);
        }


        if (preg_match('/,/', $_POST['dhcp_wins']))
		{
            list($wins_ip_1, $wins_ip_2) = preg_split('/,/', $_POST['dhcp_wins']);
            if ((set_ip_in_lan($wins_ip_1)) && (set_ip_in_lan($wins_ip_2)))
			{
                $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name='dhcp_wins'");
				mysqli_stmt_bind_param($update_query,"s", $_POST["dhcp_wins"]);
				mysqli_stmt_execute($update_query);
				mysqli_stmt_close($update_query);
            }
			else
			{
                $error["dhcp_wins"] = gettext("Une des adresses n'est pas valide : " . $_POST['dhcp_wins']);
            }
        }
		elseif
		((set_ip_in_lan($_POST['dhcp_wins'])) || ($_POST['dhcp_wins'] == ""))
		{
			$update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name='dhcp_wins'");
			mysqli_stmt_bind_param($update_query,"s", $_POST["dhcp_wins"]);
			mysqli_stmt_execute($update_query);
			mysqli_stmt_close($update_query);
        }
		else
		{
            $error["dhcp_wins"] = gettext("Cette adresse n'est pas valide : " . $_POST['dhcp_wins']);
        }

        if ((set_ip_in_lan($_POST['dhcp_ntp'])) || ($_POST['dhcp_ntp'] == ""))
		{
            $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name='dhcp_ntp'");
			mysqli_stmt_bind_param($update_query,"s", $_POST["dhcp_ntp"]);
			mysqli_stmt_execute($update_query);
			mysqli_stmt_close($update_query);
        }
		else
		{
            $error["dhcp_ntp"] = gettext("Cette adresse n'est pas valide : " . $_POST['dhcp_ntp']);
        }
        if (preg_match("/^[0-9]+$/", $_POST['dhcp_max_lease']))
		{
            $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name='dhcp_max_lease'");
			mysqli_stmt_bind_param($update_query,"i", $_POST["dhcp_max_lease"]);
			mysqli_stmt_execute($update_query);
			mysqli_stmt_close($update_query);
        }
		else
		{
            $error["dhcp_max_lease"] = gettext("Ce n'est pas un nombre valide : " . $_POST['dhcp_max_lease']);
        }

        if (preg_match("/^[0-9]+$/", $_POST['dhcp_default_lease']))
		{
            $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name='dhcp_default_lease'");
			mysqli_stmt_bind_param($update_query,"i", $_POST["dhcp_default_lease"]);
			mysqli_stmt_execute($update_query);
			mysqli_stmt_close($update_query);
        }
		else
		{
            $error["dhcp_default_lease"] = gettext("Ce n'est pas un nombre valide : " . $_POST['dhcp_default_lease']);
        }
        if (preg_match("/^eth[0-9]+$/", $_POST['dhcp_iface']) || preg_match("/^bond[0-9]+$/", $_POST['dhcp_iface']))
		{
            $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name='dhcp_iface'");
			mysqli_stmt_bind_param($update_query,"s", $_POST["dhcp_iface"]);
			mysqli_stmt_execute($update_query);
			mysqli_stmt_close($update_query);
        }
		else
		{
            $error["dhcp_iface"] = gettext("Ce n'est pas une interface valide : " . $_POST['dhcp_iface']);
        }
        if (@$_POST['dhcp_on_boot'])
		{
            $value = "1";
        }
		else
		{
            $value = "0";
        }
        $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name='dhcp_on_boot'");
		mysqli_stmt_bind_param($update_query,"i", $value);
		mysqli_stmt_execute($update_query);
		mysqli_stmt_close($update_query);

        $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name='dhcp_domain_name'");
		mysqli_stmt_bind_param($update_query,"s", $_POST["dhcp_domain_name"]);
		mysqli_stmt_execute($update_query);
		mysqli_stmt_close($update_query);

        // TFTP SERVER
        if ((set_ip_in_lan($_POST['dhcp_tftp_server'])) || ($_POST['dhcp_tftp_server'] == ""))
		{
            $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name='dhcp_tftp_server'");
			mysqli_stmt_bind_param($update_query,"s", $_POST["dhcp_tftp_server"]);
			mysqli_stmt_execute($update_query);
			mysqli_stmt_close($update_query);
        }
		else
		{
            $error["dhcp_tftp_server"] = gettext("Cette entr&#233;e n'est pas valide :") . $_POST['dhcp_tftp_server'];
        }

        $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name='dhcp_unatt_login'");
		mysqli_stmt_bind_param($update_query,"s", $_POST["dhcp_unatt_login"]);
		mysqli_stmt_execute($update_query);
		mysqli_stmt_close($update_query);
		
        $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name='dhcp_unatt_pass'");
		mysqli_stmt_bind_param($update_query,"s", $_POST["dhcp_unatt_pass"]);
		mysqli_stmt_execute($update_query);
		mysqli_stmt_close($update_query);
		
        $update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name='dhcp_unatt_filename'");
		mysqli_stmt_bind_param($update_query,"s", $_POST["dhcp_unatt_filename"]);
		mysqli_stmt_execute($update_query);
		mysqli_stmt_close($update_query);
    }
	deconnexion_db_dhcp($dhcp_link);
    return $error;
}

/**
 *  Test si l'adresse IP appartient au reseau local

 * @Parametres $ip : Adresse IP a tester

 * @Return  TRUE si oui - FLASE si non

 */
function set_ip_in_lan($ip)
{
    if (preg_match("/^(((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]{1}[0-9]|[1-9]).)" .
                    "{1}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]).)" .
                    "{2}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]){1}))$/", $ip))
	{
        return TRUE;
    }
	else
	{
        return FALSE;
    }
}

/**
 * Parse le fichier dhcp.leases

 * @Parametres $file : fichier dhcp.laeses

 * @Return an associativ array : ["hostname"] / ("ip"] / [ "macaddr"] who are in dhcpd.lease and take ["parc"] entry if exist in ldap SORT by hostname

 */
function parse_dhcpd_lease($file)
{
    require_once("includes/ldap.inc.php");
    $lease = file($file);
    $compteur_clients = 0;
    $client["macaddr"][$compteur_clients] = "";
    $client["hostname"][$compteur_clients] = "";
// $client["ip"][$compteur_clients]=$ip[0];
    foreach ($lease as $compteur => $ligne)
	{
        if (preg_match("/^lease/", $ligne))
		{  // for each "lease" we take IP / Mac Addr / hostname
            preg_match("/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/", $ligne, $ip);   // take IP
            $macaddr[0] = gettext("unresolved");
            $clienthostname[0] = gettext("unresolved");
            $etat = 0;
            while (!preg_match("/^}/", $lease[$compteur]))
			{
                if (preg_match("/binding state active/", $lease[$compteur]))
                    $etat = 1; // lease state
				if (preg_match("/hardware ethernet/", $lease[$compteur]))
				{ // take mac addr
                    preg_match("/[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}/", $lease[$compteur], $macaddr);
                }
                if (preg_match("/client-hostname/", $lease[$compteur]))
				{ // take name
                    preg_match("/\"(.*)\"/", $lease[$compteur], $clienthostname);
                    $clienthostname[0] = preg_replace("/\"/", "", $clienthostname[0]);
                }
                $compteur = $compteur + 1;
            }
            if ($etat && ((!in_array($macaddr[0], $client["macaddr"])) && ($macaddr[0] != gettext("unresolved") ) && (!registred($macaddr[0]))))
			{
                $client["macaddr"][$compteur_clients] = $macaddr[0];
                $client["hostname"][$compteur_clients] = $clienthostname[0];

                if ($client["hostname"][$compteur_clients] == gettext("unresolved"))
				{
                    $list_computer = search_machines("(&(cn=*)(ipHostNumber=$ip[0]))", "computers");
                    if (count($list_computer) > 1)
					{
                        $resolutiondunom = "doublon_ldap";
                        $client["hostname"][$compteur_clients] = $resolutiondunom;
                    }
					elseif (count($list_computer) > 0)
					{
                        $resolutiondunom = $list_computer[0]['cn'];
                        $client["hostname"][$compteur_clients] = $resolutiondunom;
                    }
                }
                $client["ip"][$compteur_clients] = $ip[0];
                $client["parc"][$compteur_clients] = search_parcs($clienthostname[0]);
                $compteur_clients++;
            }
        }
    }
    if (is_array($client["ip"]))
	{
        array_multisort($client["hostname"], SORT_ASC, $client["ip"], SORT_ASC, $client["macaddr"], SORT_ASC, $client["parc"]);
    }
	else
	{
        $client = "";
    }
    return $client;
}

function my_parse_dhcpd_lease($file)
{
    $mode_debug = "n";
    $mode_fich_debug = "n";

    require_once("includes/ldap.inc.php");
    $lease = file($file);
    $compteur_clients = 0;

    /*
      $client["macaddr"][$compteur_clients]="";
      $client["hostname"][$compteur_clients]="";
      // $client["ip"][$compteur_clients]=$ip[0];
     */

    $client["macaddr"] = array();
    $client["ip"] = array();
    $client["hostname"] = array();
    $client["parc"] = array();

    $tab_recherche_ldap_faite = array();
    $liste_noms_en_lease = array();
    $liste_noms_ldap = array();
    $liste_autres_ip = array();

    if ($mode_fich_debug == "y")
	{
        $fich = fopen('/tmp/parse_dhcpd_lease.txt', 'a+');
    }

    foreach ($lease as $compteur => $ligne)
	{
        // for each "lease" we take IP / Mac Addr / hostname
        if (preg_match("/^lease/", $ligne))
		{
            preg_match("/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/", $ligne, $ip);   // take IP
            // Initialisation pour le cas ou les infos dans cette section du dhcpd.leases ne sont pas exmploitables
            $macaddr[0] = gettext("unresolved");
            $clienthostname[0] = gettext("unresolved");
            $etat = 0;

            // On lit le fichier jusqu'a l'accolade fermante suivante
            while (!preg_match("/^}/", $lease[$compteur]))
			{
                if (preg_match("/binding state active/", $lease[$compteur]))
				{
                    $etat = 1;
                } // lease state

                if (preg_match("/hardware ethernet/", $lease[$compteur]))
				{ // take mac addr
                    preg_match("/[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}/", $lease[$compteur], $macaddr);
                }

                if (preg_match("/client-hostname/", $lease[$compteur]))
				{ // take name
                    preg_match("/\"(.*)\"/", $lease[$compteur], $clienthostname);
                    $clienthostname[0] = preg_replace("/\"/", "", $clienthostname[0]);
                }

                $compteur = $compteur + 1;
            }

            if ($mode_fich_debug == "y")
			{
                fwrite($fich, "\n");
                fwrite($fich, "\$ip[0]=" . $ip[0] . "\n");
                fwrite($fich, "\$macaddr[0]=" . $macaddr[0] . "\n");
                fwrite($fich, "\$clienthostname[0]=" . $clienthostname[0] . "\n");
            }

            if ($etat == "1")
			{
                // On a bien 'binding state active' pour cette IP... malheureusement on peut avoir deux ip avec ca pour une meme adresse MAC

                if ((!registred($macaddr[0])) &&
                        ($macaddr[0] != gettext("unresolved")))
				{
                    // Adresse MAC trouvee dans le leases et pas deja enregistree dans la table se3_dhcp

                    if (!isset($liste_noms_en_lease["$macaddr[0]"]))
					{
                        $liste_noms_en_lease["$macaddr[0]"] = array();
                    }
                    if (($mode_debug == 'y') ||
                            ((strtolower($clienthostname[0]) != 'unresolved') &&
                            (!in_array(strtolower(@$list_computer[$loop]['cn']), $liste_noms_en_lease["$macaddr[0]"]))))
					{
                        $liste_noms_en_lease["$macaddr[0]"][] = strtolower($clienthostname[0]);

                        if ($mode_fich_debug == "y")
						{
                            fwrite($fich, "\$liste_noms_en_lease[\"$macaddr[0]\"][]=" . strtolower($clienthostname[0]) . "\n");
                        }
                    }

                    if ((!in_array($macaddr[0], $client["macaddr"])))
					{
                        if ($mode_fich_debug == "y")
						{
                            fwrite($fich, "Adresse Mac non encore traitee.\n");
                        }

                        $client["macaddr"][$compteur_clients] = $macaddr[0];
                        $client["hostname"][$compteur_clients] = $clienthostname[0];

                        $liste_noms_ldap["$macaddr[0]"] = array();

                        if ($mode_fich_debug == "y")
						{
                            fwrite($fich, "\$client[\"hostname\"][$compteur_clients]=" . $client["hostname"][$compteur_clients] . "\n");
                        }

                        if ($client["hostname"][$compteur_clients] == gettext("unresolved"))
						{
                            // Le nom n'a pas ete trouve dans le dhcpd.leases pour cette section

                            if ($mode_fich_debug == "y")
							{
                                fwrite($fich, "\$client[\"hostname\"][$compteur_clients] est  unresolved.\n");
                            }

                            //$list_computer=search_machines("(&(cn=*)(ipHostNumber=$ip[0]))","computers");
                            $list_computer = search_machines("(&(cn=*)(macAddress=$macaddr[0]))", "computers");
                            if (count($list_computer) > 1)
							{
                                $resolutiondunom = "doublon_ldap";
                                $client["hostname"][$compteur_clients] = $resolutiondunom;

                                if ($mode_fich_debug == "y")
								{
                                    fwrite($fich, "\$client[\"hostname\"][$compteur_clients]=" . $resolutiondunom . "\n");
                                }
                            }
							elseif (count($list_computer) > 0)
							{
                                $resolutiondunom = $list_computer[0]['cn'];
                                $client["hostname"][$compteur_clients] = $resolutiondunom;

                                if ($mode_fich_debug == "y")
								{
                                    fwrite($fich, "\$client[\"hostname\"][$compteur_clients]=" . $resolutiondunom . "\n");
                                }
                            }
							elseif ($mode_fich_debug == "y")
							{
                                fwrite($fich, "Adresse Mac non trouvee dans le LDAP.\n");
                            }

                            $tab_recherche_ldap_faite[] = $macaddr[0];

                            for ($loop = 0; $loop < count($list_computer); $loop++)
							{
                                //echo " ".$list_computer[$loop]['cn'];
                                if (($mode_debug == 'y') ||
                                        (!in_array(strtolower($list_computer[$loop]['cn']), $liste_noms_ldap["$macaddr[0]"])))
								{
                                    $liste_noms_ldap["$macaddr[0]"][] = strtolower($list_computer[$loop]['cn']);

                                    if ($mode_fich_debug == "y")
									{
                                        fwrite($fich, "\$liste_noms_ldap[\"$macaddr[0]\"][]=" . strtolower($list_computer[$loop]['cn']) . "\n");
                                    }
                                }
                            }
                        }
						else
						{
                            // Il y a un hostname dans le lease
                            if (!in_array($macaddr[0], $tab_recherche_ldap_faite))
							{
                                // On controle quand meme si il y a d'autres noms dans le LDAP (pour affichage)
                                $list_computer = search_machines("(&(cn=*)(macAddress=$macaddr[0]))", "computers");

                                if (count($list_computer) > 0)
								{
                                    for ($loop = 0; $loop < count($list_computer); $loop++)
									{
                                        //echo " ".$list_computer[$loop]['cn'];
                                        if (($mode_debug == 'y') ||
                                                (!in_array(strtolower($list_computer[$loop]['cn']), $liste_noms_ldap["$macaddr[0]"])))
										{
                                            $liste_noms_ldap["$macaddr[0]"][] = strtolower($list_computer[$loop]['cn']);

                                            if ($mode_fich_debug == "y")
											{
                                                fwrite($fich, "\$liste_noms_ldap[\"$macaddr[0]\"][]=" . strtolower($list_computer[$loop]['cn']) . "\n");
                                            }
                                        }
                                    }
                                }
								elseif ($mode_fich_debug == "y")
								{
                                    fwrite($fich, "Adresse Mac non trouvee dans le LDAP.\n");
                                }
                            }
                        }
                        $client["ip"][$compteur_clients] = $ip[0];
                        $client["parc"][$compteur_clients] = search_parcs($clienthostname[0]);
                        $compteur_clients++;
                    }
					else
					{
                        // On controle quand meme si il y a d'autres noms dans le LDAP (pour affichage)
                        if (!in_array($macaddr[0], $tab_recherche_ldap_faite))
						{
                            $list_computer = search_machines("(&(cn=*)(macAddress=$macaddr[0]))", "computers");

                            if (count($list_computer) > 0)
							{
                                for ($loop = 0; $loop < count($list_computer); $loop++)
								{
                                    //echo " ".$list_computer[$loop]['cn'];
                                    if (($mode_debug == 'y') ||
                                            (!in_array(strtolower($list_computer[$loop]['cn']), $liste_noms_ldap["$macaddr[0]"])))
									{
                                        $liste_noms_ldap["$macaddr[0]"][] = strtolower($list_computer[$loop]['cn']);

                                        if ($mode_fich_debug == "y")
										{
                                            fwrite($fich, "\$liste_noms_ldap[\"$macaddr[0]\"][]=" . strtolower($list_computer[$loop]['cn']) . "\n");
                                        }
                                    }
                                }
                            }
							elseif ($mode_fich_debug == "y")
							{
                                fwrite($fich, "Adresse Mac non trouvee dans le LDAP.\n");
                            }
                        }

                        if (($mode_debug == 'y') ||
                                ((strtolower($clienthostname[0]) != 'unresolved') &&
                                (!in_array(strtolower($clienthostname[0]), $liste_noms_en_lease["$macaddr[0]"]))))
						{
                            $liste_noms_en_lease["$macaddr[0]"][] = strtolower($clienthostname[0]);

                            if ($mode_fich_debug == "y")
							{
                                fwrite($fich, "\$liste_noms_en_lease[\"$macaddr[0]\"][]=" . strtolower($clienthostname[0]) . "\n");
                            }
                        }

                        // Et on met a jour l'IP en supposant que la derniere IP recue est la bonne
                        $tmp_tab = array();
                        $tmp_tab = $client["macaddr"];
                        $tmp_tab = array_flip($tmp_tab);
                        $indice = $tmp_tab[$macaddr[0]];

                        $liste_autres_ip[$macaddr[0]][] = $client['ip'][$indice];
                        $client['ip'][$indice] = $ip[0];
                    }
                }
            }
        }
    }

    if (is_array($client["ip"]))
	{
        array_multisort($client["hostname"], SORT_ASC, $client["ip"], SORT_ASC, $client["macaddr"], SORT_ASC, $client["parc"]);

        $client['liste_noms_en_lease'] = $liste_noms_en_lease;
        $client['liste_noms_ldap'] = $liste_noms_ldap;
        $client['liste_autres_ip'] = $liste_autres_ip;
    }
	else
	{
        $client = "";
    }

    if ($mode_fich_debug == "y")
	{
        fwrite($fich, "=============================================\n");
        fclose($fich);
    }

    return $client;
}

function my_dhcp_form_lease($parser)
{
    $mode_debug = "n"; // Si on passe cette variable a 'y', passer la meme variable a 'y' dans my_parse_dhcpd_lease()

    require_once("includes/ldap.inc.php");

    @$content .= "<script type='text/javascript'>
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
    $content .= "<table border=\"1\" width=\"90%\">\n";
    $header = "<tr class=\"menuheader\">\n";
    $header .="<td align=\"center\"><b>\n" . gettext("Adresse IP");
    $header .= "</b></td>\n";
    $header .="<td align=\"center\"><b>\n" . gettext("Adresse MAC");
    $header .="</b></td>\n";
    $header .="<td align=\"center\"><b>\n" . gettext("Nom NETBIOS");
    $header .="</b></td>\n";
    $header .="<td align=\"center\"><b>\n" . gettext("Parc(s)");
    $header .="</b></td>\n";
    $header .="<td align=\"center\">\n";
    $header .= "<input type='hidden' name='action' value='valid' />\n";
    $header .= "<input type=\"submit\" name=\"button\" value=\"" . gettext("Valider les actions") . "\" />\n";
    $header .= "</td>\n";
//    $content .= "<a href='javascript: checkAll_baux();'><img src='../elements/images/enabled.gif' width='20' height='20' border='0' alt='Tout cocher' /></a> / <a href='javascript:UncheckAll_baux();'><img src='../elements/images/disabled.gif' width='20' height='20' border='0' alt='Tout d&#233;cocher' /></a>\n";
    $header .= "</td>\n";
    $header .="<td align=\"center\"><b>\n" . gettext("Identifiants admin local");

    $header .="</td></tr>\n";
    $content .= $header;
    $nbligne = 0;

    foreach ($parser["ip"] as $keys => $value)
	{
        if (!is_recorded_in_dhcp_database($parser["ip"][$keys], $parser["macaddr"][$keys], $parser["hostname"][$keys]))
		{
            $content .= "<tr>\n";

            $content .="<td>\n";
            $content .= "<input type=\"text\" maxlength=\"15\" size=\"15\" value=\"" . $parser["ip"][$keys] . "\"  name=\"ip[$keys]\" />\n";
            if (($mode_debug == 'y') && (count($parser['liste_autres_ip'][$parser["macaddr"][$keys]]) > 0))
			{
                for ($loop = 0; $loop < count($parser['liste_autres_ip'][$parser["macaddr"][$keys]]); $loop++)
				{
                    $content .="<br />\n" . $parser['liste_autres_ip'][$parser["macaddr"][$keys]][$loop];
                }
            }

            $content .= "</td>\n";

            $content .="<td align='center'>\n";
            $content .= "<input type=\"hidden\" maxlength=\"17\" size=\"17\" value=\"" . $parser["macaddr"][$keys] . "\"  name=\"mac[$keys]\" />\n";
            ;
            $content .= $parser["macaddr"][$keys];
            $content .= "</td>\n";

            $content .="<td align='center'>\n";
            $content .= "<input type=\"text\" maxlength=\"20\" size=\"20\" value=\"" . $parser["hostname"][$keys] . "\"  name=\"name[$keys]\" />\n";
			//$content .= "" . $parser["hostname"][$keys] . "\n";
			//$content .= "<input type=\"hidden\" maxlength=\"20\" size=\"20\" value=\"" . $parser["hostname"][$keys] . "\"  name=\"name[$keys]\" />\n";
            $content .= "<input type=\"hidden\" maxlength=\"20\" SIZE=\"20\" value=\"" . $parser["hostname"][$keys] . "\"  name=\"oldname[$keys]\">\n";

            if ((count($parser['liste_noms_en_lease'][$parser["macaddr"][$keys]]) > 1) ||
                    (count($parser['liste_noms_ldap'][$parser["macaddr"][$keys]]) > 1) ||
                    ((isset($parser['liste_noms_en_lease'][$parser["macaddr"][$keys]][0])) && (strtolower($parser['liste_noms_en_lease'][$parser["macaddr"][$keys]][0]) != strtolower($parser["hostname"][$keys]))) ||
                    ((isset($parser['liste_noms_ldap'][$parser["macaddr"][$keys]][0])) && (strtolower($parser['liste_noms_ldap'][$parser["macaddr"][$keys]][0]) != strtolower($parser["hostname"][$keys])))
            )
			{
                $content .="<br />\n";
                $content .="<table border='0'>\n";

                if (($mode_debug == 'y') ||
                        (count($parser['liste_noms_en_lease'][$parser["macaddr"][$keys]]) > 1) ||
                        ((isset($parser['liste_noms_en_lease'][$parser["macaddr"][$keys]][0])) && (strtolower($parser['liste_noms_en_lease'][$parser["macaddr"][$keys]][0]) != strtolower($parser["hostname"][$keys]))))
				{
                    $content .="<tr>\n";
                    $content .="<th valign='top'>Leases:</th>\n";
                    $content .="<td>\n";
                    for ($loop = 0; $loop < count($parser['liste_noms_en_lease'][$parser["macaddr"][$keys]]); $loop++)
					{
                        $content .=$parser['liste_noms_en_lease'][$parser["macaddr"][$keys]][$loop] . "<br />\n";
                    }
                    $content .="</td>\n";
                    $content .="</tr>\n";
                }

                if (($mode_debug == 'y') ||
                        (count($parser['liste_noms_ldap'][$parser["macaddr"][$keys]]) > 1) ||
                        ((isset($parser['liste_noms_ldap'][$parser["macaddr"][$keys]][0])) && (strtolower($parser['liste_noms_ldap'][$parser["macaddr"][$keys]][0]) != strtolower($parser["hostname"][$keys]))))
				{
                    $content .="<tr>\n";
                    $content .="<th valign='top'>Ldap:</th>\n";
                    $content .="<td>\n";
                    for ($loop = 0; $loop < count($parser['liste_noms_ldap'][$parser["macaddr"][$keys]]); $loop++)
					{
                        $content .=$parser['liste_noms_ldap'][$parser["macaddr"][$keys]][$loop] . "<br />\n";
                    }
                    $content .="</td>\n";
                    $content .="</tr>\n";
                }

                $content .="</table>\n";
            }

            $content .="</td>\n";

            $content .="<td align=\"left\">\n";
            $showid = 0;
            $listaction = "";
            // Est-ce que cette machine est integree ?
            if (count(search_machines("(cn=" . $parser["hostname"][$keys] . ")", "computers")) > 0)
			{
                if (isset($parser["parc"][$keys]))
				{
                    foreach ($parser["parc"][$keys] as $keys2 => $value2)
					{
                        $content.="<a href=../parcs/show_parc.php?parc=" . $parser["parc"][$keys][$keys2]["cn"] . ">" . $parser["parc"][$keys][$keys2]["cn"] . "</a><br>\n";
                    }
                }
                // ajouter a un parc dans lequel la machine n'est  pas ?
                $content .= add_to_parc($parser["parc"][$keys], $keys);
                // est ce que la machine est integree au domaine ?
                if (count(search_machines("(uid=" . $parser["hostname"][$keys] . "$)", "computers")) > 0)
				{
//                    $listaction .="<OPTION value=\"renommer\">Renommer\</OPTION>n"; 
                }
				else
				{ // this computer is not recorded on the domain
                    $content.="<br><FONT color='red'>" . gettext("Pas au domaine!") . "</FONT>\n";
//                    $listaction .="<OPTION value=\"integrer\">Integrer</OPTION>\n";
//                    $showid = 1;
                }
            }
			else
			{ // this computer is not registered in ldap
                $content.="<FONT color='red'>" . gettext("Non enregistre") . "</FONT>\n";
//                $listaction .="<OPTION value=\"integrer\">Integrer</OPTION>\n";
//                $showid = 1; 
            }
            //
            $content .="</td><td align=\"center\">\n";
            $content .="<SELECT  name=\"action_res[$keys]\">";
            $content .="<OPTION value=\"none\">" . gettext("Action...") . "</OPTION>";
            $content .= $listaction;
            $content .= "<OPTION value=\"reserver\">Reserver</OPTION>\n";
            $content .= "<OPTION value=\"reserver2\">Reserver hors plage de reservation</OPTION>\n";
            $content .="</SELECT>\n";
            $content .="</td>\n";
            $content .="<td align=\"center\">\n";
            if ($showid)
			{
                $content .= "Admin local : <input type=\"text\" maxlength=\"20\" SIZE=\"15\" value=\"administrateur\"  name=\"localadminname[$keys]\" ><br>\n";
                $content .= "Mot de passe : <input type=\"text\" maxlength=\"20\" SIZE=\"8\" value=\"\"  name=\"localadminpasswd[$keys]\" ><br>\n";
            }
            $content .="</td></tr>\n";
            if ($nbligne++ == 10) {
                $content .=$header;
                $nbligne = 0;
            }
        }
    }
    $content .= "</table>\n";

    $content .= "<input type='hidden' name='action' value='valid' />\n";
    //$content .= "<input type=\"submit\" name=\"button\" value=\"".gettext("valider")."\">\n";
    $content .= "<input type=\"submit\" name=\"button\" value=\"" . gettext("Valider les r&#233;servations") . "\" />\n";
    $content .= "</form>\n";
    return $content;
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
    $content .= "<table border=\"1\" width=\"90%\">\n";
    $content .="<tr class=\"menuheader\"><td align=\"center\"><b>\n" . gettext("Adresse IP");
    $content .= "</b></td><td align=\"center\"><b>\n" . gettext("Adresse MAC");
    $content .="</b></td><td align=\"center\"><b>\n" . gettext("Nom NETBIOS");
    $content .="</b></td><td align=\"center\"><b>\n" . gettext("Parc(s)");
    $content .="</b></td><td align=\"center\"><b>\n" . gettext("Action");
    $content .= "</b></td></tr>\n";
    $content .= "</b><br />\n";
    $content .= "<a href='javascript: checkAll_baux();'><img src='../elements/images/enabled.gif' width='20' height='20' border='0' alt='Tout cocher' /></a> / <a href='javascript:UncheckAll_baux();'><img src='../elements/images/disabled.gif' width='20' height='20' border='0' alt='Tout d&#233;cocher' /></a>\n";
    $content .= "</td></tr>\n";
    foreach ($parser["ip"] as $keys => $value)
	{
        if (!is_recorded_in_dhcp_database($parser["ip"][$keys], $parser["macaddr"][$keys], $parser["hostname"][$keys]))
		{
            $content .= "<tr><td>\n";
            $content .= "<input type=\"text\" maxlength=\"15\" SIZE=\"15\" value=\"" . $parser["ip"][$keys] . "\"  name=\"ip[$keys]\" >\n";
            $content .= "</td><td>\n";
            $content .= "<input type=\"text\" maxlength=\"17\" SIZE=\"17\" value=\"" . $parser["macaddr"][$keys] . "\"  name=\"mac[$keys]\" >\n";
            $content .= "</td><td>\n";
            $content .= "<input type=\"text\" maxlength=\"20\" SIZE=\"20\" value=\"" . $parser["hostname"][$keys] . "\"  name=\"name[$keys]\" >\n";
            $content .= "<input type=\"hidden\" maxlength=\"20\" SIZE=\"20\" value=\"" . $parser["hostname"][$keys] . "\"  name=\"oldname[$keys]\">\n";
            $content .="</td><td align=\"left\">\n";
            $showid = 0;
            $listaction = "";
            // Est-ce que cette machine est integree ?
            if (count(search_machines("(cn=" . $parser["hostname"][$keys] . ")", "computers")) > 0)
			{
                if (isset($parser["parc"][$keys]))
				{
                    foreach ($parser["parc"][$keys] as $keys2 => $value2)
					{
                        $content.="<a href=../parcs/show_parc.php?parc=" . $parser["parc"][$keys][$keys2]["cn"] . ">" . $parser["parc"][$keys][$keys2]["cn"] . "</a><br>\n";
                    }
                }
                // ajouter a un parc dans lequel la machine n'est  pas ?
                $content .= add_to_parc($parser["parc"][$keys], $keys);
                // est ce que la machine est integree au domaine ?
                if (count(search_machines("(uid=" . $parser["hostname"][$keys] . "$)", "computers")) > 0)
				{
                    $listaction .="<OPTION value=\"renommer\">Renommer</OPTION>\n";
                }
				else
				{ // this computer is not recorded on the domain
                    $content.="<FONT color='red'>" . gettext("Pas au domaine!") . "</FONT>\n";
                    $listaction .="<OPTION value=\"integrer\">Integrer</OPTION>\n";
                    $showid = 1;
                }
            }
			else
			{ // this computer is not registered in ldap
                $content.="<FONT color='red'>" . gettext("Non enregistre") . "</FONT>\n";
                $listaction .="<OPTION value=\"integre\">Integrer</OPTION>\n";
                $showid = 1;
            }
            //
            $content .="</td><td align=\"center\">\n";
            $content .="<SELECT  name=\"action_res[$keys]\">";
            $content .="<OPTION value=\"none\">" . gettext("Action...") . "</OPTION>";
            $content .= $listaction;
            $content .= "<OPTION value=\"reserver\">Reserver</OPTION>\n";
            $content .= "<OPTION value=\"reserver2\">Reserver hors plage de reservation</OPTION>\n";
            $content .="</SELECT>\n";
            $content .="</td>\n";
            $content .="<td align=\"center\">\n";
            if ($showid)
			{
                $content .= "Admin local : <input type=\"text\" maxlength=\"20\" SIZE=\"15\" value=\"administrateur\"  name=\"localadminname[$keys]\" ><br>\n";
                $content .= "Mot de passe : <input type=\"text\" maxlength=\"20\" SIZE=\"8\" value=\"\"  name=\"localadminpasswd[$keys]\" ><br>\n";
            }
            $content .="</td></tr>\n";
        }
    }
    $content .= "</table>\n";
    $content .= "<input type='hidden' name='action' value='valid'>\n";
    //$content .= "<input type=\"submit\" name=\"button\" value=\"".gettext("valider")."\">\n";
    $content .= "<input type=\"submit\" name=\"button\" value=\"" . gettext("Valider les r&#233;servations") . "\">\n";
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
    require_once ("printers.inc.php");
	
	// Nombre par page
	$nb_par_page = 100;
	
	// Recuperation du nombre total d'enregistrement
	$dhcp_link=connexion_db_dhcp();
	$nb_total = 0;
	$query2 = "SELECT count(*) as NB FROM `se3_dhcp`";
	$result2 = mysqli_query($dhcp_link,$query2);
	mysqli_data_seek($result2,0);
	$result2=mysqli_fetch_row($result2);
	$nb_total = $result2[0]+0;
	
	// Nombre total de pages
	$nb_pages_max = max(ceil($nb_total/$nb_par_page),1);
	
	// Recuperation du numero de la page
	if ((isset($_GET['nb_page'])))
	{
        $nb_page=$_GET['nb_page']+0;
    }
	else
		$nb_page=1;
	if ($nb_page<1)
		$nb_page=1;
	if ($nb_page>$nb_pages_max)
		$nb_page=$nb_pages_max;
   
    // Recuperation des donnees dans la base SQL
    if (isset($_GET['order']))
	{
		switch ($_GET['order'])
		{
			case "ip":
				$query = "SELECT * FROM `se3_dhcp` ORDER BY INET_ATON(IP) ASC LIMIT ".(($nb_page-1)*100).",100";
				$order="ip";
				break;
			case "mac":
				$query = "SELECT * FROM `se3_dhcp` ORDER BY mac ASC LIMIT ".(($nb_page-1)*100).",100";
				$order="ip";
				break;
			case "name":
				$query = "SELECT * FROM `se3_dhcp` ORDER BY name ASC LIMIT ".(($nb_page-1)*100).",100";
				$order="ip";
				break;
			default:
				$query = "SELECT * FROM `se3_dhcp` ORDER BY INET_ATON(IP) ASC LIMIT ".(($nb_page-1)*100).",100";
				$order="ip";
				break;
		}
	}
	else
	{
		$query = "SELECT * FROM `se3_dhcp` ORDER BY INET_ATON(IP) ASC LIMIT ".(($nb_page-1)*100).",100";
		$order="ip";
	}
	$result = mysqli_query($dhcp_link,$query);
    
    //recup liste ip imprimantes
    $liste_imprimantes=search_imprimantes("printer-name=*","printers");
    //$resultat=search_imprimantes("printer-name=$mpenc","printers");
	for ($loopp=0; $loopp < count($liste_imprimantes); $loopp++)
	{
		$printer_uri = $liste_imprimantes[$loopp]['printer-uri'];
		$printer_name = $liste_imprimantes[$loopp]['printer-name'];
		//echo "liste imp : $printer_name $printer_uri" ;
		continue;
	}
    
    $clef = 0;

    @$content .= "<script type='text/javascript'>
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

	$content .= "Page";
	if ($nb_pages_max>1)
		$content .= "s";
	$content .= " ";
	for ($ii=1; $ii<$nb_pages_max+1; $ii++)
	{
		if ($ii==$nb_page)
			$content .= $ii." ";
		else
			$content .= "<a href=\"reservations.php?order=".$order."&nb_page=".$ii."\">".$ii."</a> ";
	}

    $content .= "<form name=\"lease_form\" method=post action=\"reservations.php?order=".$order."&nb_page=".$nb_page."\">\n";
    $content .= "<table border=\"1\" width=\"90%\">\n";
    $header = "<tr class=\"menuheader\"><td align=\"center\"><b>\n<a href=\"reservations.php?order=ip&nb_page=".$nb_page."\">" . gettext("Adresse IP") . "</a>";
    $header .= "</b></td><td align=\"center\"><b>\n<a href=\"reservations.php?order=mac&nb_page=".$nb_page."\">" . gettext("Adresse MAC") . "</a>";
    $header .="</b></td><td align=\"center\"><b>\n<a href=\"reservations.php?order=name&nb_page=".$nb_page."\">" . gettext("Nom NETBIOS") . "</a>";
    $header .="</b></td><td align=\"center\"><b>\n" . gettext("Parc(s)");
    $header .="</b></td><td align=\"center\"><b>\n";
    $header .= "<input type='hidden' name='action' value='valid' />\n";
    $header .= "<input type=\"submit\" name=\"button\" value=\"" . gettext("Valider les actions") . "\" />\n";
    $header .= "</td>\n";
    $header .="<td align=\"center\"><b>\n" . gettext("identifiants pour l'acces distant");
    $header .= "</b><br />\n";
    $header .= "</td></tr>\n";
    $content .= $header;
    $nbligne = 0;
	if (mysqli_num_rows($result))
	{
		while ($row = mysqli_fetch_assoc($result))
		{
			$content .= "<tr><td>\n";
			$content .= "<input type=\"text\" maxlength=\"15\" SIZE=\"15\" value=\"" . $row["ip"] . "\"  name=\"ip[$clef]\">\n";
	//      $content .= "<input type=\"hidden\" maxlength=\"15\" SIZE=\"15\" value=\"".$row["ip"]."\"  name=\"oldip[$clef]\">\n";
			$content .= "</td><td>\n";
			$content .= "<input type=\"text\" maxlength=\"17\" SIZE=\"17\" value=\"" . strtolower($row["mac"]) . "\"  name=\"mac[$clef]\" readonly>\n";
			$content .= "</td><td>\n";
			$content .= "<input type=\"text\" maxlength=\"20\" SIZE=\"20\" value=\"" . $row["name"] . "\"  name=\"name[$clef]\">\n";
			$content .= "<input type=\"hidden\" maxlength=\"20\" SIZE=\"20\" value=\"" . $row["name"] . "\"  name=\"oldname[$clef]\">\n";
			
			$suisje_printer = "" ;

			for ($loopp=0; $loopp < count($liste_imprimantes); $loopp++)
			{
				$printer_uri = $liste_imprimantes[$loopp]['printer-uri'];
				$printer_name = $liste_imprimantes[$loopp]['printer-name'];
				//echo "liste imp : $printer_name $printer_uri" ;
				if (preg_match("/http/", $printer_uri) or preg_match("/socket/", $printer_uri))
				{
					if (preg_match("/$row[ip]:/", $printer_uri))
					{
						$suisje_printer = "yes";
						break;
					}
					else
						{
						$suisje_printer = "no" ;
					}
				}
				elseif (preg_match("/lpd/", $printer_uri) or preg_match("/smb/", $printer_uri))
				{
					if (preg_match("#$row[ip]/#", $printer_uri))
					{
						$suisje_printer = "yes";
						break;
					}
					else
						{
						$suisje_printer = "no" ;
					}
				}
				else
				{
					if (preg_match("/$row[ip]$/", $printer_uri))
					{
						$suisje_printer = "yes";
						break;
					}
					else
					{
						$suisje_printer = "no" ;
					}
				}
			}
			if  ($suisje_printer=="yes")
			{
				$content.="<br><FONT color='blue'>" . gettext("Imprimante $printer_name") . "</FONT>\n"; 
			}
			
			$content .="</td><td>\n";
			$showid = 0;
			$listaction = "";
			// Est-ce que cette machine est enregistree ?
			$parc[$clef] = search_parcs($row["name"]);
			if ((count(search_machines("(cn=" . $row["name"] . ")", "computers"))) > 0)
			{
				if (isset($parc[$clef]))
				{
					foreach ($parc[$clef] as $keys2 => $value2)
					{
						$content.="<a href=../parcs/show_parc.php?parc=" . $parc[$clef][$keys2]["cn"] . ">" . $parc[$clef][$keys2]["cn"] . "</a><br>\n";
					}
				}
				// ajouter a un parc dans lequel la machine n'est  pas ?
				$content .= add_to_parc($parc[$clef], $clef);
				// windows linux ou imprimante ?
				
				// est ce que la machine est integree au domaine ?
				if (count(search_machines("(uid=" . $row["name"] . "$)", "computers")) > 0)
				{
					$listaction .="<OPTION value=\"renommer\">Renommer un poste win &#224; distance</OPTION>\n";
					$listaction .="<OPTION value=\"renommer\">Renommer un poste linux &#224; distance</OPTION>\n";
					$listaction .="<OPTION value=\"reintegrer\">R&#233;int&#233;grer</OPTION>\n";
				}
				else
				{ // this computer is not recorded on the domain
					// une imprimante ?
					if  ($suisje_printer=="yes")
					{ 
						$listaction .="<OPTION value=\"renommer-base\">Renommer dans la base</OPTION>\n";
					}
					else
					{
						$content.="<br><FONT color='red'>" . gettext("Pas au domaine!") . "</FONT>\n"; 
						$listaction .="<OPTION value=\"renommer-base\">Renommer dans la base</OPTION>\n";
						$listaction .="<OPTION value=\"renommer-linux\">Renommer un poste linux</OPTION>\n";
						$listaction .="<OPTION value=\"integrer\">Integrer un windows au domaine</OPTION>\n";
					}
					$showid = 1;
				}
			}
			else
			{ // this computer is not registered in ldap
				$list_computer = search_machines("(&(ipHostNumber=" . $row['ip'] . ")(macAddress=" . $row['mac'] . "))", "computers");
				$content.="<INPUT TYPE=\"hidden\"  name=\"parc[$clef]\">";
				if (count($list_computer) > 0)
				{
					$content.="<FONT color='red'>" . gettext("Autre nom : ") . $list_computer[0]["cn"] . "</FONT>\n";
					$content .= "<input type=\"hidden\" value=\"" . $list_computer[0]["cn"] . "\"  name=\"name[$clef]\">\n";
					$listaction .="<OPTION value=\"actualiser\">Actualiser la reservation</OPTION>\n";
				}
				else
				{
					$content.="<FONT color='red'>" . gettext("Non enregistr&#233;e") . "</FONT>\n";
					$listaction .="<OPTION value=\"integrer\">Integrer</OPTION>\n";
					$listaction .="<OPTION value=\"renommer-base\">Renommer dans la base</OPTION>\n";
					$showid = 1;
				}
			}
			$content .="</td><td align=\"center\">\n";
			$content .="<SELECT  name=\"action_res[$clef]\">";
			$content .="<OPTION value=\"none\">" . gettext("Action...") . "</OPTION>";
			$content .= $listaction;
			$content .= "<OPTION value=\"newip\">Changer l'adresse ip</OPTION>\n";
			$content .= "<OPTION value=\"supprimer\">Supprimer la reservation</OPTION>\n";
			$content .="</SELECT>\n";
			$content .="</td>\n";
			$content .="<td align=\"center\">\n";
			if ($showid)
			{
				$content .= "Admin local : <input type=\"text\" maxlength=\"20\" SIZE=\"15\" value=\"administrateur\"  name=\"localadminname[$clef]\" ><br>\n";
				$content .= "Mot de passe : <input type=\"text\" maxlength=\"20\" SIZE=\"8\" value=\"\"  name=\"localadminpasswd[$clef]\" ><br>\n";
			}
			$content .="</td></tr>\n";
			if ($nbligne++ == 10)
			{
				$content .=$header;
				$nbligne = 0;
			}
			$clef++;
		}
	}
	mysqli_free_result($result);
	deconnexion_db_dhcp($dhcp_link);
    $content .= "</table>\n";
    $content .= "<input type='hidden' name='action' value='valid'>\n";
    $content .= "<input type=\"submit\" name=\"button\" value=\"" . gettext("Valider les modifications") . "\">\n";
    $content .= "</form>";

    return $content;
}

// Return select form whith parc where host is not recorded

/**
 * Return select form whith parc where host is not recorded

 * @Parametres $parcs parc dans lequel on veut ajouter -  $keys

 * @Return Affichage HTML d'un select avec la liste des parcs

 */
function add_to_parc($parcs, $keys)
{
    require_once("includes/ldap.inc.php");
    $liste_parcs = search_machines("objectclass=groupOfNames", "parcs");
    if (count($liste_parcs)>0)
	{
        @$ret .="<SELECT  name=\"parc[$keys]\">";
        $ret .="<OPTION value=\"none\">" . gettext("Ajouter &#224; un parc...") . "</OPTION>";
        foreach ($liste_parcs as $keys => $value)
		{
            if (is_array($parcs))
			{
                foreach ($parcs as $keys2 => $value2)
				{
                    $parc_tab[] = $parcs[$keys2]["cn"];
                }
            }
			else
			{
                $parc_tab[] = "";
            }
            if (!in_array($value['cn'], $parc_tab))
			{
                $ret .="<OPTION value=\"" . $value['cn'] . "\">" . $value['cn'] . "</OPTION>\n";
            }
        }
        $ret .= "</SELECT>";
    }
    return $ret;
}

/**
 *  renvoie l'ip si elle est libre ou une ip fixe libre dans le meme vlan 

 * @Parametres $ip : Adresse IP a tester

 * @Return  adresse ip libre

 */
function get_free_ip($ip)
{
    include ("config.inc.php");
    require_once ("ihm.inc.php");
    $reseau = get_vlan($ip);
    $calcul_ip = floatval(sprintf("%u", ip2long($ip)));
    $ipmin = floatval(sprintf("%u", $reseau['ipmin']));
    $ipmax = floatval(sprintf("%u", $reseau['ipmax']));
    $gateway = floatval(sprintf("%u", $reseau['gateway']));
    $begin_range = floatval(sprintf("%u", $reseau['begin_range']));
    $end_range = floatval(sprintf("%u", $reseau['end_range']));


    if ((reservation($ip)) or ($ip == long2ip($reseau['gateway'])) or ($calcul_ip < $ipmin) or (($calcul_ip >= $begin_range) and ($calcul_ip <= $end_range)))
	{
        $calcul_free = $reseau['ipmin'];
        while (floatval(sprintf("%u", $calcul_free)) <= $ipmax)
		{
            if ((fping(long2ip($calcul_free)) == 1) or (reservation(long2ip($calcul_free)) or ($calcul_free == $reseau['gateway'])))
			{
                $calcul_free++;
            }
			elseif ($calcul_free == $reseau['begin_range'])
			{
                $calcul_free = $reseau['end_range'] + 1;
            }
			else
			{
				// print long2ip($calcul_free) . "le coupable<br>";
                return long2ip($calcul_free);
                $ipbuzy=1;
                return $ipbuzy;
            }
        }
    }
	else
	{
        // l'ip est libre
        return $ip;
    }
}

/**
 *  renvoie l'ip si elle est libre ou une ip fixe libre dans le meme vlan 

 * @Parametres $ip : Adresse IP a tester

 * @Return  adresse ip libre

 */
function get_free_ip2($ip)
{
    include ("config.inc.php");
    require_once ("ihm.inc.php");
    $reseau = get_vlan($ip);
    $calcul_ip = floatval(sprintf("%u", ip2long($ip)));
    $ipmin = floatval(sprintf("%u", $reseau['ipmin']));
    $ipmax = floatval(sprintf("%u", $reseau['ipmax']));
    $gateway = floatval(sprintf("%u", $reseau['gateway']));
    $begin_range = floatval(sprintf("%u", $reseau['begin_range']));
    $end_range = floatval(sprintf("%u", $reseau['end_range']));


    if ((reservation($ip)) or ($ip == long2ip($reseau['gateway'])) or (($calcul_ip >= $begin_range) and ($calcul_ip <= $end_range)))
	{
        $calcul_free = $reseau['ipmin'];
        while (floatval(sprintf("%u", $calcul_free)) <= $ipmax)
		{
            if ((fping(long2ip($calcul_free)) == 1) or (reservation(long2ip($calcul_free)) or ($calcul_free == $reseau['gateway'])))
			{
                $calcul_free++;
            }
			elseif ($calcul_free == $reseau['begin_range'])
			{
                $calcul_free = $reseau['end_range'] + 1;
            }
			else
			{
				// print long2ip($calcul_free) . "le coupable<br>";
                return long2ip($calcul_free);
                $ipbuzy=1;
                return $ipbuzy;
            }
        }
    }
	else
	{
        // l'ip est libre
        return $ip;
    }
}



/**
 *  renvoie les caracteristiques du vlan correspondant a l'ip 

 * @Parametres $ip : Adresse IP a tester

 * @Return  tableau associatif $reseau

 */
function get_vlan($ip)
{
    include ("config.inc.php");
    require_once ("ihm.inc.php");
    if ($dhcp_vlan == 0)
	{
        return get_network();
    }
	else
	{
        $reseau = get_network();
        $calcul_inetaddr = ip2long($ip);
        foreach ($reseau as $key => $value)
		{
            if ($calcul_inetaddr == ($calcul_inetaddr & $value['broadcast']))
			{
                print "L'ip est sur le vlan : $key<br>";
                return $value;
            }
        }
    }
}

/**
 *  renvoie les caracteristiques de tous vlan 

 * @Parametre aucun

 * @Return  $reseau[$vlan] tableau associatif avec les ip sous forme binaire

 */
function get_network()
{
    include ("config.inc.php");
    require_once ("fonc_outils.inc.php");
    require_once ("ihm.inc.php");
	$dhcp_link=connexion_db_dhcp();
    if ($dhcp_vlan == 0)
	{
        $ifconfig = ifconfig();
        $reseau['network'] = ip2long($ifconfig['network']);
        if (ip2long($dhcp_ip_min) > $reseau['network'])
		{
            $reseau['ipmin'] = ip2long($dhcp_ip_min);
        }
		else
		{
            $reseau['ipmin'] = $reseau['network'] + 51;
			$update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name='dhcp_ip_min'");
			mysqli_stmt_bind_param($update_query,"s", long2ip($reseau['ipmin']));
			mysqli_stmt_execute($update_query);
			mysqli_stmt_close($update_query);
        }
        $reseau['mask'] = ip2long($ifconfig['mask']);
        $reseau['broadcast'] = ip2long($ifconfig['broadcast']);
        $reseau['ipmax'] = $reseau['broadcast'] - 1;
        $reseau['begin_range'] = ip2long($dhcp_begin_range);
        $reseau['end_range'] = ip2long($dhcp_end_range);
        $reseau['gateway'] = ip2long($dhcp_gateway);
    }
	else
	{
        for ($vlan = 0; $vlan <= $dhcp_vlan; $vlan++)
		{
            $nomvar = "dhcp_reseau_" . $vlan;
            if (isset($$nomvar))
			{
                $reseau[$vlan]['network'] = ip2long($$nomvar);
                $nomvar = "dhcp_ip_min_" . $vlan;
                if (ip2long($$nomvar) > $reseau[$vlan]['network'])
				{
					$reseau[$vlan]['ipmin'] = ip2long($$nomvar);
                }
				else
				{
                    $reseau[$vlan]['ipmin'] = $reseau[$vlan]['network'] + 51;
					$update_query = mysqli_prepare($dhcp_link, "UPDATE params SET value=? WHERE name='dhcp_ip_min_" . $_POST['vlan'] . "'");
					mysqli_stmt_bind_param($update_query,"s", long2ip($reseau[$vlan]['ipmin']));
					mysqli_stmt_execute($update_query);
					mysqli_stmt_close($update_query);
                }
                $nomvar = "dhcp_masque_" . $vlan;
                $reseau[$vlan]['mask'] = ip2long($$nomvar);
                $reseau[$vlan]['broadcast'] = $reseau[$vlan]['network'] | ~$reseau[$vlan]['mask'];
                $reseau[$vlan]['ipmax'] = $reseau[$vlan]['broadcast'] - 1;
                $nomvar = "dhcp_begin_range_" . $vlan;
                $reseau[$vlan]['begin_range'] = ip2long($$nomvar);
                $nomvar = "dhcp_end_range_" . $vlan;
                $reseau[$vlan]['end_range'] = ip2long($$nomvar);
                $nomvar = "dhcp_gateway_" . $vlan;
                $reseau[$vlan]['gateway'] = ip2long($$nomvar);
            }
        }
    }
	deconnexion_db_dhcp($dhcp_link);
    return $reseau;
}

/**
 * Verifie si l'entree est dans la base SQL

 * @Parametres $ip - $mac - $hostname

 * @Return True - False

 */
function is_recorded_in_dhcp_database($ip, $mac, $hostname)
{
    require_once ("ihm.inc.php");
    // Recuperation des donnees dans la base SQL
    $dhcp_link=connexion_db_dhcp();
	
	$query = mysqli_prepare($dhcp_link, "SELECT count(*) as NB from se3_dhcp where ip=? and mac=? and name=? GROUP BY mac ASC");
	mysqli_stmt_bind_param($query,"sss", $ip, $mac, $hostname);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query,$value);
	mysqli_stmt_fetch($query);
	$nb_total=$value+0;
	mysqli_stmt_close($query);
    if ($nb_total==0)
	{
        return FALSE;
    }
	else
	{
        return TRUE;
    }
	deconnexion_db_dhcp($dhcp_link);
}

/**
 * Test la presence d'une adresse MAC dans la table se3_dhcp

 * @Parametres  $mac

 * @Return  True - False

 */
function registred($mac)
{
    require_once ("ihm.inc.php");
    $dhcp_link=connexion_db_dhcp();
	$query = mysqli_prepare($dhcp_link, "SELECT count(*) as NB from se3_dhcp where mac=? GROUP BY mac ASC");
	mysqli_stmt_bind_param($query,"s", $mac);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query,$value);
	mysqli_stmt_fetch($query);
	$nb_total=$value+0;
	mysqli_stmt_close($query);
    if ($nb_total==0)
	{
        return FALSE;
    }
	else
	{
        return TRUE;
    }
	deconnexion_db_dhcp($dhcp_link);
}

/**
 * Test la presence d'une adresse ip dans la table se3_dhcp

 * @Parametres  $ip

 * @Return  True - False

 */
function reservation($ip)
{
    require_once ("ihm.inc.php");
	$dhcp_link=connexion_db_dhcp();
	$query = mysqli_prepare($dhcp_link, "SELECT count(*) as NB from se3_dhcp where ip=? GROUP BY mac ASC");
	mysqli_stmt_bind_param($query,"s", $ip);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query,$value);
	mysqli_stmt_fetch($query);
	$nb_total=$value+0;
	mysqli_stmt_close($query);
    if ($nb_total==0)
	{
        return FALSE;
    }
	else
	{
        return TRUE;
    }
	deconnexion_db_dhcp($dhcp_link);
}

// add entry in se3_dhcp mysql table for reservation

/**
 * add entry in se3_dhcp mysql table for reservation

 * @Parametres $ip - $mac - $name - $force

 * @Return $ret

 */
function add_reservation($ip, $mac, $name, $force)
{
    require_once ("ihm.inc.php");
    $ret="";
    if (set_ip_in_lan($ip))
	{
        $oldip= $ip;
		if ($force==1)
			$ip = get_free_ip2($ip);
		else
			$ip = get_free_ip($ip);
        if ("$ip" == "")
		{
            $ret = gettext("<FONT color='red'> Attention : Impossible de r&#233;server une ip dans ce vlan </FONT>");
        }
		else
		{
            $error = already_exist($ip, $name, $mac);
            if ($error == "")
			{
                $dhcp_link=connexion_db_dhcp();  
				$update_query = mysqli_prepare($dhcp_link, "INSERT INTO `se3_dhcp` (`ip`, `mac`, `name`) VALUES (?, ?, ?)");
				mysqli_stmt_bind_param($update_query,"sss", $ip, $mac, $name);
				mysqli_stmt_execute($update_query);
				mysqli_stmt_close($update_query);
				deconnexion_db_dhcp($dhcp_link);
                exec("/usr/bin/sudo /usr/share/se3/scripts/integreDomaine.sh ldap $name $ip $mac");
                if ($ip != $oldip)
				{
                    $ret = gettext("<FONT color='red'> Attention : </FONT>l'adresse choisie pour cette machine est d&#233;j&#224; prise ou elle se situe dans la plage dynamique $name --> $oldip,recherche d'une adresse libre...<br>"); 
                }
                $ret .= gettext("Mise en place d'une r&#233;servation pour la machine  $name --> $ip<br>");
            }
        }
    }
	else
	{
        $ret = gettext("<FONT color='red'> Attention : l'addresse choisie pour cette machine n'est pas valide </FONT>" . "$name --> $ip<br>");
    }
    return $ret;
}

/**
 * Test si une reservation existe deja pour cette machine

 * @Parametres $ip : ip de la machine - $name : nom de la machine - $mac : adresse mac de la machine

 * @Return Affichage HTML si la machine existe deja

 */
function already_exist($ip, $name, $mac)
{
    require_once ("ihm.inc.php");
    // Recuperation des donnees dans la base SQL
	$dhcp_link=connexion_db_dhcp();
	$query = mysqli_prepare($dhcp_link, "SELECT count(*) as NB from se3_dhcp where ip=?");
	mysqli_stmt_bind_param($query,"s", $ip);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query,$value);
	mysqli_stmt_fetch($query);
	$nb_total=$value+0;
	mysqli_stmt_close($query);
    if ($nb_total == 0)
	{
        $error = "";
    }
	else
	{
        $error = gettext("Cette adresse ip est d&#233;j&#224; utilis&#233;e : " . $ip) . "\n<br />";
    }

	$query = mysqli_prepare($dhcp_link, "SELECT count(*) as NB from se3_dhcp where mac=?");
	mysqli_stmt_bind_param($query,"s", $mac);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query,$value);
	mysqli_stmt_fetch($query);
	$nb_total=$value+0;
	mysqli_stmt_close($query);
    if ($nb_total == 0)
	{
        $error.="";
    }
	else
	{
        $error.=gettext("Cette adresse mac est d&#233;j&#224; utilis&#233;e : " . $mac) . "\n<br />";
    }

	$query = mysqli_prepare($dhcp_link, "SELECT count(*) as NB from se3_dhcp where name=?");
	mysqli_stmt_bind_param($query,"s", $name);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query,$value);
	mysqli_stmt_fetch($query);
	$nb_total=$value+0;
	mysqli_stmt_close($query);	
    if ($nb_total == 0)
	{
        $error.="";
    }
	else
	{
        $error.=gettext("Ce nom est d&#233;j&#224; utilis&#233; : " . $name) . "\n<br />";
    }
    return $error;
	deconnexion_db_dhcp($dhcp_link);
}

/**
 * Supprime une reservation

 * @Parametres $ip : Ip de la machine - $mac : Adresse mac de la machine - $name : Nom de la machine

 * @Return Message d'erreur SQL en cas de non suppression

 */
function suppr_reservation($ip, $mac, $name)
{
    require_once ("ihm.inc.php");
    $error = "Suppression de l'entr&#233;e pour la machine $name --> $ip<br>";
	$dhcp_link=connexion_db_dhcp();
	$delete_query = mysqli_prepare($dhcp_link, "DELETE FROM `se3_dhcp` where `ip`=? AND `mac`=? AND `name`=?");
	mysqli_stmt_bind_param($delete_query,"sss", $ip, $mac, $name);
	mysqli_stmt_execute($delete_query);
	mysqli_stmt_close($delete_query);
	deconnexion_db_dhcp($dhcp_link);
    return $error;
}
/**
 * renomme une machine sous linux 

 * @Parametres $ip : Ip de la machine - $mac : Adresse mac de la machine - $name : Nom de la machine

 * @Return Message d'erreur SQL en cas de d'echec de l'update

 */

/**
 * renomme une machine sous linux 

 * @Parametres $ip : Ip de la machine - $mac : Adresse mac de la machine - $name : Nom de la machine

 * @Return Message d'erreur SQL en cas de d'echec de l'update

 */

function renomme_linux($ip, $mac, $name)
{
	require_once ("ihm.inc.php");
	$ret = "Renommage du poste $ip <br>";
	$scriptfile="/tmp/rename.sh";
	if (file_exists($scriptfile)) unlink($scriptfile);

	$fich=fopen("$scriptfile","w+");
	fwrite($fich,"#!/bin/bash
echo \"$name\" > \"/etc/hostname\"
invoke-rc.d hostname.sh stop 
invoke-rc.d hostname.sh start

echo \"
127.0.0.1    localhost
127.0.1.1    $name

# The following lines are desirable for IPv6 capable hosts
::1      ip6-localhost ip6-loopback
fe00::0  ip6-localnet
ff00::0  ip6-mcastprefix
ff02::1  ip6-allnodes
ff02::2  ip6-allrouters
\" > \"/etc/hosts\"

reboot");
	fclose($fich);

	// Copie du script sur l'esclave avec scp
	exec ("/usr/bin/scp $scriptfile root@$ip:/tmp/", $AllOutput, $ReturnValue);
	// chmod +x du script bash
	exec ("ssh -l root  $ip 'chmod +x $scriptfile ; $scriptfile'", $AllOutput, $ReturnValue);
	if ($ReturnValue==0)
	{
		echo "renommage distant en cours....";
	}
	else
	{
		echo "renommage distant impossible ";
	}
return $ret;
}

/**
 * renomme une reservation et met a jour l'enregistrement ldap

 * @Parametres $ip : Ip de la machine - $mac : Adresse mac de la machine - $name : Nom de la machine

 * @Return Message d'erreur SQL en cas de d'echec de l'update

 */
function renomme_reservation($ip, $mac, $name)
{
    require_once ("ihm.inc.php");
    $ret = "Modification de l'entr&#233;e pour la machine $name : $ip<br>";
    $error = already_exist("ipbidon", $name, "macbidon");
    if ($error == "")
	{
        $dhcp_link=connexion_db_dhcp();
		$update_query = mysqli_prepare($dhcp_link, "UPDATE se3_dhcp SET name=? where `ip`=? AND `mac`=?");
		mysqli_stmt_bind_param($update_query,"sss", $name, $ip, $mac);
		mysqli_stmt_execute($update_query);
		mysqli_stmt_close($update_query);
		deconnexion_db_dhcp($dhcp_link);
        $ret .= exec("/usr/bin/sudo /usr/share/se3/scripts/integreDomaine.sh ldap $name $ip $mac");
        return $ret;
    }
	else
	{
        return $error;
    }
}

/**
 * change l'ip d'une reservation et met a jour l'enregistrement ldap

 * @Parametres $ip : Ip de la machine - $mac : Adresse mac de la machine - $name : Nom de la machine

 * @Return Message d'erreur SQL en cas de d'echec de l'update

 */
function change_ip_reservation($ip, $mac, $name)
{
    require_once ("ihm.inc.php");
    $ret = "";
    if (set_ip_in_lan($ip))
	{
        $oldip= $ip;
        $ip = get_free_ip($ip);
        if ("$ip" == "")
		{
            $ret = gettext("Impossible de reserver une ip dans ce vlan");
        }
		else
		{
            $ret = already_exist($ip, "nombidon", "macbidon");
            if ($ret == "")
			{
                $dhcp_link=connexion_db_dhcp();
				$update_query = mysqli_prepare($dhcp_link, "UPDATE se3_dhcp SET ip=?  where `name`=? AND `mac`=?");
				mysqli_stmt_bind_param($update_query,"sss", $ip, $name, $mac);
				mysqli_stmt_execute($update_query);
				mysqli_stmt_close($update_query);
				deconnexion_db_dhcp($dhcp_link);
                $ret .= exec("/usr/bin/sudo /usr/share/se3/scripts/integreDomaine.sh ldap $name $ip $mac");
                if ($ip != $oldip)
				{
                    $ret .= gettext("<FONT color='red'> Attention : </FONT>l'adresse choisie pour cette machine est d&#233;j&#224; prise ou elle se situe dans la plage dynamique $name --> $oldip,recherche d'une adresse libre...<br>"); 
				}
                $ret .= gettext("R&#233;servation modifi&#233;e pour la machine $name  : $ip\n");
            }
        }
    }
	else
	{
        $ret = gettext("Cette addresse n'est pas valide : " . $ip);
    }
    return $ret;
}

/**
 * Indique l'etat du serveur DHCP

 * @Parametres
 * @Return Affichage HTML sur l'etat

 */
function dhcpd_status()
{
    exec("sudo /usr/share/se3/scripts/makedhcpdconf state", $ret);
    if ($ret[0] == "1")
	{
        $content = gettext("Le serveur DHCP est : ") . "<FONT color='green'>" . gettext("actif") . "</FONT>";
    }
	else
	{
        $content = gettext("Le serveur DHCP est : ") . "<FONT color='red'>" . gettext("inactif") . "</FONT>";
    }
    return $content;
}

/**
 * Redemarre le serveur DHCP

 * @Parametres

 * MongoUpdateBatch@Return

 */
function dhcpd_restart()
{
    exec("sudo /usr/share/se3/scripts/makedhcpdconf", $ret);
}

/**
 * Stop le serveur DHCP

 * @Parametres
 * MongoUpdateBatch@Return

 */
function dhcpd_stop()
{
    exec("sudo /usr/share/se3/scripts/makedhcpdconf stop", $ret);
}

/**
 * Valide le nom d'une machine

 * @Parametres  $nom : Nom a valider

 * @Return  0 si faux - 1 si Ok

 */
function valid_name($nom)
{
    $nom = strtoupper($nom);
    $l = strlen($nom);
    if ($l == 0)
	{
        print gettext("<br><I>le nom doit contenir au moins une lettre</I>");
        return 0;
    }
    if ($l > 63)
	{
        print gettext("<br><I>le nom $nom ne doit pas d&#233;passer 63 caract&#232;res</I>");
        return 0;
    }
    for ($i = 0; $i < $l; $i++)
	{
        $c = substr($nom, $i, 1);
        if (!preg_match("/[a-zA-Z0-9_-]/", $c, $tab_err))
		{
            print gettext("<br><I>caract&#232;re $c incorrect dans hostname $nom </I>");
            return 0;
        }
    }
    $prem = substr($nom, 0, 1);
    if (!preg_match("/[a-zA-Z0-9]/", $prem, $tab_err))
	{
        print gettext("<br><I>le nom $nom doit commencer par une lettre ou un chiffre</I>");
        return 0;
    }
    $der = substr($nom, $l - 1, 1);
    if (!preg_match("/[a-zA-Z0-9]/", $der, $tab_err))
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
    $tab_mac = explode(':', $mac); /* transforme adresse mac en tableau de 6 octets */
    if (count($tab_mac) <> 6)
	{
        print gettext("<br><I>Attention : une adresse MAC doit avoir la forme xx:xx:xx:xx:xx:xx</I>");
        return(0);
    }
    $mac = strtoupper($mac);
    $l = strlen($mac);
    for ($i = 0; $i < $l; $i++)
	{
        $c = substr($mac, $i, 1);
        if (!preg_match("/[A-F0-9:]/", $c, $tab_err))
		{
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
    $ch_mac = strtoupper($ch_mac);
    $mac_retour = "";
    $tab_mac = explode(':', $ch_mac); /* transforme l'adresse mac en tableau de 4 chaines */
    if (count($tab_mac) <> 6)
	{
        $z = count($tab_mac);
        print gettext("<br><I>Attention : une adresse mac doit avoir la forme xx:xx:xx:xx:xx:xx</I>");
        return("");
    }
	else
	{
        for ($i = 0; $i < 6; $i++)
		{
            while (strlen($tab_mac[$i]) < 2)
                $tab_mac[$i] = '0' . $tab_mac[$i];
            $mac_retour = $mac_retour . $tab_mac[$i];
            if ($i < 5)
                $mac_retour = $mac_retour . ':'; /* on ajoute un point sauf au dernier */
        }
        /* verification caracteres valides */
        if (!preg_match("/[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}/", $mac_retour, $tab_err)) {
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
    $liste_name = trim($liste_name);   /* supprime espaces a droite et a gauche */
    if ($liste_name == "")
        return 1;
    $tab_name = explode(' ', $liste_name); /* transforme la liste de noms en tableau de noms */
    $nb_name = count($tab_name);
    for ($i = 0; $i < $nb_name; $i++)
	{
        $name = $tab_name[$i];
        if (!valid_name($name))
		{
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
    $nb_lignes = count($tableau);
    $separ = ";";
    $z = 0;
    $erreur = 0;  // si erreur est vrai en sortie de boucle, annuler transaction
    $faux_nom = 1;    // si jamais le nom n'est pas renseigne, on l'invente
    // avec un numero
    while ($z < $nb_lignes)
	{
        // sauter eventuelle ligne vide
        // c'est souvent le cas pour la derniere ligne du presse-papier
        if (trim($tableau[$z]) == "")
            break;
        // decoupage de chaque ligne a partir du separateur |
        $tab_ligne = explode($separ, $tableau[$z]);
        $ip = trim($tab_ligne[0]);
        if (!set_ip_in_lan($ip))
		{
            print("<br>");
            print gettext("Erreur sur adresse ip : $tab_ligne[0]");
            $ligne = $z + 1;
            print(" Ligne n $ligne");
            $erreur = 1;
            $z++;
            continue;
        }
//		$ip=format_ip($ip);
        if ($ip == "")
		{
            print("<br>");
            print gettext("Erreur sur adresse ip : $tab_ligne[0]");
            $ligne = $z + 1;
            print gettext(" Ligne n $ligne");
            $erreur = 1;
            $z++;
            continue;
        }
        $nom = trim($tab_ligne[1]);
        if (!valid_name($nom))
		{
            print("<br>");
            print gettext("Erreur sur hostname : $tab_ligne[1] ");
            $ligne = $z + 1;
            print gettext("Ligne n $ligne");
            $erreur = 1;
            $z++;
            continue;
        }
        $mac = trim($tab_ligne[2]);
        if (!valid_mac($mac))
		{
            print("<br>");
            print gettext("Erreur sur adresse mac : $tab_ligne[2] ");
            $ligne = $z + 1;
            print gettext("Ligne n $ligne");
            $erreur = 1;
            $z++;
            continue;
        }
        $mac = format_mac($mac);
        if ($mac == "")
		{
            print("<br>");
            print gettext("Erreur sur adresse mac : $tab_ligne[2] ");
            $ligne = $z + 1;
            print gettext("Ligne n $ligne");
            $erreur = 1;
            $z++;
            continue;
        }

        require_once ("ihm.inc.php");
		$dhcp_link=connexion_db_dhcp();
        // Recuperation des donnees dans la base SQL
		$query = mysqli_prepare($link_clamav, "SELECT * from se3_dhcp where mac=?");
		mysqli_stmt_bind_param($query,"s", $mac);
		mysqli_stmt_execute($query);
		mysqli_stmt_store_result($query);
		$v_count=mysqli_stmt_num_rows($query);
		mysqli_stmt_close($query);
        if ($v_count <> 0)
		{
            print("<br>");
            print gettext("Adresse mac $mac d&#233;ja utilis&#233;e ");
            $ligne = $z + 1;
            print gettext(" Ligne n $ligne");
            $erreur = 1;
            $z++;
            continue;
        }
		
        $query = mysqli_prepare($link_clamav, "SELECT * from se3_dhcp where name=?");
		mysqli_stmt_bind_param($query,"s", $name);
		mysqli_stmt_execute($query);
		mysqli_stmt_store_result($query);
		$v_count=mysqli_stmt_num_rows($query);
		mysqli_stmt_close($query);
        if ($v_count <> 0)
		{
            print("<br>");
            print gettext("Hostname $nom d&#233;ja utilis&#233; ");
            $ligne = $z + 1;
            print gettext(" Ligne n $ligne");
            $erreur = 1;
            $z++;
            continue;
        }

        $nominmaj = strtolower($nom);
		$query = mysqli_prepare($link_clamav, "SELECT * from se3_dhcp where name=?");
		mysqli_stmt_bind_param($query,"s", $nominmaj);
		mysqli_stmt_execute($query);
		mysqli_stmt_store_result($query);
		$v_count=mysqli_stmt_num_rows($query);
		mysqli_stmt_close($query);
        if ($v_count <> 0)
		{
            print("<br>");
            print gettext("Hostname $nominmaj d&#233;ja utilis&#233; ");
            $ligne = $z + 1;
            print gettext(" Ligne n $ligne");
            $erreur = 1;
            $z++;
            continue;
        }

        if ($nom == "")
		{
            $nom = "X" . $faux_nom;
            $faux_nom++;
        }
        if (!valid_name($nom))
		{
            print("<br>");
            print gettext("Nom $nom incorrect ");
            $ligne = $z + 1;
            print gettext(" Ligne n $ligne");
            $erreur = 1;
            $z++;
            continue;
        }
		deconnexion_db_dhcp($dhcp_link);
        // tout est ok, on insere la ligne
        add_reservation($ip, $mac, $nom);
        $z++;
        if ($erreur)
		{
            print("<br><br><b>" . gettext("Erreurs durant le traitement") . "</b><br>");
        }
    }
    print gettext("Traitement termin&#233;<br>");
    print gettext("Nb de lignes trait&#233;es : $z");
    dhcpd_restart();
    $mac = "";
    $ip = "";
    $nom = "";
}

/**
 * Fonctions: Test la presence de dhcp_vlan dans la table params et en retourne la valeur

 * @Parametres $dhcp_vlan_valeur : Contenu de dhcp_vlan

 * @Return - 0 si pas de vlan - n nombre de vlan

 */
function dhcp_vlan_test()
{
    include ("config.inc.php");
    // si la variable dhcp_vlan n'est pas definie on cree l'entree dans la base sql
    if ($dhcp_vlan == "")
	{
        $dhcp_link=connexion_db_dhcp();
		mysqli_query($dhcp_link,"INSERT into params set id='NULL', name='dhcp_vlan', value='0', srv_id='0',descr='Nombre de vlan',cat='7'");
		deconnexion_db_dhcp($dhcp_link);
        return 0;
    }
	else
	{
        return $dhcp_vlan;
    }
}

/**
 * Verifie l'existance des champs dans la table params pour les vlans

 * @Parametres $nom_champ : Nom du champ a tester
 * MongoUpdateBatch@Return


 */
function dhcp_vlan_champ($nom_champ)
{
    require_once ("config.inc.php");
    if ($$nom_champ == "")
	{
        $dhcp_link=connexion_db_dhcp();
		mysqli_query($dhcp_link,"INSERT into params set id='NULL', name='$nom_champ', value='', srv_id='',descr='',cat='7'");
		deconnexion_db_dhcp($dhcp_link);
    }
}

/**
 * rename domain client

 * @Parametres $ip - $mac - $name

 * @Return $ret

 */
function renomme_domaine($ip, $oldname, $name)
{
    require_once ("ihm.inc.php");
    $ret = "<br>\n";
    $ret .= exec("/usr/bin/sudo /usr/share/se3/scripts/integreDomaine.sh renomme $name $ip $oldname adminse3 $xppass") . "<br>\n";
    $ret .= "<p align='center' style='color:red;'> Attention : Si l'emetteur ne reboote pas tout seul en administrateur local, ouvrez une session administrateur local et lancez <br>c:\\netinst\\shutdown.cmd</p>\n";
    return $ret;
}

/**
 * integrate domain client

 * @Parametres $ip - $mac - $name  [ $admin - $adminpasswd ]

 * @Return $ret

 */
function integre_domaine($ip, $mac, $name, $admin, $adminpasswd)
{
    require_once ("ihm.inc.php");
    if ($adminpasswd == "xxx")
	{
        $adminpasswd = "";
    }
    //doit-on faire verifier l'existence dans le ldap ?
    $ret = "<br>\n";
    $ret .= exec("/usr/bin/sudo /usr/share/se3/scripts/integreDomaine.sh rejoint $name $ip $mac $admin $adminpasswd") . "<br>\n";
    $ret .= "<p align='center' style='color:red;'> Attention : si l'emetteur ne reboote pas tout seul en administrateur local, ouvrez une session administrateur local et lancez <br>c:\\netinst\\shutdown.cmd </p>\n";
    return $ret;
}


?>
