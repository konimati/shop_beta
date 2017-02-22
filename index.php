<?php 
	session_start();
	
	//$_SESSION['nowy_produkt'] = 0;
	function pobranieProdukty()
	{
		require_once "connectDB.php";
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
				$zapytanie_produkty = "SELECT produkty.idProdukty, nazwa, url, cena FROM produkty INNER JOIN obrazy ON produkty.idProdukty = obrazy.idProdukty";
				
				$setUTF = "SET NAMES utf8";
				$polaczenie -> query($setUTF);

				if ($rezultat_produkty = $polaczenie->query($zapytanie_produkty)) {

					if (!$rezultat_produkty) throw new Exception($polaczenie -> error);
				    /* fetch associative array */
				    while ($row = $rezultat_produkty->fetch_assoc()) {
				        $wiersze_produkty[] = $row;
				    }
				    if(empty($wiersze_produkty))
				    {
				    	throw new Exception($polaczenie -> error);
				    	exit();
				    }
				    /* free result set */
				    $rezultat_produkty->free();
				}
				
				$polaczenie -> close();

				return $wiersze_produkty;
			}
		}
		catch(Exception $error)
		{
				echo'<div class = "error">Błąd serwera!</div> <br />';
				echo $error;
		}
	}
	

?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<title>Sklep internetowy</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
<h1>Sklep suplementów diety</h1>
<?php  
	if(!isset($_SESSION['logged']))
	{
		echo '<a href="register.php">[Rejestracja]</a>';
		echo '<a href="logowanie.php">[Logowanie]</a><br /><br />';
	}

	if((isset($_SESSION['logged']) && ($_SESSION['logged'] == true)))
	{
		echo "<p>Witaj ".$_SESSION['user']."!! [<a href='logout.php'>Wyloguj się</a>]</p>";
		echo "<p>[<a href='profile_edit.php'>Edytuj swoje konto</a>]</p>";
		echo "<br />";
	}
	$produkty = pobranieProdukty();
	if(empty($produkty))
	{
		exit();
	}
	
	//unset($_SESSION['produkt']);
	foreach ($produkty as $key => $value) 
	{
		echo '<form method="POST" action="produkt.php" accept-charset="utf-8">';
		echo "<fieldset>";
		echo '<img src="photo/'.$produkty[$key]["url"].'">';
		echo "<br /><br />";
		echo "<p><b>".$produkty[$key]["nazwa"]."</b></p>";
		echo "<p>Cena: ".$produkty[$key]["cena"]." zł/szt</p>";
		echo '<input type="hidden" name="idProdukty" value="'.$produkty[$key]["idProdukty"].'" />';
		echo '<input type="hidden" name="type" value="add" />';
		echo '<button type="submit">Wybierz</button></div>';
		echo '</form>';
		echo "</fieldset>";
		echo "<br /><br />";
	}
	
?>

</form>

</body>
</html>