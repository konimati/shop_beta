<?php 

	session_start();

	if(isset($_POST['email']))
	{
		$data = date("Y-m-d H:i:s"); 
		//wysłany POST
		$wszystko_ok = true;
		//sprawdzanie nickname
		$nick = $_POST['nick'];
		//sprawdzanie długości nicka
		if ((strlen($nick) < 3) || (strlen($nick) > 20))
		{
			$wszystko_ok = false;
			$_SESSION['error_nick'] = "Nick musi mieć od 3 do 20 znaków";
		}
		//sprawdzanie znakow nicka
		if(ctype_alnum($nick)==false)
		{
			$wszystko_ok = false;
			$_SESSION['error_nick'] = "Nick może składać się tylko z liter i cyfr";
		}

		//sprawdz email
		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);

		if((filter_var($emailB, FILTER_VALIDATE_EMAIL) == FALSE) || ($emailB != $email))
		{
			$wszystko_ok = false;
			$_SESSION['error_email'] = "Podaj poprawny adres email";
		}

		//sprawdz hasło
		$password = $_POST['pass1'];
		$password2 = $_POST['pass2'];

		if((strlen($password) < 8) || (strlen($password) >20))
		{
			$wszystko_ok = false;
			$_SESSION['error_password'] = "Hasło nieprawidłowe";
		}
		if($password != $password2)
		{
			$wszystko_ok = false;
			$_SESSION['error_password'] = "Hasła są różne";
		}

		$password_hash = password_hash($password, PASSWORD_DEFAULT);

		//regulamin
		if(!isset($_POST['regulamin']))
		{
			$_SESSION['error_regulamin'] = "Zaakceptuj regulamin";
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
				//czy istnieje email?
				$rezultat = $polaczenie -> query("SELECT idUser FROM uzytkownicy WHERE email='$email'");

				if (!$rezultat) throw new Exception($polaczenie -> error);

				$ile_takich_emaili = $rezultat-> num_rows;
				if($ile_takich_emaili > 0)
				{
					$wszystko_ok = false;
					$_SESSION['error_email'] = "Istnieje już konto przypisane do tego adresu e-mail";
				}

				//czy nick wolny?
				$rezultat = $polaczenie -> query("SELECT idUser FROM uzytkownicy WHERE user='$nick'");

				if (!$rezultat) throw new Exception($polaczenie -> error);

				$ile_takich_nickow = $rezultat-> num_rows;
				if($ile_takich_nickow > 0)
				{
					$wszystko_ok = false;
					$_SESSION['error_nick'] = "Nick zajęty";
				}

				if($wszystko_ok == true)
				{
					//walidacja poprawna
					if($polaczenie -> query("INSERT INTO uzytkownicy VALUES (NULL, '$nick','$password_hash', '$email', '0', '$data')"))
					{
						$_SESSION['udanarejestracja'] = true;
						header('Location: witamy.php');
					}
					else
					{
						throw new Exception($polaczenie -> error);
					}
				}

				$polaczenie -> close();
			}
		}
		catch(Exception $error)
		{
			echo'<div class = "error">Błąd serwera!</div>';
			echo $error;
		}

	}
	
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<title>Sklep internetowy - rejestracja</title>

	<style>
	.error
	{
		color: red;
		margin-top: 10px;
		margin-bottom: 10px;
	}		
	</style>
</head>

<body>
<form method="POST" accept-charset="utf-8">

Nickname: <br /><input type="text" name="nick" value="" placeholder=""/><br />
<?php

	if(isset($_SESSION['error_nick']))
	{
		echo '<div class = "error">'.$_SESSION['error_nick'].'</div>';
		unset($_SESSION['error_nick']);
	}

?>
E-mail: <br /><input type="email" name="email" value="" placeholder=""/><br />
<?php

	if(isset($_SESSION['error_email']))
	{
		echo '<div class = "error">'.$_SESSION['error_email'].'</div>';
		unset($_SESSION['error_email']);
	}

?>
Hasło: <br /><input type="password" name="pass1" value="" placeholder=""/><br />
<?php

	if(isset($_SESSION['error_password']))
	{
		echo '<div class = "error">'.$_SESSION['error_password'].'</div>';
		unset($_SESSION['error_password']);
	}

?>
Powtórz hasło: <br /><input type="password" name="pass2" value="" placeholder=""/><br />
<label><input type="checkbox" name="regulamin" value=""/>Akceptuję regulamin</label><br />
<?php

	if(isset($_SESSION['error_regulamin']))
	{
		echo '<div class = "error">'.$_SESSION['error_regulamin'].'</div>';
		unset($_SESSION['error_regulamin']);
	}

?>
<br /><br />
<input type="submit" name="" value="Zarejestruj się"/>
</form>

</body>
</html>