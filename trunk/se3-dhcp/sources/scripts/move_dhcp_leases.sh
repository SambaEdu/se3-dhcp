#!/bin/sh

# $Id$ #

#####Supprime le fichier dhcp.leases#####

rm -f  /var/lib/dhcp/dhcpd.leases
touch  /var/lib/dhcp/dhcpd.leases
service isc-dhcp-server restart
