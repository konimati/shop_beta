<?php 
	session_start();
	
	if(!isset($_SESSION['logged']))
	{
		header('Location: index.php');
		exit();
	}
//Array ( [0] => Array ( [nazwa] => Amino Energy [url] => Amino_Energy_i25512_d250x250.jpg [cena] => 75 [smak] => Czekolada [ilosc] => 2 ) 
	//[1] => Array ( [nazwa] => HIGH KICK [url] => HIGH_KICK_i27789_d250x250.jpg [cena] => 102 [smak] => Czekolada [ilosc] => 1 ) )
	
//Array ( [0] => Array ( [nazwa] => Amino Energy [url] => Amino_Energy_i25512_d250x250.jpg [cena] => 75 [smak] => Czekolada [ilosc] => 1 [idSmaki_produktow] => 1 ) 
//	[1] => Array ( [nazwa] => HIGH KICK [url] => HIGH_KICK_i27789_d250x250.jpg [cena] => 102 [smak] => Tiramisu [ilosc] => 2 [idSmaki_produktow] => 6 ) )
	if(isset($_POST['zamowienie']) && ($_POST['zamowienie']) == 'nowe')
	{
		//print_r($_POST['zamowienie']);
		$data = date("Y-m-d H:i:s"); 

	
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
				$suma =$_SESSION["suma_zamówienia"];
				$idUser =$_SESSION["id"];
				$idPost =$_POST["idPost"];
				$zamowienie = $polaczenie -> query("INSERT INTO zamowienia VALUES (NULL, '$data','$suma', '$idUser', '$idPost')");
				if (!$zamowienie) throw new Exception($polaczenie -> error);
				$idZamowienia = mysqli_insert_id($polaczenie);
				foreach ($_SESSION["produkt"] as $key => $value) {
					$idSmaki_produktow = $_SESSION["produkt"][$key]["idSmaki_produktow"];
					$ilosc = $_SESSION["produkt"][$key]["ilosc"];
					$zamowienie = $polaczenie -> query("INSERT INTO zamowienia_has_produkty_has_smaki SET idZamowienie = $idZamowienia, idSmaki_produktow='$idSmaki_produktow', ilosc='$ilosc'");
					if (!$zamowienie) throw new Exception($polaczenie -> error);
				}
				
				//$sql = mysqli_query($mysqli, "insert into bank_details SET user_id = $user_id, bank_name='$bn', ac_no='$ac_no'");
				//$zamowienie = $polaczenie -> query("INSERT INTO zamowienia VALUES (NULL, '$data','$suma', '$idUser', '$idPost')");
				
				header('Location: index.php');
			}

				$polaczenie -> close();
			
		}
		catch(Exception $error)
		{
			echo'<div class = "error">Błąd serwera!</div>';
			echo $error;
		}
	
	}
	else
	{
		header('Location: order_info.php');
	}
		
// SELECT * FROM `zamowienia` INNER JOIN zamowienia_has_produkty_has_smaki ON zamowienia_has_produkty_has_smaki.idZamowienie = zamowienia.idZamowienie INNER JOIN produkty_has_smaki ON produkty_has_smaki.idSmaki_produktow = zamowienia_has_produkty_has_smaki.idSmaki_produktow INNER JOIN produkty ON produkty.idProdukty=produkty_has_smaki.idProdukty INNER JOIN smaki ON smaki.idSmaki=produkty_has_smaki.idSmaki WHERE zamowienia.idZamowienie = 6

?>


