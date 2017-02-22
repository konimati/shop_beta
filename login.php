<?php 

	session_start();

	if(!isset($_POST['login']) || (!isset($_POST['pass'])))
	{
		header("Location: logowanie.php");
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
			$login = $_POST['login'];
			$password = $_POST['pass'];

			$login = htmlentities($login, ENT_QUOTES, "UTF-8");

			if($rezultat = @$polaczenie->query(
				sprintf("SELECT * FROM uzytkownicy WHERE user='%s'",
				mysqli_real_escape_string($polaczenie, $login)
				)))
			{
				$ilu_userow = $rezultat->num_rows;
				if($ilu_userow > 0)
				{
					$wiersz = $rezultat->fetch_assoc();

					//weryfikacja hasła
					if(password_verify($password, $wiersz['pass']))
					{
						$_SESSION['logged'] = true;
					
						$_SESSION['id'] = $wiersz['idUser'];
						$_SESSION['user'] = $wiersz['user'];
						$_SESSION['email'] = $wiersz['email'];

						unset($_SESSION['blad']);
						$rezultat->close();
						if(isset($_SESSION["produkt"]))
						{
							header('Location: koszyk.php');
						}
						else
						{
							header('Location: index.php');
						}
						
					}
					else //zle haslo
					{
						$_SESSION['blad']='<span style ="color:red">Nieprawidłowy login lub hasło!</span>';
						header('Location: logowanie.php');
					}
				}
				else //login nie znaleziony
				{
					$_SESSION['blad']='<span style ="color:red">Nieprawidłowy login lub hasło!</span>';
						header('Location: logowanie.php');
				}
			}

			$polaczenie -> close();
		}
	}
	catch(Exception $error)
	{
		echo'<span style="color:red">Błąd serwera!</span>';
	}




?>