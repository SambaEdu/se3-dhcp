<?php

/**

   * Import - Export les entrees DHCP
   * @Version $Id$

   * @Projet LCS / SambaEdu

   * @auteurs Philippe Chadefaux
   * @auteurs Eric Mercier (Academie de Versailles)

   * @note

   * @Licence Distribue sous la licence GPL

*/

/**
   * @Repertoire: dhcp

   * file: import.php
*/




// loading libs and init
include "entete.inc.php";
include "ldap.inc.php";
include "ihm.inc.php";
require_once "dhcpd.inc.php";


$action=$_POST['action'];
if (is_admin("system_is_admin",$login)=="Y")
{

	//aide
	$_SESSION["pageaide"]="Le_module_DHCP#Import_.2F_Export";


	$content .= "<h1>".gettext("Import - Export")."</h1>";

       $content .= "<H3>".gettext("Import : M&#233;thode par fichier");
       $content .= "<u onmouseover=\"return escape".gettext("('Le fichier au format texte, doit avoir une entr&#233;e sur chaque ligne, et le ; comme s&#233;parateur.<br><br>Une entr&#233;e (ligne) doit avoir le format suivant : <br> <b>- Champ 1 :</b> adresse ip sur 15 caract&#232;res ( format xxx.xxx.xxx.xxx )<br> <b>- Champ 2 :</b> Nom de machine sur 20 caract&#232;res maxi<br><b> - Champ 3 :</b> Adresse MAC sur 17 caract&#232;res ( format xx:xx:xx:xx:xx:xx)')")."\">";
	$content .= "<img name=\"action_image2\"  src=\"../elements/images/system-help.png\"></u></H3>";

       $content .= "<FORM NAME=\"import_fichier\" ACTION=\"import_valid.php\" ENCTYPE=\"multipart/form-data\" METHOD=\"POST\">";
       $content .= "<P>".gettext("Indiquer le fichier :")." <INPUT TYPE=FILE NAME=\"file_name\">";
       $content .= "<INPUT TYPE=SUBMIT NAME=\"valid\" VALUE=\"".gettext("Importer")."\">";
       $content .= "<INPUT TYPE=HIDDEN NAME=\"action\" VALUE=\"file\">";
	$content .= "</FORM>";
       $content .= "</B>";
       $content .= "<H3>".gettext("Import : M&#233;thode par copier coller");

       $content .= " <u onmouseover=\"return escape".gettext("('Copier/coller dans l\'espace texte.<br>Les entr&#233;es doivent avoir une entr&#233;e sur chaque ligne, et le ; comme s&#233;parateur.<br><br>Une entr&#233;e (ligne) doit avoir le format suivant : <br> <b>- Champ 1 :</b> adresse ip sur 15 caract&egrave;res ( format xxx.xxx.xxx.xxx )<br> <b>- Champ 2 :</b> Nom de machine sur 20 caract&#232;res maxi<br><b> - Champ 3 :</b> Adresse MAC sur 17 caract&#232;res ( format xx:xx:xx:xx:xx:xx)')")."\">";
	$content .= "<img name=\"action_image2\"  src=\"../elements/images/system-help.png\"></u></H3>";
       $content .= "<FORM NAME=import_pp  METHOD=POST ACTION=import_valid.php>";
       $content .= "<TABLE BORDER=1 width=100% CELLPADDING=0 CELLSPACING=0>";
       $content .= "<TR><TD>".gettext("Coller ici la table des adresses")."</TD></TR>";
       $content .= "<TR><TD><TT><TEXTAREA  NAME=saisie ROWS=15 COLS=72></TEXTAREA></TT></TD></TR>";
       $content .= "</TABLE>";
       $content .= "<INPUT TYPE=HIDDEN NAME=\"action\" VALUE=\"cc\">";
//       $content .= "<H3>Choisir le separateur :</H3>";
//       $content .= "<INPUT TYPE=RADIO NAME=\"separ\" VALUE=\"tab\" CHECKED>Tabulation <INPUT TYPE=RADIO NAME=\"separ\" VALUE=\"pipe\"> | ( caract&#232;re pipe )<INPUT TYPE=RADIO NAME=\"separ\" VALUE=\"cvs\"> ; (caract&#232;re point_virgule)";
       $content .= "<P><INPUT TYPE=SUBMIT NAME=\"valid\" VALUE=\"".gettext("Importer")."\"></P>";
       $content .= "</FORM>";

	$content .= "<BR>";
        $content .= "<H3>".gettext("Exporter au format csv");

       $content .= " <u onmouseover=\"return escape".gettext("('Exporte les entr&#233;es du serveur DHCP vers un fichier au format csv (s&#233;parateur ;)')")."\">";
	$content .= "<img name=\"action_image2\"  src=\"../elements/images/system-help.png\"></u></H3>";
	$content .= "<A HREF=\"export_csv.php\">".gettext("Exporter les entr&#233;es du serveur DHCP")."</A>";


	$content .= "<BR>";


	$content .= "<br />";
	$content .= "<h3>".gettext("G&#233;n&#233;rer le csv d'apr&#232;s l'annuaire LDAP");
        $content .= " <u onmouseover=\"return escape".gettext("('Exporte les donn&#233;es des machines depuis l\'annuaire LDAP dans un fichier au format csv (s&#233;parateur ;)')")."\">";
	$content .= "<img name=\"action_image2\"  src=\"../elements/images/system-help.png\"></u></h3>";
	$content.="<a href='se3_genere_csv_dhcp_machines.php'>G&#233;n&#233;rer le csv d'apr&#232;s le contenu de l'annuaire LDAP</a>";
	
	$content .= "<BR>";


	$content .= "<br />";
	$content .= "<h3>".gettext("Modifier le plan d'adressage des machines clientes");
        $content .= " <u onmouseover=\"return escape".gettext("('Permet de modifier l\'adressage IP des clients d\'apres un fichier au format csv de l\'adressage actuel')")."\">";
	$content .= "<img name=\"action_image2\"  src=\"../elements/images/system-help.png\"></u></h3>";
	$content.="<a href='readressage_ip.php'>Modifier le plan d'adressage des machines clientes</a>";

	print "$content\n";
} else {
	print (gettext("Vous n'avez pas les droits n&#233;cessaires pour ouvrir cette page..."));
}

// Footer
include ("pdp.inc.php");

?>
