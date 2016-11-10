#
# Mise à jour de la structure de la table `dhcp`
#

ALTER TABLE se3_dhcp
MODIFY ip VARCHAR( 15 ),
MODIFY mac VARCHAR( 17 ),
MODIFY name VARCHAR( 255 ),
ADD UNIQUE (ip),
ADD UNIQUE (mac);