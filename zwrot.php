<?php
	
	session_start();
	
	if(!isset($_SESSION['zalogowanyb'])) {
		header('Location: index.php');
		exit();
	}
	
	if(isset($_POST['wybranaksiazka'])) { // Czy wysłano formularz
		
		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		
		try {
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			if($polaczenie->connect_errno!=0) {
				throw new Exception(mysqli_connect_errno());
			} else {
				$wybranaksiazka = $_POST['wybranaksiazka'];
				$rezultat = $polaczenie->query("SELECT id FROM wypozyczenie WHERE ide = '$wybranaksiazka' AND oddana = '0' AND idr = '0'");
				if(!$rezultat) throw new Exception($polaczenie->error);
				$ile_wypozyczen = $rezultat->num_rows;
				if($ile_wypozyczen>0) {	// Jeśli nie ma rezerwacji
					if($polaczenie->query("UPDATE wypozyczenie SET oddana = 1, data_zwrotu = now() WHERE ide = '$wybranaksiazka' AND oddana = '0'")) {
						if($polaczenie->query("UPDATE egzemplarz SET stan = 1 WHERE id = '$wybranaksiazka'")) {
							header('Location: wypozyczanie1.php');
						} else {
							throw new Exception($polaczenie->error);
						}
					} else {
						throw new Exception($polaczenie->error);
					}
				} else {	// Jeśli są rezerwacje
					if($polaczenie->query("UPDATE wypozyczenie SET oddana = 1, data_zwrotu = now() WHERE ide = '$wybranaksiazka' AND oddana = '0'")) {
						header('Location: wypozyczanie1.php');
					} else {
						throw new Exception($polaczenie->error);
					}
				}
				$rezultat->free_result();
				$polaczenie->close();	// Zamknięcie połączenia
			}
		}
		catch(Exception $e) {
			echo '<span class="error">Błąd serwera!</span>';
			//echo '<br />Informacja deweloperska: '.$e;
		}
	}
	
?>