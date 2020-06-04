<?php
	session_start();
	
	if(!isset($_POST['login']) || !isset($_POST['haslo'])) {
		header('Location: index.php');
		exit();
	}
	
	require_once "connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	try {
		$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
		if($polaczenie->connect_errno!=0) {
			throw new Exception(mysqli_connect_errno());
		} else {
			$email = $_POST['login'];
			$haslo = $_POST['haslo'];
			$email = htmlentities($email, ENT_QUOTES, "UTF-8");
			$rezultat = $polaczenie->query(sprintf("SELECT * FROM czytelnik WHERE BINARY email='%s'", mysqli_real_escape_string($polaczenie,$email)));
			if(!$rezultat) throw new Exception($polaczenie->error);
			$ilu_userow = $rezultat->num_rows;
			if($ilu_userow>0) {
				$wiersz = $rezultat->fetch_assoc();
				if(password_verify($haslo, $wiersz['haslo'])) {
					$_SESSION['zalogowany'] = true;
					$_SESSION['id'] = $wiersz['id'];
					unset($_SESSION['blad']);
					$rezultat->free_result();
					header('Location: panel.php');
				} else {
					$_SESSION['blad'] = 'Nieprawidłowy login lub hasło!';
					header('Location: index.php');
				}
			} else {
				$_SESSION['blad'] = 'Nieprawidłowy login lub hasło!';
				header('Location: index.php');
			}
			$polaczenie->close();
		}
		
	} catch(Exception $e) {
		echo '<span style="color:red">Błąd serwera!</span>';
		//echo '<br>Informacja deweloperska: '.$e;
	}
	
?>