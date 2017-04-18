<?php


/**

   * Export le dhcp au format CSV
   * @Version $Id$
   
   * @Projet LCS / SambaEdu

   * @auteurs - Laurent Joly

   * @note
   
   * @Licence Distribue sous la licence GPL
   
*/
						
/**

   * @Repertoire: dhcp

   * file: export_csv.php
*/


// loading libs and init
@session_start();
require_once "functions.inc.php";
include "ldap.inc.php";
include "ihm.inc.php";
$login=isauth();

require_once "dhcpd.inc.php";

$jour=date("d-n-y");
header("Content-Type: application/csv-tab-delimited-table");
header("Content-disposition: filename=se3_dhcp_".strftime("%Y%m%d-%H%M%S").".csv");

if (is_admin("system_is_admin",$login)=="Y")
{
	search_doublons_mac('y');
}
else
{
	echo "Acces Refuse";
}
?>
