#
# Structure de la table `dhcp`
#

CREATE TABLE IF NOT EXISTS se3_dhcp(
  id INT NOT NULL AUTO_INCREMENT,
  ip varchar(15) NOT NULL,
  mac varchar(17) NOT NULL,
  name varchar(255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY ip (ip),
  UNIQUE KEY mac (mac)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;