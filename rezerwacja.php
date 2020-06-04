<?php
	
	session_start();
	
	if(!isset($_SESSION['zalogowany'])) {
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
				$id_czyt = $_SESSION['id'];
				$rezultat = $polaczenie->query("SELECT id FROM wypozyczenie WHERE idr = '$id_czyt'");
				if(!$rezultat) throw new Exception($polaczenie->error);
				$ile_rezerwacji = $rezultat->num_rows;
				if($ile_rezerwacji<3) {
					if($polaczenie->query("UPDATE wypozyczenie SET idr = '$id_czyt' WHERE ide = '$wybranaksiazka' AND oddana = '0'")) {
						if($polaczenie->query("UPDATE egzemplarz SET stan = 2 WHERE id = '$wybranaksiazka'")) {
							header('Location: kontorez.php');
						} else {
							throw new Exception($polaczenie->error);
						}
					} else {
						throw new Exception($polaczenie->error);
					}
				} else {
					header('Location: kontorez.php');
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