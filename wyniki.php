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
		
		<table class="results">
			<!-- Tabela na egzemplarze -->
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
						if(isset($_POST['pytanie']) && ($_POST['pytanie'])==true) {
							$pytanie1 = $_POST['pytanie'];
							$pytanie = '%'.$pytanie1.'%';
						}
						// Lista wyników wyszukiwania
						$rezultat = $polaczenie->query("SELECT e.id, a.nazwisko, k.tytul, e.stan FROM ksiazka AS k, autor AS a, egzemplarz AS e WHERE a.id = k.ida AND k.id = e.idk AND (nazwisko LIKE '$pytanie' OR tytul LIKE '$pytanie') ORDER BY k.tytul ASC");
						if(!$rezultat) throw new Exception($polaczenie->error);
						$ile_ksiazek = $rezultat->num_rows;
						if($ile_ksiazek>0) {
							while($wiersz = $rezultat->fetch_array()) {	// Odczytywanie i wyświetlanie danych z bazy w pętli
								$id = $wiersz[0];
								$autor = $wiersz[1];
								$tytul = $wiersz[2];
								$stan = $wiersz[3];
								$stan2 = "nie";
								$kolor = "red";
								if ($stan == "1") {
									$stan2 = "tak";
									$kolor = "green";
								}
								// Tabela na egzemplarze
								echo '<tr>
									<td>
										<a href="ksiazka.php?id='.$id.'" class="linkdoksiazki">'.$id.'</a>
									</td>
									<td>
										<a href="ksiazka.php?id='.$id.'" class="linkdoksiazki">'.$autor.'</a>
									</td>
									<td>
										<a href="ksiazka.php?id='.$id.'" class="linkdoksiazki">'.$tytul.'</a>
									</td>
									<td>
										<a href="ksiazka.php?id='.$id.'" class="linkdoksiazki" style="color:'.$kolor.'">'.$stan2.'</a>
									</td>
								</tr>';
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
		</table>
	</div>
	
</body>
</html>