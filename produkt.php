<?php 
	session_start();

	// if(!isset($_SESSION['logged']))
	// {
	// 	header('Location: index.php');
	// 	exit();
	// }
	

	function infoProduct($produkt)
	{
		require "connectDB.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		try
		{
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);

			if($polaczenie->connect_errno !=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				$query = "SELECT * FROM produkty INNER JOIN obrazy ON produkty.idProdukty = obrazy.idProdukty WHERE produkty.idProdukty='$produkt'";
				
				$setUTF = "SET NAMES utf8";
				$polaczenie -> query($setUTF);
		
				if ($rezultat_produkty = $polaczenie->query($query)) {

					if (!$rezultat_produkty) throw new Exception($polaczenie -> error);
				    /* fetch associative array */
				    while ($row = $rezultat_produkty->fetch_assoc()) {
				        $produkt = $row;
				    }
				    /* free result set */
				    $rezultat_produkty->free();
				}
				
				$polaczenie -> close();

				return $produkt;
			}
		}
		catch(Exception $error)
		{
				echo'<div class = "error">Błąd serwera!</div> <br />';
				echo $error;
		}
	}

	function infoSmakProduct($produkt)
	{
		require "connectDB.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		try
		{
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);

			if($polaczenie->connect_errno !=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				$query = "SELECT smaki.*, produkty_has_smaki.idSmaki_produktow FROM smaki, produkty_has_smaki WHERE smaki.idSmaki = produkty_has_smaki.idSmaki AND produkty_has_smaki.idProdukty IN ($produkt)";
				

				$setUTF = "SET NAMES utf8";
				$polaczenie -> query($setUTF);
				//$rezultat = $polaczenie -> query($query);
			
				if ($rezultat_smak_produkty = $polaczenie->query($query)) {

					if (!$rezultat_smak_produkty) throw new Exception($polaczenie -> error);
				    /* fetch associative array */
				    while ($row = $rezultat_smak_produkty->fetch_assoc()) {
				        $wiersze_smak_produkty[] = $row;
				    }

				    /* free result set */
				    $rezultat_smak_produkty->free();
				}
				
				$polaczenie -> close();

				return $wiersze_smak_produkty;
			}
		}
		catch(Exception $error)
		{
				echo'<div class = "error">Błąd serwera!</div> <br />';
				echo $error;
		}
	}


	if(isset($_POST["type"]) && $_POST["type"]=='add')
	{

		foreach($_POST as $key => $zamowienie)
		{
			$idProdukt[$key]= $zamowienie; 
		}
		unset($idProdukt['type']);
		//Array ( [idProdukty] => 1 [type] => add )
		$produkt = infoProduct($idProdukt['idProdukty']);
		$smak_produktu = infoSmakProduct($idProdukt['idProdukty']);
	}

	

?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<title>Sklep internetowy - zamówienie</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">

	<script type="text/javascript">
	function input(cbox) {
	  if (cbox.checked) {
	    var input = document.createElement("input");
	    input.type = "number";
	    input.name = cbox.name;
	    var div = document.createElement("div");
	    div.id = cbox.name;
	    div.innerHTML = "Podaj ilość " + cbox.name;
	    div.appendChild(input);
	    document.getElementById("insertinputs").appendChild(div);
	  } else {
	    document.getElementById(cbox.name).remove();
	  }
	}
</script>
</head>

<body>
<h1>Sklep suplementów diety</h1>
<?php
print_r($smak_produktu);
	if((isset($_SESSION['logged']) && ($_SESSION['logged'] == true)))
	{
		echo "<p>Witaj ".$_SESSION['user']."!! [<a href='logout.php'>Wyloguj się</a>]</p>";
		echo "<p>[<a href='profile_edit.php'>Edytuj swoje konto</a>]</p>";
		echo "<br />";
	}
	if(!isset($_SESSION['logged']))
	{
		echo '<a href="register.php">[Rejestracja]</a>';
		echo '<a href="logowanie.php">[Logowanie]</a>';
	}

	echo "<p>[<a href='index.php'>Powrót do katalogu</a>]</p><br /><br />";
	echo '<form method="POST" action="koszyk.php" accept-charset="utf-8">';
	echo '<img src="photo/'.$produkt["url"].'">';
	echo "<br /><br />";
	echo "<p><b>".$produkt["nazwa"]."</b></p>";
	echo "<p>".$produkt["opis"]."</p>";
	echo '<input type="hidden" name="produkt" value="'.$produkt["nazwa"].'" />';
	echo '<input type="hidden" name="type" value="add" />';
	
	echo '<label><span>Smak</span><select name="smak_produktu">';
	foreach ($smak_produktu as $key => $value) 
	{
		echo '<option value="'.$smak_produktu[$key]["nazwa"].'&&'.$smak_produktu[$key]["idSmaki_produktow"].'">'.$smak_produktu[$key]["nazwa"].'</option>';		
	}
	echo '</select></label><br />';
	echo '<input type="number" name="ilosc" value="1" /><br />';

	// <label>
	// 	<span>Color</span>
	// 	<select name="product_color">
	// 	<option value="Black">Black</option>
	// 	<option value="Silver">Silver</option>
	// 	</select>
	// </label>
	
	//echo "<p>Ilość produktu: ".$ilosc_smakow."</p>";
	echo '<input type="submit" name="order" value="Zamów"/>';
	echo '</form>';
	
?>



</body>
</html>