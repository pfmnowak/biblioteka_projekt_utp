<?php
	
	session_start();
	
	if(isset($_SESSION['zalogowany']) && ($_SESSION['zalogowany'])==true) {
		header('Location: panel.php');
		exit();
	}
	if(isset($_SESSION['zalogowanyb']) && ($_SESSION['zalogowanyb'])==true) {
		header('Location: panelb.php');
		exit();
	}
	
	if(isset($_POST['email'])) { // Czy wysłano formularz.
		
		// Udana walidacja?
		$wszystko_ok = true;
		
		// Sprawdzenie poprawności imienia
		$imie = $_POST['imie'];
		
		// Sprawdzenie długości imienia
		if ((strlen($imie)<3) || (strlen($imie)>20)){
			$wszystko_ok = false;
			$_SESSION['e_imie']="Imię musi posiadać od 3 do 20 znaków!";
		}
		
		//Sprawdzenie poprawności nazwiska
		$nazwisko = $_POST['nazwisko'];
		
		// Sprawdzenie długości nazwiska
		if ((strlen($nazwisko)<3) || (strlen($nazwisko)>30)){
			$wszystko_ok = false;
			$_SESSION['e_nazwisko']="Nazwisko musi posiadać od 3 do 30 znaków!";
		}
		
		//Sprawdzenie poprawności adresu
		$adres = $_POST['adres'];
		
		// Sprawdzenie długości adresu
		if ((strlen($adres)<4) || (strlen($adres)>30)){
			$wszystko_ok = false;
			$_SESSION['e_adres']="Adres musi posiadać od 4 do 30 znaków!";
		}
		
		//Sprawdzenie poprawności telefonu
		$telefon = $_POST['telefon'];
		
		// Sprawdzenie długości telefonu
		if ((strlen($telefon)<9) || (strlen($telefon)>9)){
			$wszystko_ok = false;
			$_SESSION['e_telefon']="Numer telefonu musi się składać z 9 cyfr!";
		}
		
		if(ctype_digit($telefon)==false){
			$wszystko_ok = false;
			$_SESSION['e_telefon']="Telefon może składać się tylko z cyfr.";
		}
		
		// Sprawdzenie poprawności maila
		$email = $_POST['email'];
		$email2 = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if ((filter_var($email2, FILTER_VALIDATE_EMAIL)==false) || ($email2!=$email)){
			$wszystko_ok = false;
			$_SESSION['e_email']="Nieprawidłowy adres e-mail.";
			}
		
		// Sprawdzenie poprawności haseł
		$haslo1 = $_POST['haslo1'];
		$haslo2 = $_POST['haslo2'];
		
		if ((strlen($haslo1)<8) || (strlen($haslo1)>20)){
			$wszystko_ok = false;
			$_SESSION['e_haslo']="Hasło musi posiadać od 8 do 20 znaków!";
		}
		
		if ($haslo2!=$haslo1){
			$wszystko_ok = false;
			$_SESSION['e_haslo']="Hasła muszą być takie same!";
		}
		
		$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);
		
		// CAPTCHA
		$sekret = "my_secret_key";
		$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
		
		$odpowiedz = json_decode($sprawdz);
		if($odpowiedz->success==false){
			$wszystko_ok = false;
			$_SESSION['e_bot']="Potwierdź, że nie jesteś botem!";
		}
		
		// Zapamiętaj wprowadzone dane
		$_SESSION['fr_imie'] = $imie;
		$_SESSION['fr_nazwisko'] = $nazwisko;
		$_SESSION['fr_adres'] = $adres;
		$_SESSION['fr_telefon'] = $telefon;
		$_SESSION['fr_email'] = $email;
		$_SESSION['fr_haslo1'] = $haslo1;
		$_SESSION['fr_haslo2'] = $haslo2;
		
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
				// Czy email już istnieje?
				$rezultat = $polaczenie->query("SELECT id FROM czytelnik WHERE email='$email'");
				if(!$rezultat) throw new Exception($polaczenie->error);
				$ile_takich_maili = $rezultat->num_rows;
				if($ile_takich_maili>0) {
					$wszystko_ok = false;
					$_SESSION['e_email']="Podany adres e-mail już istnieje.";
				}
				
				// Czy telefon już istnieje?
				$rezultat = $polaczenie->query("SELECT id FROM czytelnik WHERE telefon='$telefon'");
				if(!$rezultat) throw new Exception($polaczenie->error);
				$ile_takich_telefonow = $rezultat->num_rows;
				if($ile_takich_telefonow>0) {
					$wszystko_ok = false;
					$_SESSION['e_telefon']="Podany telefon już istnieje.";
				}
				
				if($wszystko_ok==true) {
					if($polaczenie->query("INSERT INTO czytelnik VALUES (NULL, '$imie', '$nazwisko', '$adres', '$email', '$haslo_hash', '$telefon')")) {
						$_SESSION['udanarejestracja'] = true;
						header('Location: witamy.php');
					} else {
						throw new Exception($polaczenie->error);
					}
				}
				$polaczenie->close();
			}
		}
		catch(Exception $e) {
			echo '<span class="error">Błąd serwera!</span>';
			//echo '<br />Informacja deweloperska: '.$e;
		}
	}
	
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Biblioteka - załóż swoje konto!</title>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="css/fontello.css" type="text/css"/>
</head>

<body>
	
	<header>
		<a href="index.php" style="text-decoration: none;"><h2 class="logo">Biblioteka<i class="icon-book-1"></i></h2></a>
		<br><a href="index.php" class="link">Powrót na stronę główną</a>
	</header>
	
	<div class="inputbox"><!-- Formularz rejestracji -->
		<form method="post">
			
			Imię: <br> <input type="text" value="<?php
				if(isset($_SESSION['fr_imie'])) {
					echo $_SESSION['fr_imie'];
					unset($_SESSION['fr_imie']);
				}
			?>" name="imie" /><br>
			<?php
				if(isset($_SESSION['e_imie'])) {
					echo '<div class="error">'.$_SESSION['e_imie'].'</div>';
					unset($_SESSION['e_imie']);
				}
			?>
			
			Nazwisko: <br> <input type="text" value="<?php
				if(isset($_SESSION['fr_nazwisko'])) {
					echo $_SESSION['fr_nazwisko'];
					unset($_SESSION['fr_nazwisko']);
				}
			?>" name="nazwisko" /><br>
			<?php
				if(isset($_SESSION['e_nazwisko'])) {
					echo '<div class="error">'.$_SESSION['e_nazwisko'].'</div>';
					unset($_SESSION['e_nazwisko']);
				}
			?>
			
			Adres: <br> <input type="text" value="<?php
				if(isset($_SESSION['fr_adres'])) {
					echo $_SESSION['fr_adres'];
					unset($_SESSION['fr_adres']);
				}
			?>" name="adres" /><br>
			<?php
				if(isset($_SESSION['e_adres'])) {
					echo '<div class="error">'.$_SESSION['e_adres'].'</div>';
					unset($_SESSION['e_adres']);
				}
			?>
			
			Telefon: <br> <input type="text" value="<?php
				if(isset($_SESSION['fr_telefon'])) {
					echo $_SESSION['fr_telefon'];
					unset($_SESSION['fr_telefon']);
				}
			?>" name="telefon" /><br>
			<?php
				if(isset($_SESSION['e_telefon'])) {
					echo '<div class="error">'.$_SESSION['e_telefon'].'</div>';
					unset($_SESSION['e_telefon']);
				}
			?>
			
			E-mail: <br> <input type="email" value="<?php
				if(isset($_SESSION['fr_email'])) {
					echo $_SESSION['fr_email'];
					unset($_SESSION['fr_email']);
				}
			?>" name="email" /><br>
			<?php
				if(isset($_SESSION['e_email'])) {
					echo '<div class="error">'.$_SESSION['e_email'].'</div>';
					unset($_SESSION['e_email']);
				}
			?>
			
			Podaj hasło: <br> <input type="password" value="<?php
				if(isset($_SESSION['fr_haslo1'])) {
					echo $_SESSION['fr_haslo1'];
					unset($_SESSION['fr_haslo1']);
				}
			?>" name="haslo1" /><br>
			<?php
				if(isset($_SESSION['e_haslo'])) {
					echo '<div class="error">'.$_SESSION['e_haslo'].'</div>';
					unset($_SESSION['e_haslo']);
				}
			?>
			
			Powtórz hasło: <br> <input type="password" value="<?php
				if(isset($_SESSION['fr_haslo2'])) {
					echo $_SESSION['fr_haslo2'];
					unset($_SESSION['fr_haslo2']);
				}
			?>" name="haslo2" /><br>
			
			<div class="g-recaptcha" data-sitekey="my_site_key"></div>
			<?php
				if(isset($_SESSION['e_bot'])) {
					echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
					unset($_SESSION['e_bot']);
				}
			?>
			<br>
			
			<input type="submit" value="Zarejestruj się">
			
		</form>
	</div>
	
</body>
</html>