<?php 
	session_start();
//print_r($_SESSION["produkt"]);
	if((!isset($_SESSION['produkt']) or empty($_SESSION['produkt'])) or ((!isset($_SESSION['logged']) or ($_SESSION['logged'] == false))))
	{
		header('Location: index.php');
		exit();
	}
	 
	if(isset($_POST['przedplata']))
	{
		if(!empty($_POST['przedplata'])) 
		{
			$przedplata = $_POST['przedplata'];
		}
	}
	if(isset($_POST['pobranie']))
	{
		if(!empty($_POST['pobranie'])) 
		{
			$pobranie = $_POST['pobranie'];
		}
	}
	if(isset($przedplata) && !empty($przedplata))
	{	
		$sposob ='przedplata';
		$dostawca = pobranieDostawcy($przedplata, $sposob);	
		unset($_POST);
	}
	if(isset($pobranie) && !empty($pobranie))
	{	
		$sposob ='pobranie';
		$dostawca = pobranieDostawcy($pobranie, $sposob);
		unset($_POST);
	}

	//print_r($dostawca); exit();

	function pobranieDostawcy($dostawca, $sposob)
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
				$zapytanie_produkty = "SELECT nazwa, cena_$sposob, idPost FROM post WHERE nazwa='$dostawca'";
				
				$setUTF = "SET NAMES utf8";
				$polaczenie -> query($setUTF);

				if ($rezultat_produkty = $polaczenie->query($zapytanie_produkty)) {

					if (!$rezultat_produkty) throw new Exception($polaczenie -> error);
				    /* fetch associative array */
				    while ($row = $rezultat_produkty->fetch_assoc()) {
				        $wiersze_produkty = $row;
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
	<title>Sklep internetowy - zamówienie</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	
</head>

<body>
<h1>Sklep suplementów diety</h1>
<form method="POST" action="order.php" accept-charset="utf-8">
<?php
	if(isset($dostawca))
	{
		foreach ($dostawca as $key => $value) 
		{
			$przesylka[] = $value;
		}
	}
	$_SESSION["suma_zamówienia"] = $_SESSION["suma"]+$przesylka[1];


	if(isset($przesylka) && !empty($przesylka) && !empty($_SESSION["produkt"]))
	{	
		echo '<table border="1" width="100%">';
		foreach ($_SESSION["produkt"] as $key => $value) 
		{	
			echo '<tr>
				<td><img src="photo/'.$_SESSION["produkt"][$key]["url"].'"width="100" height="100"></td>	
				<td><p>
				<b>'.$_SESSION["produkt"][$key]["nazwa"].'<br />Smak: '.$_SESSION["produkt"][$key]["smak"].'</b>
				</p></td>
			</tr>
			<tr>
				<td>Ilość: '.$_SESSION["produkt"][$key]["ilosc"].' szt</p>
				
				</td>	
				<td><p>Cena: '.$_SESSION["produkt"][$key]["cena"]*$_SESSION["produkt"][$key]["ilosc"].' zł</p>
				
				</td>
			</tr>';
		}
		echo '</table>';
		echo 'Adres wysyłki:<br />';
		echo 'Imie: '.$_SESSION['imie'].'<br />';
		echo 'Nazwisko: '.$_SESSION['nazwisko'].'<br />';
		echo 'Ulica: '.$_SESSION['ulica'].'<br />';
		echo 'Miejscowosc: '.$_SESSION['miejscowosc'].'<br />';
		echo 'Nr_domu: '.$_SESSION['nr_domu'].'<br />';
		echo 'Nr lokalu: '.$_SESSION['nr_lokalu'].'<br />';
		echo 'Kod pocztowy: '.$_SESSION['kod_pocztowy'].'<br />';
		echo '<p>Przesyłka: '.$przesylka[0].' koszt: '.$przesylka[1].'zł</p>';
		echo '<p><b>Suma zamówienia: '.$_SESSION["suma_zamówienia"].'</b></p>';
		echo '<input type="hidden" name="zamowienie" value="nowe" />';
		echo '<input type="hidden" name="idPost" value="'.$przesylka[2].'" />';

	}
	echo '<button type="submit">Zamów</button>';
	echo "<p>[<a href='sending.php'>Powrót</a>]</p>";
	
?>

</form>

</body>
</html>