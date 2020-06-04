<?php
	
	session_start();
	
	if(!isset($_SESSION['zalogowanyb'])) {
		header('Location: index.php');
		exit();
	}
	
	if(isset($_POST['id_czyt'])) { // Czy wysłano formularz.
		
		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		
		try {
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			if($polaczenie->connect_errno!=0) {
				throw new Exception(mysqli_connect_errno());
			} else {
				// Zapewnienie wyświetlania polskich znaków
				mysqli_query($polaczenie, "SET CHARSET utf8");
				mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
				
				$wybranaksiazka = $_POST['wybranaksiazka'];
				$id_czyt = $_POST['id_czyt'];
				$id_bibl = $_SESSION['id_bibl'];
				
				// Czy taki czytelnik istnieje?
				if($id_czyt != 0) {
					$rezultat = $polaczenie->query("SELECT id FROM czytelnik WHERE id = '$id_czyt'");
					if(!$rezultat) throw new Exception($polaczenie->error);
					$ilu_czytelnikow = $rezultat->num_rows;
					if($ilu_czytelnikow>0) {	// Jeśli id czytelnika jest poprawne
						$rezultat = $polaczenie->query("SELECT id FROM wypozyczenie WHERE idc = '$id_czyt' AND oddana = '0'");
						if(!$rezultat) throw new Exception($polaczenie->error);
						$ile_wypozyczen = $rezultat->num_rows;
						if($ile_wypozyczen<5) {	// Jeśli czytelnik nie wykorzystał limitu wypożyczeń
							$rezultat = $polaczenie->query("SELECT id, idr FROM wypozyczenie WHERE ide = '$wybranaksiazka' AND idr <> '0'");
							if(!$rezultat) throw new Exception($polaczenie->error);
							$ile_rezerwacji = $rezultat->num_rows;
							if($ile_rezerwacji>0) {	// Jeśli książka jest zarezerwowana
								$wiersz = $rezultat->fetch_array();
								$idw = $wiersz[0];
								$idr = $wiersz[1];
								if($idr == $id_czyt) {
									if($polaczenie->query("UPDATE wypozyczenie SET idr = 0 WHERE id = '$idw'")) {
									} else {
										throw new Exception($polaczenie->error);
									}
								} else {
									$_SESSION['e_czytelnik']="Ta książka jest zarezerwowana przez innego czytelnika.";
									header('Location: wypozyczanie3.php');
									exit();
								}
							}
							if($polaczenie->query("INSERT INTO wypozyczenie VALUES (NULL, '$id_bibl', '$id_czyt', '$wybranaksiazka', now(), now() + INTERVAL 30 DAY, '0', '0')")) {
								if($polaczenie->query("UPDATE egzemplarz SET stan = 0 WHERE id = '$wybranaksiazka'")) {
									header('Location: wypozyczanie3.php');
									$_SESSION['e_czytelnik']="Pomyślnie wypożyczono egzemplarz.";
								} else {
									throw new Exception($polaczenie->error);
								}
							} else {
								throw new Exception($polaczenie->error);
							}
						} else {
						$_SESSION['e_czytelnik']="Czytelnik może wypożyczyć maksymalnie 5 książek.";
							header('Location: wypozyczanie3.php');
						}
					} else {
						$_SESSION['e_czytelnik']="Niepoprawny numer id czytelnika.";
						header('Location: wypozyczanie3.php');
					}
				} else {
					$_SESSION['e_czytelnik']="Niepoprawny numer id czytelnika.";
					header('Location: wypozyczanie3.php');
				}
				$rezultat->free_result();
				$polaczenie->close();	// Zamknięcie połączenia
			}
		}
		catch(Exception $e) {
			echo '<span class="error">Błąd serwera!</span>';
			// echo '<br />Informacja deweloperska: '.$e;
		}
	}
	
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Biblioteka</title>
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="css/fontello.css" type="text/css"/>
</head>

<body>
	
	<header>
		<div class="sides">
			&nbsp
		</div>
		<div class="mid">
			<a href="index.php" style="text-decoration: none;"><h2 class="logo">Biblioteka<i class="icon-book-1"></i></h2></a>
		</div>
		<div class="sides">
			<div class="logout">
				<a class="sociallink" href="panelb.php"><i class="icon-user"></i></a>
			</div>
			<div class="logout">
				<a class="sociallink" href="logout.php"><i class="icon-logout"></i></a>
			</div>
			<div style="clear: both;"></div>
		</div>
		<div style="clear: both;"></div>
	</header>
	
	<div class="container">
		<div>
			<form method="post" action="wypozyczanie2.php">
				<input class="search" type="text" name="id_czyt" placeholder="ID czytelnika">
				<?php
					$wybranaksiazka = $_POST['wybranaksiazka'];
					echo '<input type="hidden" name="wybranaksiazka" value="'.$wybranaksiazka.'">';
				?>
				<br>
				<input type="submit" value="Wypożycz">
			</form>
		</div>
	</div>
	
</body>
</html>