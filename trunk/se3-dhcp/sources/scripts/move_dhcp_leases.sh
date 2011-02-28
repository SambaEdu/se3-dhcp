#!/bin/sh

# $Id$ #

#####Supprime le fichier dhcp.leases#####

rm -f  /var/lib/dhcp3/dhcpd.leases
touch  /var/lib/dhcp3/dhcpd.leases
/etc/init.d/dhcp3-server restart
