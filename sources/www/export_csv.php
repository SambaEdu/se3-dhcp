<?php


/**

   * Export le dhcp au format CSV
   * @Version $Id$
   
   * @Projet LCS / SambaEdu

   * @auteurs - Philippe Chadefaux (Plouf)	

   * @note
   
   * @Licence Distribue sous la licence GPL
   
*/
						
/**

   * @Repertoire: dhcp

   * file: export_csv.php
*/

// loading libs and init
@session_start();
require_once "functions.php";
include "ldap.inc.php";
include "ihm.inc.php";
$login=isauth();

require_once "dhcpd.inc.php";

$jour=date("d-n-y");
header("Content-Type: application/csv-tab-delimited-table");
header("Content-disposition: filename=inventaire-$jour.csv");

if (is_admin("system_is_admin",$login)=="Y")
{
	$link=connexion_db_dhcp();
	$query = "select * from se3_dhcp";
    $result = mysqli_query($link,$query);
	
	if (mysqli_num_rows($result))
	{
		while ($row = mysqli_fetch_assoc($result))
		{
			echo "$row[1];$row[3];$row[2]\n";
		}
	}
	mysqli_free_result($result);
	deconnexion_db_dhcp($link);
}
else
{
	echo "Acces Refuse";
}
?>
