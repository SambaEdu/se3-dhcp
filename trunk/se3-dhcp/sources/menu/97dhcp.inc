<?php

/**
* Menu Serveur DHCP

* @Version $Id$

* @Projet LCS / SambaEdu

* @Auteurs Equipe TICE crdp de Caen

* @Note:

* @Licence Distribue sous la licence GPL
*/

/**

* file: 97dhcp.inc
* @Repertoire: includes/menu.d/
*/


$liens1 = array(array(
 "Serveur dhcp", "system_is_admin",3,
 "Configuration", "dhcp/config.php","system_is_admin",3,
 "Gestion des baux", "dhcp/baux.php","system_is_admin",3,
 "R&#233;servations actives", "dhcp/reservations.php","system_is_admin",3,
 "Import - Export", "dhcp/import.php","system_is_admin",3,
));

$liens=array_merge($liens,$liens1);
?>
