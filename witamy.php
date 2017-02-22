<?php 

	session_start();

	if(isset($_SESSION['udanarejestracja']))
	{
		header('Location: index.php');
		exit();
	}
	else
	{
		unset($_SESSION['udanarejestracja']);
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
<b>Dziękujemy za rejestrację</b> <br /><br />
<a href="index.php">Zaloguj się</a>
<br /><br />


</body>
</html>