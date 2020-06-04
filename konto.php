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
				<h3>Dane czytelnika</h3>
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
								// Czytelnik
								$rezultat = $polaczenie->query("SELECT * FROM czytelnik WHERE id = '$moje_id'");
								if(!$rezultat) throw new Exception($polaczenie->error);
								$ilu_czytelnikow = $rezultat->num_rows;
								if($ilu_czytelnikow>0) {
									$wiersz = $rezultat->fetch_array(); // Odczytywanie i wyświetlanie danych z bazy
									$imie = $wiersz[1];
									$nazwisko = $wiersz[2];
									$adres = $wiersz[3];
									$email = $wiersz[4];
									$telefon = $wiersz[6];
									echo '<p>Imię: '.$imie.'</p><p>Nazwisko: '.$nazwisko.'</p><p>Adres: '.$adres.'</p><p>E-mail: '.$email.'</p><p>Telefon: '.$telefon.'</p>';
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