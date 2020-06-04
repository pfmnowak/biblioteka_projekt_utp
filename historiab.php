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
		
		<nav>
			<div class="menu">
				<a href="kontob.php" class="linkikona"><div class="option">Dane bibliotekarza</div></a>
				<a href="historiab.php" class="linkikona"><div class="option">Historia bibliotekarza</div></a>
				<a href="kontobwyp.php" class="linkikona"><div class="option">Wypożyczenia aktywne</div></a>
				<a href="archiwum.php" class="linkikona"><div class="option">Archiwum</div></a>
			</div>
		</nav>
		
		
		<article>
			<div class="content">
				<h3>Historia bibliotekarza</h3>
				<?php
					if(isset($_SESSION['id_bibl'])) {
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
								$id_bibl = $_SESSION['id_bibl'];
								
								$rezultat = $polaczenie->query("SELECT w.data_zwrotu, e.id, k.tytul, k.rok_wydania, a.nazwisko, w.data_wypozyczenia, w.oddana, w.idc FROM wypozyczenie AS w, egzemplarz AS e, ksiazka AS k, autor AS a WHERE w.idb = '$id_bibl' AND w.ide = e.id AND e.idk = k.id AND k.ida = a.id ORDER BY w.data_wypozyczenia DESC");
								if(!$rezultat) throw new Exception($polaczenie->error);
								$ile_wypozyczen = $rezultat->num_rows;
								if($ile_wypozyczen>0) {
									echo '<table class="results"><tr><th>Nr egz.</th><th>Autor</th><th>Tytuł</th><th>Rok wydania</th><th>Data wypożyczenia</th><th>Data zwrotu</th><th>Czytelnik</th></tr>';
									while ($wiersz = $rezultat->fetch_array()) {
										$data_zwrotu = $wiersz[0];
										$ide = $wiersz[1];
										$tytul = $wiersz[2];
										$rok_wydania = $wiersz[3];
										$nazwisko = $wiersz[4];
										$data_wypozyczenia = $wiersz[5];
										$oddana = $wiersz[6];
										$czyt = $wiersz[7];
										$zwrot = $data_zwrotu;
										if($oddana == 0) {
											$zwrot = 'Wypożyczona';
										}
										echo '<tr><td>'.$ide.'</td><td>'.$tytul.'</td><td>'.$nazwisko.'</td><td>'.$rok_wydania.'</td><td>'.$data_wypozyczenia.'</td><td>'.$zwrot.'</td><td>'.$czyt.'</td></tr>';
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