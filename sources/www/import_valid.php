<?php


/**

   * Verifie l'import des csv
   * @Version $Id$
   
   * @Projet LCS / SambaEdu

   * @auteurs  Philippe CHadefaux (Plouf)	

   * @note 
   
   * @Licence Distribue sous la licence GPL
   
*/
						
/**
   * @Repertoire: dhcp

   * file: import_valid.php
*/


// loading libs and init
include "entete.inc.php";
include "ldap.inc.php";
include "ihm.inc.php";


$action=$_POST['action'];
if (is_admin("system_is_admin",$login)=="Y")
{

	//aide
        $_SESSION["pageaide"]="Le_module_DHCP#Import_.2F_Export";

	echo "<h1>".gettext("Importation DHCP")."</h1>";
//	$content .= "<h1>".gettext("R&#233;servations existantes")."</h1>";
	// Prepare HTML code
	switch($action) {
	case 'cc' :
		
		require_once "dhcpd.inc.php";
		$tableau=explode("\n",$_POST['saisie']);
        	traite_tableau($tableau);
	case 'file' :
		if  ( move_uploaded_file($_FILES['file_name']['tmp_name'], $_FILES['file_name']['name'])) {
			require_once "dhcpd.inc.php";
	        	$tableau=file($_FILES['file_name']['name']);
        		traite_tableau($tableau);
		}	
		
	default :
		// anti  hacking
		$title = '';
		$content = '';
		return;
	}
												
	
} else {
	print (gettext("Vous n'avez pas les droits n&#233;cessaires pour ouvrir cette page..."));
}

// Footer
include ("pdp.inc.php");


?>
