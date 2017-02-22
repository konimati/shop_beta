<?php 
	session_start();

	if((!isset($_SESSION['produkt']) or empty($_SESSION['produkt'])) or ((!isset($_SESSION['logged']) or ($_SESSION['logged'] == false))))
	{
		header('Location: index.php');
		exit();
	}
	
	function pobranieDostawcy()
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
				$zapytanie_produkty = "SELECT * FROM post";
				
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
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script>
	

	jQuery(document).ready(function(){
		var $submit = $("#submit_post").hide();
        // $radio = $('input[name="przedplata"]').click(function() {
        //     $submit.toggle( $radio.is(":checked") );
        // });

        jQuery('#btn_przedplata').on('click', function(event) {  
            jQuery('#przedplata').toggle('show');
            jQuery('#odbior input').removeAttr('checked');
            jQuery('#odbior').hide();
            $submit.hide();
            $('input[type="radio"][name="przedplata"]').change(function() {
	     	if(this.checked)
	    	{
	        	$submit.show();
			}
			else
			{
				$submit.hide();
			}
		

		 });
            //if($('#radio_button').is(':checked'))
            

        });
        jQuery('#btn_pobranie').on('click', function(event) {        
            jQuery('#odbior').toggle('show');
            jQuery('#przedplata input').removeAttr('checked');
            jQuery('#przedplata').hide();
            $submit.hide();
             $('input[type="radio"][name="pobranie"]').change(function() {
	     	if(this.checked)
	    	{
	        	$submit.show();
			}
			else
			{
				$submit.hide();
			}
		

		 });
            
        });

	    
    });

	</script>
</head>

<body>
<h1>Sklep suplementów diety</h1>
<?php  

	if((isset($_SESSION['logged']) && ($_SESSION['logged'] == true)))
	{
		echo "<p>Witaj ".$_SESSION['user']."!! [<a href='logout.php'>Wyloguj się</a>]</p>";
		echo "<p>[<a href='profile_edit.php'>Edytuj swoje konto</a>]</p>";
		echo "<br />";
	}
	echo '<p>Wybierz sposób dostawy</p>';
	$post = pobranieDostawcy();
	if(empty($post))
	{	
		exit();
	}
	//print_r($post);
	echo '<form method="POST" action="order_info.php" accept-charset="utf-8">';
	echo "<fieldset>";

	echo '<button type="button" id="btn_przedplata">Przedpłata</button>';
	echo '<button type="button" id="btn_pobranie">Przy odbiorze</button>';
	echo '<div id="przedplata" hidden>';
	foreach ($post as $key => $value) 
	{
		
		echo '<label>';
		echo '<img src="post/'.$post[$key]["url"].'">';
		echo '<input type="radio" name="przedplata" value="'.$post[$key]["nazwa"].'" />'.$post[$key]["nazwa"];
		//echo '<input type="hidden" name="cena" value="'.$post[$key]["cena_przedplata"].'" />';
		//echo '<br />';
		echo ' Koszt: '.$post[$key]["cena_przedplata"];
		echo '</label>';
		
	}
	echo '</div>';
	echo '<br />';
	echo '<div id="odbior" hidden>';
	foreach ($post as $key => $value) 
	{
		
		echo '<label>';
		echo '<img src="post/'.$post[$key]["url"].'">';
		echo '<input type="radio" name="pobranie" value="'.$post[$key]["nazwa"].'" />'.$post[$key]["nazwa"];
		//echo '<input type="hidden" name="cena" value="'.$post[$key]["cena_pobranie"].'" />';
		//echo '<br />';
		echo ' Koszt: '.$post[$key]["cena_pobranie"];
		echo '</label>';
		
	}
	echo '</div>';
	echo "<br /><br />";

	echo '<button id="submit_post" type="submit">Wybierz</button></div>';
	
	echo '</form>';
	echo "</fieldset>";
	echo "<br /><br />";
	echo "<p>[<a href='koszyk.php'>Powrót do koszyka</a>]</p>";

	//pobieranie z bazy danych o przewoźnikach
	//przedpłata
	//za pobraniem
	
?>

</form>

</body>
</html>