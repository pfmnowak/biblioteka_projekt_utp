<?php
	
	session_start();
	
	if(!isset($_SESSION['zalogowanyb'])) {
		header('Location: logowanie.php');
		exit();
	}
	
	if(isset($_POST['idw'])) { // Czy wysłano formularz
		
		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		
		try {
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			if($polaczenie->connect_errno!=0) {
				throw new Exception(mysqli_connect_errno());
			} else {
				$idw = $_POST['idw'];
				if($polaczenie->query("UPDATE wypozyczenie SET data_zwrotu = now() + INTERVAL 30 DAY WHERE id = '$idw'")) {
					header('Location: kontowyp.php');
					$_SESSION['e_czytelnik']="Pomyślnie prolongowano książkę.";
					header('Location: wypozyczanie3.php');
				} else {
					throw new Exception($polaczenie->error);
				}
				$polaczenie->close();	// Zamknięcie połączenia
			}
		}
		catch(Exception $e) {
			echo '<span class="error">Błąd serwera!</span>';
			// echo '<br />Informacja deweloperska: '.$e;
		}
	}
	
?>