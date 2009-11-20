<?php

/**

   * Page destinee a effectuer un readressage IP d'apres un CSV de l'adressage actuel
   * @Version $Id$

   * @Projet LCS / SambaEdu

   * @auteurs Stephane Boireau


   * @note

   * @Licence Distribue sous la licence GPL

*/

/**
   * @Repertoire: dhcp

   * file: readressage_ip.php
*/

	$step=isset($_POST['step']) ? $_POST['step'] : NULL;
	//$tri_nom_netbios=isset($_POST['tri_nom_netbios']) ? $_POST['tri_nom_netbios'] : "n";
	$tri_machines=isset($_POST['tri_machines']) ? $_POST['tri_machines'] : "";

	if($step==2) {
		$ip=isset($_POST['ip']) ? $_POST['ip'] : array();
		$t_0=isset($_POST['t_0']) ? $_POST['t_0'] : array();
		$t_1=isset($_POST['t_1']) ? $_POST['t_1'] : array();
		$t_2=isset($_POST['t_2']) ? $_POST['t_2'] : array();

		$nom_fic="se3_dhcp_".preg_replace("/ /","_",time());
		$nom_fic.=".csv";

		$now=gmdate('D, d M Y H:i:s').' GMT';
		header('Content-Type: text/x-csv');
		header('Expires: ' . $now);
		// lem9 & loic1: IE need specific headers
		if(preg('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) {
			header('Content-Disposition: inline; filename="'.$nom_fic.'"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		}
		else {
			header('Content-Disposition: attachment; filename="'.$nom_fic.'"');
			header('Pragma: no-cache');
		}

		// Initialisation du contenu du fichier:
		$fd='';

		for($i=0;$i<count($ip);$i++) {
			if($t_0[$i]!="") {
				$fd.=$ip[$i].";".$t_0[$i].";".$t_2[$i]."\n";
			}
		}

		// On renvoye le fichier vers le navigateur:
		echo $fd;
		die();
	}

	// Pour avoir la barre bleu en haut
	include "entete.inc.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title>Re-adressage IP</title>
	<style type="text/css">
		table.my_table {
			border-style: solid;
			border-width: 1px;
			border-color: black;
			border-collapse: collapse;
		}

		.my_table th {
			border-style: solid;
			border-width: 1px;
			border-color: black;
			background-color: khaki;
			font-weight:bold;
			text-align:center;
			vertical-align: middle;
		}

		.my_table td {
			text-align:center;
			vertical-align: middle;
			border-style: solid;
			border-width: 1px;
			border-color: black;
		}

		.my_table .lig1 {
			background-color: #C8FFC8;
		}
		.my_table .lig-1 {
			background-color: #FFE6AA;
		}

		.my_table tr:hover {
			background-color:white;
		}

		div#fixe   {
			position: fixed;
			bottom: 5%;
			right: 5%;
		}

	</style>
</head>
<body>
	<h1>Re-adressage IP</h1>

	<?php

		if(!isset($step)) {
			echo "<form name='import_fichier' action='".$_SERVER['PHP_SELF']."#premiere_ip' enctype='multipart/form-data' method='post'>\n";
			echo "<p>Adresse reseau: <input type='text' name='ip' value='172.21.1.0' /><br />\n";
			echo "Nombre de classes de 256 machines: <input type='text' name='nb_classes' value='4' /><br />\n";
			echo "Laisser <input type='text' name='nb_ip_libres' value='241' /> IP libres en debut de liste (*)<br />\n";
			//echo "Trier les machines ordre alphabétique: <input type='checkbox' name='tri_nom_netbios' value='y' />\n";
			echo "<input type='radio' name='tri_machines' value='' id='pas_de_tri_machines' checked /><label for='pas_de_tri_machines'> Ne pas trier les machines  (<i>prendre l'ordre du fichier fourni</i>),</label><br />\n";
			echo "<input type='radio' name='tri_machines' id='tri_machines_netbios' value='netbios' /><label for='tri_machines_netbios'> Trier les machines ordre alphabétique,</label><br />\n";
			echo "<input type='radio' name='tri_machines' id='tri_machines_ip' value='ip' /><label for='tri_machines_ip'> Trier les machines selon les adresses IP actuelles.</label><br />\n";
			echo "<input type='hidden' name='step' value='1' /></p>\n";
			echo "<p>Veuillez fournir le fichier CSV des noms de machines et adresses MAC:<br />\n";
			echo "<input type='file' name='file_name' />&nbsp;\n";
			echo "<input type='submit' name='valid' value='Importer' /></p>\n";
			echo "</form>\n";

			echo "<p>(*) pour par exemple r&eacute;server la premi&egrave;re classe C aux serveurs, imprimantes sur ip, vid&eacute;oprojecteurs sur ip,...<br />Les 15 premi&egrave;res adresses sont de toutes fa&ccedil;ons r&eacute;serv&eacute;es.</p>\n";
		}
		elseif($step==1) {
			$csv_file = isset($_FILES["file_name"]) ? $_FILES["file_name"] : NULL;

			if(!is_uploaded_file($csv_file['tmp_name'])) {
				echo "<p>Erreur 1</p>\n";
			}
			else {
				$source_file=$csv_file['tmp_name'];
				$dest_file=$source_file;

				$fp=fopen($dest_file,"r");
				if(!$fp){
					echo "<p>Erreur 2</p>\n";
				}
				else {
					$nb_ip_libres=isset($_POST['nb_ip_libres']) ? $_POST['nb_ip_libres'] : 0;

					$tab=array();
					if($tri_machines=='') {
						$cpt=$nb_ip_libres;
						while(!feof($fp)){
							$ligne=trim(fgets($fp,4096));
							if($ligne!="") {
								$tab_tmp=explode(";", $ligne);

								// On modifie l'ordre des champs pour trier par la suite sur le premier champ: NOM_NETBIOS
								$tab[$cpt][0]=strtoupper($tab_tmp[1]);
								$tab[$cpt][1]=$tab_tmp[0];
								$tab[$cpt][2]=$tab_tmp[2];
								$cpt++;
							}
						}
						$tab2=array();
						$tab2=$tab;
					}
					elseif($tri_machines=='netbios') {
						$cpt=0;
						while(!feof($fp)){
							$ligne=trim(fgets($fp,4096));
							if($ligne!="") {
								$tab_tmp=explode(";", $ligne);

								// On modifie l'ordre des champs pour trier par la suite sur le premier champ: NOM_NETBIOS
								$tab[$cpt][0]=strtoupper($tab_tmp[1]);
								$tab[$cpt][1]=$tab_tmp[0];
								$tab[$cpt][2]=$tab_tmp[2];
								$cpt++;
							}
						}

						// On trie par nom de machine
						/*
						$tab2=array();
						$tab2=$tab;
						sort($tab2);
						*/
						sort($tab);
						$tab2=array();
						for($loop=0;$loop<$nb_ip_libres;$loop++) {$tab2[]="";}
						for($loop=0;$loop<count($tab);$loop++) {$tab2[]=$tab[$loop];}

					}
					elseif($tri_machines=='ip') {
						$cpt=0;
						$tab_num=array();
						while(!feof($fp)){
							$ligne=trim(fgets($fp,4096));
							if($ligne!="") {
								$tab_tmp=explode(";", $ligne);

								$tab_tmp2=explode(".", $tab_tmp[0]);

								$tab_num[$cpt]=$tab_tmp2[0]*256*256*256+$tab_tmp2[1]*256*256+$tab_tmp2[2]*256+$tab_tmp2[3];

								$tab[$cpt][0]=$tab_tmp2[0]*256*256*256+$tab_tmp2[1]*256*256+$tab_tmp2[2]*256+$tab_tmp2[3];
								$tab[$cpt][1]=strtoupper($tab_tmp[1]);
								$tab[$cpt][2]=$tab_tmp[2];
								$tab[$cpt][3]=$tab_tmp[0];

								/*
								echo "<p>";
								echo "\$tab[$cpt][0]=".$tab[$cpt][0]."<br />";
								echo "\$tab[$cpt][1]=".$tab[$cpt][1]."<br />";
								echo "\$tab[$cpt][2]=".$tab[$cpt][2]."<br />";
								echo "\$tab[$cpt][3]=".$tab[$cpt][3]."</p>";
								*/

								$cpt++;
							}
						}

						// On trie par adresse IP
						//natsort($tab);

						/*
						echo "<table><tr><td>";
						foreach($tab_num as $key => $value) {
							echo "\$tab_num[$key]=".$value."<br />";
						}
						echo "</td><td>";
						*/

						$tab_num2=$tab_num;
						sort($tab_num2);
						/*
						foreach($tab_num2 as $key => $value) {
							echo "\$tab_num2[$key]=".$value."<br />";
						}
						echo "</td></tr></table>";
						*/

						$tab_num3=array_flip($tab_num);
						/*
						foreach($tab_num3 as $key => $value) {
							echo "\$tab_num3[$key]=".$value."<br />";
						}
						*/

						$tab2=array();
						for($loop=0;$loop<$nb_ip_libres;$loop++) {$tab2[]="";}
						$cpt=$nb_ip_libres;
						foreach($tab_num2 as $key => $value) {
							$tab2[$cpt]=array();
							$tab2[$cpt][0]=$tab[$tab_num3[$value]][1];
							$tab2[$cpt][1]=$tab[$tab_num3[$value]][3];
							$tab2[$cpt][2]=$tab[$tab_num3[$value]][2];

							/*
							echo "<p>";
							echo "\$tab2[$cpt][0]=".$tab2[$cpt][0]."<br />";
							echo "\$tab2[$cpt][1]=".$tab2[$cpt][1]."<br />";
							echo "\$tab2[$cpt][2]=".$tab2[$cpt][2]."</p>";
							*/

							$cpt++;
						}

					}
				}
				fclose($fp);

				/*
				echo "<table border='1'>\n";
				for($i=0;$i<count($tab);$i++) {
					echo "<tr><td>".$tab[$i][0]."</td><td>".$tab[$i][1]."</td><td>".$tab[$i][2]."</td></tr>\n";
				}
				echo "</table>\n";
				echo "<p><br /></p>\n";
				*/


				function tip($ip) {
					$tab=explode(".",$ip);
					$tip=256*256*256*$tab[0]+256*256*$tab[1]+256*$tab[2]+$tab[3];
					return $tip;
				}

				function ip($tip) {
					$ip1=floor($tip/(256*256*256));
					$ip2=floor(($tip-$ip1*256*256*256)/(256*256));
					$ip3=floor(($tip-$ip1*256*256*256-$ip2*256*256)/256);
					$ip4=$tip-$ip1*256*256*256-$ip2*256*256-$ip3*256;

					$ip="$ip1.$ip2.$ip3.$ip4";
					return $ip;
				}

				function dernier_octet($ip) {
					$tab=explode(".",$ip);
					return $tab[3];
				}

				echo "<script type='text/javascript'>
				function inserer(num,dernier){

					//alert('document.getElementById(\'td_\'+'+num+'+\'_0\').innerHTML='+document.getElementById('td_'+num+'_0').innerHTML)
					//alert('document.getElementById(\'t_\'+'+num+'+\'_0\').value='+document.getElementById('t_'+num+'_0').value)

					temoin='n';

					for(i=dernier-1;i>num;i--){

						//document.getElementById('compteur').innerHTML=i;

						j=i-1;
						if(((document.getElementById('t_'+j+'_0'))&&(document.getElementById('t_'+j+'_0').value!=''))||(temoin=='y')) {
							temoin='y';
							if(document.getElementById('td_'+i+'_0')) {
								document.getElementById('td_'+i+'_0').innerHTML=document.getElementById('t_'+j+'_0').value;
								document.getElementById('t_'+i+'_0').value=document.getElementById('t_'+j+'_0').value;

								document.getElementById('td_'+i+'_1').innerHTML=document.getElementById('t_'+j+'_1').value;
								document.getElementById('t_'+i+'_1').value=document.getElementById('t_'+j+'_1').value;

								document.getElementById('td_'+i+'_2').innerHTML=document.getElementById('t_'+j+'_2').value;
								document.getElementById('t_'+i+'_2').value=document.getElementById('t_'+j+'_2').value;
							}
						}
					}

					if(document.getElementById('td_'+num+'_0')) {
						document.getElementById('td_'+num+'_0').innerHTML='&nbsp;';
						document.getElementById('t_'+num+'_0').value='';
						document.getElementById('td_'+num+'_1').innerHTML='&nbsp;';
						document.getElementById('t_'+num+'_1').value='';
						document.getElementById('td_'+num+'_2').innerHTML='&nbsp;';
						document.getElementById('t_'+num+'_2').value='';
					}

				}

				function supprimer(num,dernier){
					for(i=num;i<dernier-1;i++){
						j=i+1;

						document.getElementById('td_'+i+'_0').innerHTML=document.getElementById('t_'+j+'_0').value;
						document.getElementById('t_'+i+'_0').value=document.getElementById('t_'+j+'_0').value;

						document.getElementById('td_'+i+'_1').innerHTML=document.getElementById('t_'+j+'_1').value;
						document.getElementById('t_'+i+'_1').value=document.getElementById('t_'+j+'_1').value;

						document.getElementById('td_'+i+'_2').innerHTML=document.getElementById('t_'+j+'_2').value;
						document.getElementById('t_'+i+'_2').value=document.getElementById('t_'+j+'_2').value;
					}
				}

				</script>\n";

				/*
				// On trie par nom de machine
				$tab2=$tab;
				if($tri_nom_netbios=='y') {
					sort($tab2);
				}
				*/

				$nb_classes=isset($_POST['nb_classes']) ? $_POST['nb_classes'] : 4;
				if($nb_classes=="") {$nb_classes=4;}
				elseif(strlen(preg_replace("/[0-9]/","",$nb_classes))!=0) {$nb_classes=4;}
				elseif($nb_classes==0) {$nb_classes=4;}

				// Nombre d'IP dispo... il faudra quand même exclure la dernière en 255 qui correspond au broadcast
				$total=$nb_classes*256-15;

				$ip=isset($_POST['ip']) ? $_POST['ip'] : "172.16.1.0";
				// On commence à 15 pour garder de l'espace en début de classe IP pour les serveurs
				$ip=ip(tip($ip)+15);
				$tip=tip($ip);

				//echo "\$ip=$ip<br />";
				//echo "\$tip=$tip<br />";
				//die();

				//echo "<div id='compteur' style='float:right; width:5em; height:1em; border: 1px solid black; text-align:center;'>\n";
				//echo "</div>\n";

				echo "<p>Effectuez le re-adressage en pr&eacute;voyant quelques IP libres entre les machines de differentes salles.</p>";
				echo "<p><i>NOTE:</i> Les liens javascript d'insertion/suppression sont un peu longs &agrave; r&eacute;agir lors du premier clic.<br />Mais cela devient plus fluide pour les clics suivants.</p>\n";

				echo "<div class='my_table'>\n";
				echo "<form name='import_fichier' action='".$_SERVER['PHP_SELF']."' enctype='multipart/form-data' method='post'>\n";
				echo "<input type='hidden' name='step' value='2' />\n";

				echo "<table class='my_table'>\n";
				echo "<tr><th>IP<br />future</th><th>Nom</th><th>IP<br />actuelle</th><th>MAC</th><th>Action</th></tr>\n";
				//for($i=0;$i<count($tab2);$i++) {
				$alt=1;
				for($i=0;$i<$total;$i++) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";

					echo "<td>\n";
					if($i==$nb_ip_libres) {echo "<a name='premiere_ip'></a>";}
					echo $ip;
					echo "<input type='hidden' id='ip_".$i."' name='ip[".$i."]' value='".$ip."' />\n";
					echo "</td>\n";

					if(isset($tab2[$i][0])) {
						echo "<td>"."<span id='td_".$i."_0'>".$tab2[$i][0]."</span>\n";
						echo "<input type='hidden' id='t_".$i."_0' name='t_0[".$i."]' value='".$tab2[$i][0]."' />\n";
						echo "</td>\n";
						echo "<td>"."<span id='td_".$i."_1'>".$tab2[$i][1]."</span>\n";
						echo "<input type='hidden' id='t_".$i."_1' name='t_1[".$i."]' value='".$tab2[$i][1]."' />\n";
						echo "</td>\n";
						echo "<td>"."<span id='td_".$i."_2'>".$tab2[$i][2]."</span>\n";
						echo "<input type='hidden' id='t_".$i."_2' name='t_2[".$i."]' value='".$tab2[$i][2]."' />\n";
						echo "</td>\n";
					}
					else {
						echo "<td>"."<span id='td_".$i."_0'>&nbsp;"."</span>\n";
						echo "<input type='hidden' id='t_".$i."_0' name='t_0[".$i."]' value='' />\n";
						echo "</td>\n";
						echo "<td>"."<span id='td_".$i."_1'>&nbsp;"."</span>\n";
						echo "<input type='hidden' id='t_".$i."_1' name='t_1[".$i."]' value='' />\n";
						echo "</td>\n";
						echo "<td>"."<span id='td_".$i."_2'>&nbsp;"."</span>\n";
						echo "<input type='hidden' id='t_".$i."_2' name='t_2[".$i."]' value='' />\n";
						echo "</td>\n";
					}

					echo "<td>\n";
					echo "<a href='#' onClick='inserer($i,$total) ;return false;'>Inserer</a> / <a href='#' onClick='supprimer($i,$total) ;return false;'>Supprimer</a>";
					echo "</td>\n";

					echo "</tr>\n";

					$ip=ip(tip($ip)+1);
					if(dernier_octet($ip)==0) {
						echo "<tr><td colspan='5' style='background-color:grey;'>&nbsp;</td></tr>\n";
					}
				}
				echo "</table>\n";

				echo "<input type='submit' name='export' value='Exporter' />\n";

				echo "<div id='fixe'>\n";
				echo "<input type='submit' name='export2' value='Exporter' />\n";
				echo "</div>\n";

				echo "</form>\n";
				echo "</div>\n";

			}

		}
		elseif($step==2) {

		}

	?>

</body>
</html>
