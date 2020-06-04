<?php
	
	session_start();
	
	if(!isset($_SESSION['zalogowany'])) {
		header('Location: index.php');
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
			<a href="index.php" style="text-decoration: none;">
				<h2 class="logo">Biblioteka<i class="icon-book-1"></i></h2></a>
		</div>
		<div class="sides">
			<div class="logout">
				<a class="sociallink" href="konto.php"><i class="icon-user"></i></a>
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
			<form method="post" action="wyniki.php">
				<input class="search" type="text" name="pytanie" placeholder="Wyszukiwarka...">
				<input class="searchbtn" type="submit" value="Szukaj">
			</form>
		</div>
		<br><br>
		
		<nav>
			<div class="menu">
				<a href="konto.php" class="linkikona"><div class="option">Dane</div></a>
				<a href="kontowyp.php" class="linkikona"><div class="option">Wypożyczenia</div></a>
				<a href="kontorez.php" class="linkikona"><div class="option">Rezerwacje</div></a>
				<a href="kontohist.php" class="linkikona"><div class="option">Historia czytelnika</div></a>
			</div>
		</nav>
		
		<article>
			<div class="content">
				<h3>Książki wypożyczone</h3>
				<?php
					if(isset($_SESSION['id'])) {
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
								$moje_id = $_SESSION['id'];
								
								$rezultat = $polaczenie->query("SELECT w.data_zwrotu, e.id, k.tytul, k.rok_wydania, k.strony, a.nazwisko, w.id, w.idr FROM wypozyczenie AS w, egzemplarz AS e, ksiazka AS k, autor AS a WHERE w.idc = '$moje_id' AND w.oddana = '0' AND w.ide = e.id AND e.idk = k.id AND k.ida = a.id");
								if(!$rezultat) throw new Exception($polaczenie->error);
								$ile_wypozyczen = $rezultat->num_rows;
								if($ile_wypozyczen>0) {
									echo '<table class="results"><tr><th>Nr egz.</th><th>Autor</th><th>Tytuł</th><th>Rok wydania</th><th>Liczba stron</th><th>Data zwrotu</th><th>Prolongata</th></tr>';
									while ($wiersz = $rezultat->fetch_array()) {
										$data_zwrotu = $wiersz[0];
										$ide = $wiersz[1];
										$tytul = $wiersz[2];
										$rok_wydania = $wiersz[3];
										$strony = $wiersz[4];
										$nazwisko = $wiersz[5];
										$idw = $wiersz[6];
										$idr = $wiersz[7];
										if ($idr == 0) {
											$teraz = new DateTime();
											$teraz->add(new DateInterval('P5D'));
											$czas_zwrotu = DateTime::createfromFormat('Y-m-d H:i:s', $data_zwrotu);
											$roznica = $teraz->diff($czas_zwrotu);
											if($teraz>$czas_zwrotu) {
												$prolongata = '<form action="prolongata.php" method="post"><input type="hidden" name="idw" value="'.$idw.'"><input type="submit" value="Prolonguj" id="prolongujbtn"></form>';
											} else {
												$prolongata = "Za: ".$roznica->format('%d dni, %h godz i %i min').".";
											}
										} else {
											$prolongata = "Rezerwacja";
										}
										echo '<tr><td>'.$ide.'</td><td>'.$tytul.'</td><td>'.$nazwisko.'</td><td>'.$rok_wydania.'</td><td>'.$strony.'</td><td>'.$data_zwrotu.'</td><td>'.$prolongata.'</td></tr>';
									}
									echo '</table>';
								} else {
									echo 'Brak wypożyczeń';
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
			</div>
		</article>
		
		<div style="clear:both;"></div>
		
	</div>
	
</body>
</html>