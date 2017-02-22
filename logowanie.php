<?php 

	session_start();

	if(isset($_SESSION['logged']) && ($_SESSION['logged'] == true))
	{
		header('Location: index.php');
		exit();
	}
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<title>Sklep internetowy</title>
</head>

<body>
<h1>Sklep suplementów diety</h1>
<a href="register.php">Rejestracja</a>
<a href="index.php">Katalog produktów</a>
<br /><br />
<form action="login.php" method="POST" accept-charset="utf-8">
	Login: <br /> <input type="text" name="login" placeholder=""> <br />
	Hasło: <br /> <input type="password" name="pass" value="" placeholder=""> <br />
	<br /> <input type="submit" name="zaloguj" value="Zaloguj się">
</form>
<?php

	if(isset($_SESSION['blad']))
		echo $_SESSION['blad'];

?>

</body>
</html>