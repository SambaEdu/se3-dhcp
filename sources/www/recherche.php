<?php

include "entete.inc.php";
include "ldap.inc.php";
include "ihm.inc.php";
require_once "fonc_parc.inc.php";
require_once "dhcpd.inc.php";

if (is_admin("system_is_admin",$login)=="Y")
{
	//aide
	$_SESSION["pageaide"]="Le_module_DHCP#G.C3.A9rer_les_baux_et_r.C3.A9server_des_IPs";

	echo "<h1>Recherche de r&#233;servations</h1>";



// recherche sur le nom de machine
if (isset($_POST["search_name_id"]))
{
	switch($_POST["search_name_id"])
	{
		case 0:
		$search_name_id=0; break;
		case 1:
		$search_name_id=1; break;
		case 2:
		$search_name_id=2; break;
		case 3:
		$search_name_id=3; break;
		default:
		$search_name_id=0; break;
	}
}
else
{
	$search_name_id=0;
}

if (isset($_POST["search_name"]) and @$_POST["search_name"]!="")
{
	if ($search_name_id==1)
		$search_name_sql=$_POST["search_name"]."%";
	elseif ($search_name_id==2)
		$search_name_sql="%".$_POST["search_name"];
	elseif ($search_name_id==3)
		$search_name_sql="%".$_POST["search_name"]."%";
	else
		$search_name_sql=$_POST["search_name"];
	$search_name_aff=$_POST["search_name"];
}
else
{
	$search_name_sql="%";
	$search_name_aff="";
}

// recherche sur l'adresse ip
if (isset($_POST["search_ip_id"]))
{
	switch($_POST["search_ip_id"])
	{
		case 0:
		$search_ip_id=0; break;
		case 1:
		$search_ip_id=1; break;
		case 2:
		$search_ip_id=2; break;
		case 3:
		$search_ip_id=3; break;
		default:
		$search_ip_id=0; break;
	}
}
else
{
	$search_ip_id=0;
}

if (isset($_POST["search_ip"]) and @$_POST["search_ip"]!="")
{
	if ($search_ip_id==1)
		$search_ip_sql=$_POST["search_ip"]."%";
	elseif ($search_ip_id==2)
		$search_ip_sql="%".$_POST["search_ip"];
	elseif ($search_ip_id==3)
		$search_ip_sql="%".$_POST["search_ip"]."%";
	else
		$search_ip_sql=$_POST["search_ip"];
	$search_ip_aff=$_POST["search_ip"];
}
else
{
	$search_ip_sql="%";
	$search_ip_aff="";
}

// nombre de resultats
if (isset($_POST["search_start"]))
{
	$start=$_POST["search_start"]+0;
}
else
{
	$start=0;
}
if (isset($_POST["search_nbrows"]))
{
	$nbrows=$_POST["search_nbrows"]+0;
}
else
{
	$nbrows=10;
}

//recup liste ip imprimantes
require_once ("printers.inc.php");
$liste_imprimantes=search_imprimantes("printer-name=*","printers");
//$resultat=search_imprimantes("printer-name=$mpenc","printers");
for ($loopp=0; $loopp < count($liste_imprimantes); $loopp++)
{
	$printer_uri = $liste_imprimantes[$loopp]['printer-uri'];
	$printer_name = $liste_imprimantes[$loopp]['printer-name'];
	//echo "liste imp : $printer_name $printer_uri" ;
	continue;
}

$dhcp_link=connexion_db_dhcp();
$search_query = mysqli_prepare($dhcp_link, "SELECT ip, mac, name FROM `se3_dhcp` WHERE name LIKE ? AND ip LIKE ? ORDER BY INET_ATON(ip) ASC LIMIT $start ,$nbrows");
mysqli_stmt_bind_param($search_query, "ss", $search_name_sql, $search_ip_sql);
mysqli_stmt_execute($search_query);
mysqli_stmt_bind_result($search_query,$res_ip,$res_mac,$res_name);
mysqli_stmt_store_result($search_query);
$num_rows=mysqli_stmt_num_rows($search_query);
$content = "<form name=\"lease_form\" method=\"post\" action=\"\">\n";
$content .="<table border='1' width='90%'>";
$content .="<tr class=\"menuheader\"><td align=\"center\"><b>Adresse IP</b></td>";
$content .= "<td align=\"center\"><b>Adresse MAC</b></td>";
$content .="<td align=\"center\"><b>Nom NETBIOS</b></td>";
$content .="<td align=\"center\"><b>Parc(s)</b></td>";
$content .="<td align=\"center\"><b>Action</b></td>";
$content .= "</tr>\n";
if ($num_rows!=0)
{
	$clef=0;
	while (mysqli_stmt_fetch($search_query))
	{
		$listaction ="<OPTION value='none'>Action...</OPTION>\n";
		$listaction .= "<OPTION value=\"newip\">Changer l'adresse ip</OPTION>\n";
		$listaction .= "<OPTION value=\"supprimer\">Supprimer la reservation</OPTION>\n";
		$content.= "<tr>\n";
		$content.= "<td align='center'><input type=\"text\" maxlength=\"15\" SIZE=\"15\" value=\"".$res_ip."\"  name=\"ip[$clef]\"></td>\n";
		$content.= "<td align='center'><input type=\"text\" maxlength=\"17\" SIZE=\"17\" value=\"".strtolower($res_mac)."\"  name=\"mac[$clef]\" readonly></td>\n";
		$content.= "<td align='center'>";
		$content.= "<input type=\"text\" maxlength=\"20\" SIZE=\"20\" value=\"".$res_name."\"  name=\"name[$clef]\">";
		$content.= "<input type=\"hidden\" maxlength=\"20\" SIZE=\"20\" value=\"".$res_name."\"  name=\"oldname[$clef]\">";
		for ($loopp=0; $loopp < count($liste_imprimantes); $loopp++)
		{
			$printer_uri = $liste_imprimantes[$loopp]['printer-uri'];
			$printer_name = $liste_imprimantes[$loopp]['printer-name'];
			if (preg_match("/http/", $printer_uri) or preg_match("/socket/", $printer_uri))
			{
				if (preg_match("/$res_ip:/", $printer_uri))
				{
					$suisje_printer = "yes";
					break;
				}
				else
				{
					$suisje_printer = "no" ;
				}
			}
			elseif (preg_match("/lpd/", $printer_uri) or preg_match("/smb/", $printer_uri))
			{
				if (preg_match("#$res_ip/#", $printer_uri))
				{
					$suisje_printer = "yes";
					break;
				}
				else
				{
					$suisje_printer = "no" ;
				}
			}
			else
			{
				if (preg_match("/$res_ip$/", $printer_uri))
				{
					$suisje_printer = "yes";
					break;
				}
				else
				{
					$suisje_printer = "no" ;
				}
			}
		}
		if  ($suisje_printer=="yes")
		{
			$content.="<br><FONT color='blue'>Imprimante ".$printer_name."</FONT>\n"; 
		}
		$content.="</td>\n";
		$content.="<td align='center'>";
		// Est-ce que cette machine est enregistree ?
		$parc = search_parcs($res_name);
		if ((count(search_machines("(cn=" . $res_name . ")", "computers"))) > 0)
		{
			if (isset($parc))
			{
				foreach ($parc as $keys2 => $value2)
				{
					$content.="<a href=../parcs/show_parc.php?parc=" . $parc[$keys2]["cn"] . ">" . $parc[$keys2]["cn"] . "</a><br>\n";
				}
			}
			// est ce que la machine est integree au domaine ?
			if (count(search_machines("(uid=" . $res_name . "$)", "computers")) > 0)
			{
				$listaction .="<OPTION value=\"renommer\">Renommer un poste win &#224; distance</OPTION>\n";
				$listaction .="<OPTION value=\"renommer\">Renommer un poste linux &#224; distance</OPTION>\n";
				$listaction .="<OPTION value=\"reintegrer\">R&#233;int&#233;grer</OPTION>\n";
			}
			else
			{ // this computer is not recorded on the domain
				// une imprimante ?
				if  ($suisje_printer=="yes")
				{ 
					$listaction .="<OPTION value=\"renommer-base\">Renommer dans la base</OPTION>\n";
				}
				else
				{
					$content.="<br><FONT color='red'>Pas au domaine!</FONT>\n"; 
					$listaction .="<OPTION value=\"renommer-base\">Renommer dans la base</OPTION>\n";
					$listaction .="<OPTION value=\"renommer-linux\">Renommer un poste linux</OPTION>\n";
					$listaction .="<OPTION value=\"integrer\">Integrer un windows au domaine</OPTION>\n";
				}
			}
		}
		$content.="</td>";
		$content.="<td align='center'><select name='action_res[$clef]'>".$listaction."</select></td>";
		$content.="</tr>";
		$clef++;
	}
}
mysqli_stmt_close($search_query);
deconnexion_db_dhcp($dhcp_link);
$content .="<tr class=\"menuheader\"><td align=\"center\"><b>Adresse IP</b></td>";
$content .= "<td align=\"center\"><b>Adresse MAC</b></td>";
$content .="<td align=\"center\"><b>Nom NETBIOS</b></td>";
$content .="<td align=\"center\"><b>Parc(s)</b></td>";
$content .="<td align=\"center\"><b>Action</b></td>";
$content .= "</tr>\n";
$content .= "</table><br>\n";
$content .= "<input type='hidden' name='search_name_id' value='$search_name_id'>\n";
$content .= "<input type='hidden' name='search_name' value='$search_name_aff'>\n";
$content .= "<input type='hidden' name='search_ip_id' value='$search_ip_id'>\n";
$content .= "<input type='hidden' name='search_ip' value='$search_ip_aff'>\n";
$content .= "<input type='hidden' name='search_nbrows' value='$nbrows'>\n";
$content .= "<input type='hidden' name='action' value='valid'>\n";
$content .= "<input type=\"submit\" name=\"button\" value=\"" . gettext("Valider les modifications") . "\">\n";
$content .= "</form>";

?>

<form name="search_dhcp" method="post" action="">
<table border="1" align="center">
<tr>
	<td width="300">Nom de la machine</td>
	<td width="200" align='center'>
		<select name="search_name_id">
		<option value="0" <?php if ($search_name_id==0) echo "selected"; ?>>exactement</option>
		<option value="1" <?php if ($search_name_id==1) echo "selected"; ?>>commençant par</option>
		<option value="2" <?php if ($search_name_id==2) echo "selected"; ?>>finissant par</option>
		<option value="3" <?php if ($search_name_id==3) echo "selected"; ?>>contenant</option>
		</select>
	</td>
	<td width="200" align='center'><input type="text" maxlength="50" size="15" name="search_name" value="<?php echo $search_name_aff; ?>"></td>
</tr>
<tr>
	<td>Adresse IP</td>
	<td align='center'>
		<select name="search_ip_id">
		<option value="0" <?php if ($search_ip_id==0) echo "selected"; ?>>exactement</option>
		<option value="1" <?php if ($search_ip_id==1) echo "selected"; ?>>commençant par</option>
		<option value="2" <?php if ($search_ip_id==2) echo "selected"; ?>>finissant par</option>
		<option value="3" <?php if ($search_ip_id==3) echo "selected"; ?>>contenant</option>
		</select>
	</td>
	<td align='center'><input type="text" maxlength="50" size="15" name="search_ip" value="<?php echo $search_ip_aff; ?>"></td>
</tr>
<tr>
<tr>
	<td>Nombre de r&#233;sultats affich&#233;s</td>
	<td align='center' colspan='2'>
		<select name="search_nbrows">
		<option value="10" <?php if ($nbrows==10) echo "selected"; ?>>10</option>
		<option value="25" <?php if ($nbrows==25) echo "selected"; ?>>25</option>
		<option value="50" <?php if ($nbrows==50) echo "selected"; ?>>50</option>
		<option value="75" <?php if ($nbrows==75) echo "selected"; ?>>75</option>
		<option value="100" <?php if ($nbrows==100) echo "selected"; ?>>100</option>
		</select>
	</td>
</tr>
<tr>
<td colspan='3' align='center'>
<input type="submit" name="button" value="Valider">
</td>
</tr>
</table>
</form>

<?php

echo $content;

}
else
{
	echo ("Vous n'avez pas les droits n&#233;cessaires pour ouvrir cette page...");
}

// Footer
include ("pdp.inc.php");
?>