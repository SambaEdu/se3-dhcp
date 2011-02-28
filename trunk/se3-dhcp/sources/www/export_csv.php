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

include "config.inc.php";

/******************** Export les tables ******************************************************/


$jour=date("d-n-y");
$query = mysql_query("select * from se3_dhcp");
header("Content-Type: application/csv-tab-delimited-table");
header("Content-disposition: filename=inventaire-$jour.csv");

if (mysql_num_rows($query) != 0) {
		
//	echo"\n";
       	while($row = mysql_fetch_array($query)) {
		$AFFICHE="$row[1];$row[3];$row[2]";	
			
		// Affichage final
		echo "$AFFICHE\n";
	}
}	
