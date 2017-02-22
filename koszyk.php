<?php 
	session_start();

	// if(!isset($_SESSION['logged']))
	// {
	// 	header('Location: index.php');
	// 	exit();
	// }
	
	if(isset($_POST['produkt']) && $_POST['type'] == 'add' && $_POST['ilosc'] > 0)
	{	

		$smak_i_id = $_POST['smak_produktu'];
		$smak = explode("&&", $smak_i_id);
		print_r($smak);
		$ilosc = $_POST['ilosc'];
		
		$tab_smakow = array('smak' => $smak[0], 'ilosc' => $ilosc, 'idSmaki_produktow' => $smak[1]);
		print_r($tab_smakow);
		$result = array_merge(infoProdukt($_POST['produkt']), $tab_smakow);
	
		if (isset($_SESSION["produkt"]))
		{
			$found = false;
			foreach ($_SESSION["produkt"] as $key) 
			{
				if(($key['nazwa'] == $result['nazwa']) and ($key['smak'] == $result['smak']))
				{
					$key['ilosc'] += 1;
					$found = true;
				}

			}

			if ($found == false) 
			{
				$_SESSION["produkt"][] = $result;
			}
			else
			{
				foreach ($_SESSION["produkt"] as $key => $value) 
				{
					if(($_SESSION["produkt"][$key]['nazwa'] == $result['nazwa']) and ($_SESSION["produkt"][$key]['smak'] == $result['smak']))
		 			{	

						$_SESSION["produkt"][$key]['ilosc'] += $result['ilosc'];
					}
				}
			}

		} 
		else
		{
			$_SESSION["produkt"][] = $result;
		}
		
		unset($_POST);
	}
	

	function infoProdukt($produkt)
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
				
								
				$query = 'SELECT produkty.nazwa, obrazy.url, produkty.cena FROM obrazy, produkty WHERE produkty.idProdukty = obrazy.idProdukty AND produkty.nazwa="'.$produkt.'"';

				$setUTF = "SET NAMES utf8";

				$polaczenie -> query($setUTF);
				//$rezultat = $polaczenie -> query($query);
			
				if ($rezultat = $polaczenie->query($query)) {

					if (!$rezultat) throw new Exception($polaczenie -> error);
				    /* fetch associative array */
				    while ($row = $rezultat->fetch_assoc()) {
				        $produkt = $row;
				    }

				    /* free result set */
				    $rezultat->free();
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

	if (isset($_SESSION["produkt"]))
	{
		if(isset($_POST['type']) && $_POST['type'] == 'remove')
		{
			foreach ($_SESSION["produkt"] as $key => $value) 
			{
				if(($_SESSION["produkt"][$key]['nazwa'] == $_POST['remove_name']) and ($_SESSION["produkt"][$key]['smak'] == $_POST['remove_smak']))
			 	{	
					unset($_SESSION["produkt"][$key]);
				}
			}
		}
	}
		


?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<title>Sklep internetowy - zamówienie</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	
</head>

<body>
<h1>Sklep suplementów diety</h1>
<form method="POST" accept-charset="utf-8">
<?php
//unset($_SESSION["produkt"]);

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
	echo "<p>[<a href='index.php'>Dodaj produkt</a>]</p>";
	//echo "<p>[<a href='katalog.php'>Reset</a>]</p>";
	echo "<br /><br />";
	$suma = 0;
	if(isset($_SESSION['produkt']) && !empty($_SESSION['produkt']))
	{
		echo '<table border="1" width="100%">';

		foreach ($_SESSION["produkt"] as $key => $value) 
		{
			echo '<form method="POST" action="koszyk.php" accept-charset="utf-8">';
			echo '<input type="hidden" name="type" value="remove" />';
			echo '<input type="hidden" name="remove_name" value="'.$_SESSION["produkt"][$key]["nazwa"].'" />';
			echo '<input type="hidden" name="remove_smak" value="'.$_SESSION["produkt"][$key]["smak"].'" />';
			echo '<tr>
				<td><img src="photo/'.$_SESSION["produkt"][$key]["url"].'"width="100" height="100"></td>	
				<td><p>
				<b>'.$_SESSION["produkt"][$key]["nazwa"].'<br />Smak: '.$_SESSION["produkt"][$key]["smak"].'</b>
				<br /><button type="submit">Usuń</button></div>
				</p></td>
			</tr>
			<tr>
				<td>Ilość: '.$_SESSION["produkt"][$key]["ilosc"].' szt</p></td>	
				<td><p>'.$_SESSION["produkt"][$key]["cena"].' zł/szt</p>
				<p>Cena: '.$_SESSION["produkt"][$key]["cena"]*$_SESSION["produkt"][$key]["ilosc"].' zł</p></td>
			</tr>';
			
			echo '</form>';
			$suma += $_SESSION["produkt"][$key]["cena"]*$_SESSION["produkt"][$key]["ilosc"];
		}
		$_SESSION["suma"] = $suma;
		echo '<tr><b>Suma: '.$_SESSION["suma"].'</b></tr>';
		echo '</table>';
		if((isset($_SESSION['logged']) && ($_SESSION['logged'] == true)))
		{
			echo "<p>[<a href='address.php'>Przejdź dalej</a>]</p>";
		}
		else
		{
			if(!empty($_SESSION['produkt']))
			{
				echo "<p>[<a href='logowanie.php'>Przejdź dalej</a>]</p>";
			}
		}
		
	}
	if(empty($_SESSION['produkt']))
	{
		echo 'Brak produktów w koszyku';
	}
	
	
?>

</form>

</body>
</html>