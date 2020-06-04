<?php
	
	session_start();
	
	if(!isset($_SESSION['zalogowanyb'])) {
		header('Location: index.php');
		exit();
	}
	
	if(isset($_POST['tytul'])) { // Czy wysłano formularz.
		
		// Udana walidacja?
		$wszystko_ok = true;
		
		// Sprawdzenie poprawności tytułu
		$tytul = $_POST['tytul'];
		
		// Sprawdzenie długości tytułu
		if (strlen($tytul)>50){
			$wszystko_ok = false;
			$_SESSION['e_tytul']="Tytuł nie może mieć więcej niż 50 znaków!";
		}
		
		// Sprawdzenie poprawności ISBN
		$isbn = $_POST['isbn'];
		
		// Sprawdzenie długości ISBN
		if ((strlen($isbn)<13) || (strlen($isbn)>13)){
			$wszystko_ok = false;
			$_SESSION['e_isbn']="Numer ISBN musi się składać z 13 cyfr!";
		}
		
		if(ctype_digit($isbn)==false){
			$wszystko_ok = false;
			$_SESSION['e_isbn']="ISBN może składać się tylko z cyfr.";
		}
		
		// Sprawdzenie poprawności roku
		$rok = $_POST['rok'];
		
		// Sprawdzenie długości roku
		if ((strlen($rok)<4) || (strlen($rok)>4)){
			$wszystko_ok = false;
			$_SESSION['e_rok']="Rok musi się składać z 4 cyfr!";
		}
		
		if(ctype_digit($rok)==false){
			$wszystko_ok = false;
			$_SESSION['e_rok']="Rok wydania może składać się tylko z cyfr.";
		}
		
		// Sprawdzenie poprawności stron
		$strony = $_POST['strony'];
		
		// Sprawdzenie długości stron
		if (strlen($strony)>4){
			$wszystko_ok = false;
			$_SESSION['e_strony']="Ilość stron nie może się składać z więcej niż 4 cyfr!";
		}
		
		if(ctype_digit($strony)==false){
			$wszystko_ok = false;
			$_SESSION['e_strony']="Ilość stron może składać się tylko z cyfr.";
		}
		
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
				// Czy ISBN już istnieje?
				$rezultat = $polaczenie->query("SELECT id FROM ksiazka WHERE isbn='$isbn'");
				if(!$rezultat) throw new Exception($polaczenie->error);
				$ile_ksiazek = $rezultat->num_rows;
				if($ile_ksiazek>0) {
					$wszystko_ok = false;
					$_SESSION['e_isbn']="Książka o tym numerze ISBN znajduje się już w bazie.";
					$rezultat->free_result();
				}
				// Inserty do bazy (tabele ksiazka i egzemplarz)
				if($wszystko_ok==true) {
					$autor = $_POST['autor'];
					$gatunek = $_POST['gatunek'];
					$wydawnictwo = $_POST['wydawnictwo'];
					$ilosc = $_POST['ilosc'];
					if($polaczenie->query("INSERT INTO ksiazka VALUES (NULL, '$autor', '$gatunek', '$wydawnictwo', '$tytul', '$isbn', '$rok', '$strony')")) {
						$rezultat = $polaczenie->query("SELECT id FROM ksiazka WHERE isbn='$isbn'");
						if(!$rezultat) throw new Exception($polaczenie->error);
						$wiersz = $rezultat->fetch_array();
						$idk = $wiersz[0];
						while ($ilosc > 0) {
							$ilosc--;
							if($polaczenie->query("INSERT INTO egzemplarz VALUES (NULL, '$idk', '1')")) {} else {
								throw new Exception($polaczenie->error);
							}
						}
						$rezultat->free_result();
					} else {
						throw new Exception($polaczenie->error);
					}
				}
				$polaczenie->close();
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
	<title>Biblioteka - dodawanie książek</title>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
	
	<div class="inputbox"><!-- Formularz dodawania nowych książek -->
		<form method="post">
			
			Nazwisko autora:<br>
			<select name="autor">
				<?php
					require_once "connect.php";
					mysqli_report(MYSQLI_REPORT_STRICT);
					try {
						$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
						if($polaczenie->connect_errno!=0) {
							throw new Exception(mysqli_connect_errno());
						} else {
							mysqli_query($polaczenie, "SET CHARSET utf8");
							mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
							if($rezultat = $polaczenie->query("SELECT * FROM autor")) {
								while($wiersz = $rezultat->fetch_array()) {	// Odczytywanie i wyświetlanie danych z bazy w pętli.
									$ida = $wiersz[0];
									$nazwisko = $wiersz[1];
									echo '<option value="'.$ida.'">'.$nazwisko.'</option>';
								}
							} else {
								throw new Exception($polaczenie->error);
							}
							$rezultat->free_result();
						}
					}
					catch(Exception $e) {
						echo '<span class="error">Błąd serwera!</span>';
						// echo '<br />Informacja deweloperska: '.$e;
					}
				?>
			</select><br>
			
			Wydawnictwo:<br>
			<select name="wydawnictwo">
				<?php
					if($rezultat = $polaczenie->query("SELECT * FROM wydawnictwo")) {
						while($wiersz = $rezultat->fetch_array()) {	// Odczytywanie i wyświetlanie danych z bazy w pętli.
							$ida = $wiersz[0];
							$wydawnictwo = $wiersz[1];
							echo '<option value="'.$ida.'">'.$wydawnictwo.'</option>';
						}
					} else {
						throw new Exception($polaczenie->error);
					}
					$rezultat->free_result();
				?>
			</select><br>
			
			
			Gatunek:<br>
			<select name="gatunek">
				<?php
					if($rezultat = $polaczenie->query("SELECT * FROM gatunek")) {
						while($wiersz = $rezultat->fetch_array()) {	// Odczytywanie i wyświetlanie danych z bazy w pętli.
							$ida = $wiersz[0];
							$gatunek = $wiersz[1];
							echo '<option value="'.$ida.'">'.$gatunek.'</option>';
						}
					} else {
						throw new Exception($polaczenie->error);
					}
					$rezultat->free_result();
					$polaczenie->close();	// Zamknięcie połączenia.
				?>
			</select><br>
			
			Ilość egzemplarzy:<br>
			<select name="ilosc">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
			</select><br>
			
			Tytuł: <br> <input type="text" name="tytul" /><br>
			<?php
				if(isset($_SESSION['e_tytul'])) {
					echo '<div class="error">'.$_SESSION['e_tytul'].'</div>';
					unset($_SESSION['e_tytul']);
				}
			?>
			
			ISBN: <br> <input type="text" name="isbn" /><br>
			<?php
				if(isset($_SESSION['e_isbn'])) {
					echo '<div class="error">'.$_SESSION['e_isbn'].'</div>';
					unset($_SESSION['e_isbn']);
				}
			?>
			
			Rok wydania: <br> <input type="text" name="rok" /><br>
			<?php
				if(isset($_SESSION['e_rok'])) {
					echo '<div class="error">'.$_SESSION['e_rok'].'</div>';
					unset($_SESSION['e_rok']);
				}
			?>
			
			Ilość stron: <br> <input type="text" name="strony" /><br>
			<?php
				if(isset($_SESSION['e_strony'])) {
					echo '<div class="error">'.$_SESSION['e_strony'].'</div>';
					unset($_SESSION['e_strony']);
				}
			?>
			
			<input type="submit" value="Dodaj książkę">
			
		</form>
	</div>
	
</body>
</html>