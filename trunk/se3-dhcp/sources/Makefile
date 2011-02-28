#!/usr/bin/make -f
# Makefile for se3_dhcp

all:

install:
    # Add here commands to install the package into debian/slis-core.
    # Install des fichiers de configuration et de cron.
	cp -R www/* $(DESTDIR)/var/www/se3/dhcp/
	cp -R menu/* $(DESTDIR)/var/www/se3/includes/menu.d/
	cp -R install-scripts/* $(DESTDIR)/var/cache/se3_install/se3-dhcp/
	cp -R scripts/* $(DESTDIR)/usr/share/se3/scripts/

clean:
