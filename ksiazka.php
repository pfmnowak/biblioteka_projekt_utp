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
		
		<article>
			<div class="content">
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
							$ksiazka = $_GET['id'];
							// Wyświetlenie danego egzemplarza książki ze wszystkimi szczegółami
							$rezultat = $polaczenie->query("SELECT k.tytul, k.isbn, k.rok_wydania, k.strony, a.nazwisko, w.wydawnictwo, g.gatunek, e.id, e.stan FROM ksiazka AS k, autor AS a, wydawnictwo AS w, gatunek AS g, egzemplarz AS e WHERE a.id = k.ida AND w.id = k.idw AND g.id = k.idg AND k.id = e.idk AND e.id = '$ksiazka'");
							if(!$rezultat) throw new Exception($polaczenie->error);
							$ile_ksiazek = $rezultat->num_rows;
							if($ile_ksiazek>0) {
								$wiersz = $rezultat->fetch_array();	// Odczytywanie i wyświetlanie danych z bazy
								$tytul = $wiersz[0];
								$isbn = $wiersz[1];
								$rok = $wiersz[2];
								$strony = $wiersz[3];
								$autor = $wiersz[4];
								$wydawnictwo = $wiersz[5];
								$gatunek = $wiersz[6];
								$id = $wiersz[7];
								$stan = $wiersz[8];
								$stan2 = "Wypożyczona";
								$kolor = "red";
								if ($stan == "1") {
									$stan2 = "Dostępna";
									$kolor = "green";
								}
								echo '<h3>'.$tytul.'</h3><p>'.$autor.'</p><p>Liczba stron: '.$strony.'</p><p>Rok wydania: '.$rok.'</p><p>ISBN: '.$isbn.'</p><p>Wydawnictwo: '.$wydawnictwo.'</p><p>Gatunek: '.$gatunek.'</p><p>Nr egzemplarza: '.$id.'</p><p style="color:'.$kolor.'">Dostępność: '.$stan2.'</p>';
								if ($stan == "0") {
									$id_czyt = $_SESSION['id'];
									$rezultat = $polaczenie->query("SELECT idc FROM wypozyczenie WHERE ide = '$id' AND oddana = '0'");
									if(!$rezultat) throw new Exception($polaczenie->error);
									$wiersz = $rezultat->fetch_array();
									$id_z_bazy = $wiersz[0];
									if($id_z_bazy != $id_czyt) {
										echo '<form action="rezerwacja.php" method="post"><input type="hidden" name="wybranaksiazka" value="'.$id.'"><input type="submit" value="Rezerwuj" class="submitksiazka"></form>';
									}
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
		</article>
		
	</div>
	
</body>
</html>