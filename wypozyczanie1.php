<?php
	
	session_start();
	
	if(!isset($_SESSION['zalogowanyb'])) {
		header('Location: logowanie.php');
		exit();
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
			<form method="post">
				<input class="search" type="text" name="id_egz" placeholder="ID książki">
				<input class="searchbtn" type="submit" value="Szukaj">
			</form>
		</div>
		<br><br>
		
		<table class="results">
			<!-- Tabela na egzemplarze-->
			<tr><th>Nr</th><th>Autor</th><th>Tytuł</th><th>Dostępność</th></tr>
			<?php
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
						// Książka do wypożyczenia
						$id_egz = $_POST['id_egz'];
						$rezultat = $polaczenie->query("SELECT k.tytul, a.nazwisko, e.id, e.stan FROM ksiazka AS k, autor AS a, egzemplarz AS e WHERE a.id = k.ida AND k.id = e.idk AND e.id = '$id_egz'");
						if(!$rezultat) throw new Exception($polaczenie->error);
						$ile_ksiazek = $rezultat->num_rows;
						if($ile_ksiazek>0) {
							while($wiersz = $rezultat->fetch_array()) {	// Odczytywanie i wyświetlanie danych z bazy w pętli
								$tytul = $wiersz[0];
								$autor = $wiersz[1];
								$id = $wiersz[2];
								$stan = $wiersz[3];
								$prolongata = "<br>";
								if ($stan == "1") {
									$stan2 = "Dostępna";
									$kolor = "green";
									$action = "wypozyczanie2";
									$button = "Wypożycz";
								} else {
									$rezultat = $polaczenie->query("SELECT id, data_zwrotu, oddana, idr FROM wypozyczenie WHERE ide = '$id_egz' ORDER BY id DESC LIMIT 1");
									if(!$rezultat) throw new Exception($polaczenie->error);
									$wiersz = $rezultat->fetch_array();
									$idw = $wiersz[0];
									$data_zwrotu = $wiersz[1];
									$oddana = $wiersz[2];
									$idr = $wiersz[3];
									if ($oddana == 1) {
										$stan2 = "Zarezerwowana";
										$kolor = "purple";
										$action = "wypozyczanie2";
										$button = "Wypożycz";
									} else {
										$stan2 = "Wypożyczona";
										$kolor = "red";
										$action = "zwrot";
										$button = "Zwróć";
										if ($idr == 0) {
											$teraz = new DateTime();
											$teraz->add(new DateInterval('P5D'));
											$czas_zwrotu = DateTime::createfromFormat('Y-m-d H:i:s', $data_zwrotu);
											$roznica = $teraz->diff($czas_zwrotu);
											if($teraz>$czas_zwrotu) {
												$prolongata = '<form action="prolongatab.php" method="post"><input type="hidden" name="idw" value="'.$idw.'"><input type="submit" value="Prolonguj"></form>';
											}
										}
									}
								}
								echo '<tr><td>'.$id.'</td><td>'.$autor.'</td><td>'.$tytul.'</td><td style="color:'.$kolor.'">'.$stan2.'</td></tr></table><br>
								<form action="'.$action.'.php" method="post"><input type="hidden" name="wybranaksiazka" value="'.$id.'"><input type="submit" value="'.$button.'" class="submitksiazka"></form>'.$prolongata.'';
							}
						}
						$rezultat->free_result();
						$polaczenie->close();	// Zamknięcie połączenia
					}
				}
				catch(Exception $e) {
					echo '<span class="error">Błąd serwera!</span>';
					// echo '<br>Informacja deweloperska: '.$e;
				}
			?>
	</div>
	
</body>
</html>