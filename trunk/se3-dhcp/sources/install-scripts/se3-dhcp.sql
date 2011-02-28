#
# Structure de la table `dhcp`
#

CREATE TABLE IF NOT EXISTS se3_dhcp(
  id	INT	AUTO_INCREMENT,
  ip	text	NOT NULL,
  mac	text	NOT NULL,
  name	text	NOT NULL,
  PRIMARY KEY (id)
);

