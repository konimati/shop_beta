<?php 
	session_start();
	$wszystko_ok = false;
	if(!isset($_SESSION['logged']))
	{
		header('Location: index.php');
		exit();
	}

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
			$setUTF = "SET NAMES utf8";
			$polaczenie -> query($setUTF);

			$user = $_SESSION['user'];
			$idUser = $_SESSION['id'];
			
			$query = "SELECT * FROM adresy WHERE idUser='$idUser'";
			$rezultat = $polaczenie -> query($query);

			if (!$rezultat) throw new Exception($polaczenie -> error);

			$wiersz = $rezultat->fetch_assoc();

			$_SESSION['imie'] = $wiersz['imie'];
			$_SESSION['nazwisko'] = $wiersz['nazwisko'];
			$_SESSION['ulica'] = $wiersz['ulica'];
			$_SESSION['miejscowosc'] = $wiersz['miejscowosc'];
			$_SESSION['nr_domu'] = $wiersz['nr_domu'];
			$_SESSION['nr_lokalu'] = $wiersz['nr_lokalu'];
			$_SESSION['kod_pocztowy'] = $wiersz['kod_pocztowy'];

			if(!empty($_SESSION['imie']))
			{
				$wszystko_ok = true;
			}
			
			if(isset($_POST['imie']))
			{
				$imie = $_POST['imie'];
				$nazwisko = $_POST['nazwisko'];
				$ulica = $_POST['ulica'];
				$miejscowosc = $_POST['miejscowosc'];
				$nr_domu = $_POST['nr_domu'];
				$nr_lokalu = $_POST['nr_lokalu'];
				$kod_pocztowy = $_POST['kod_pocztowy'];
				
				if(empty($_SESSION['imie']))
				{
					$query_adresy = "INSERT INTO adresy VALUES (NULL, '$imie','$nazwisko', '$miejscowosc', '$ulica', '$nr_domu', '$nr_lokalu', '$kod_pocztowy', '$idUser')";

					$rezultat = $polaczenie -> query($query_adresy);
					
					if (!$rezultat) throw new Exception($polaczenie -> error);
					$wszystko_ok = true;
					header('Location: address.php');
				}
				else
				{
					$query = "UPDATE adresy SET imie='$imie', nazwisko='$nazwisko', ulica='$ulica', miejscowosc='$miejscowosc', nr_domu='$nr_domu', nr_lokalu='$nr_lokalu', kod_pocztowy='$kod_pocztowy' WHERE idUser='$idUser'";
					//print_r($_SESSION); exit();
					if($polaczenie -> query($query))
					{
						$wszystko_ok = true;
						header('Location: address.php');
					}
					else
					{
						throw new Exception($polaczenie -> error);
					}
				}
				
						
				
			}
			$polaczenie -> close();
		}
	}
	catch(Exception $error)
	{
			echo'<div class = "error">Błąd serwera!</div> <br />';
			echo $error;
	}

?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<title>Sklep internetowy</title>
</head>

<body>
<?php
	echo "<p>[<a href='koszyk.php'>Powrót do koszyka</a>]</p><br /><br />";
?>
<h1>Adres do wysyłki</h1>
<form method="POST" accept-charset="utf-8">

<!-- E-mail: <br /><input type="email" name="email" value="
 if (isset($_SESSION['email'])) echo $_SESSION['email'];
?>"/><br /> -->
Imie: <br /><input type="text" name="imie" value="
<?php if (isset($_SESSION['imie'])) echo $_SESSION['imie'];
?>"/><br />
Nazwisko: <br /><input type="text" name="nazwisko" value="
<?php if (isset($_SESSION['nazwisko'])) echo $_SESSION['nazwisko'];
?>"/><br />
Ulica: <br /><input type="text" name="ulica" value="
<?php if (isset($_SESSION['ulica'])) echo $_SESSION['ulica'];
?>"/><br />
Miejscowość: <br /><input type="text" name="miejscowosc" value="
<?php if (isset($_SESSION['miejscowosc'])) echo $_SESSION['miejscowosc'];
?>"/><br />
Nr domu: <br /><input type="text" name="nr_domu" value="
<?php if (isset($_SESSION['nr_domu'])) echo $_SESSION['nr_domu'];
?>"/><br />
Nr lokalu: <br /><input type="text" name="nr_lokalu" value="
<?php if (isset($_SESSION['nr_lokalu'])) echo $_SESSION['nr_lokalu'];
?>"/><br />
Kod pocztowy: <br /><input type="text" name="kod_pocztowy" value="
<?php if (isset($_SESSION['kod_pocztowy'])) echo $_SESSION['kod_pocztowy'];
?>"/><br />
<br /><br />
<?php
if($wszystko_ok == false)
{
	echo '<input type="submit" name="" value="Dodaj podany adres"/>';
}
else
	echo "<p>[<a href='sending.php'>Przejdź dalej</a>]</p>";
?>

</form>
</body>
</html>

