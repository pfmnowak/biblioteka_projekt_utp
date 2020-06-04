<?php
	
	session_start();
	
	if(!isset($_SESSION['zalogowanyb'])) {
		header('Location: index.php');
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
		
		// Sprawdzenie poprawności nazwiska
		$nazwisko = $_POST['nazwisko'];
		
		// Sprawdzenie długości nazwiska
		if ((strlen($nazwisko)<3) || (strlen($nazwisko)>30)){
			$wszystko_ok = false;
			$_SESSION['e_nazwisko']="Nazwisko musi posiadać od 3 do 30 znaków!";
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
		
		// Zapamiętaj wprowadzone dane
		$_SESSION['fr_imie'] = $imie;
		$_SESSION['fr_nazwisko'] = $nazwisko;
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
				$rezultat = $polaczenie->query("SELECT id FROM bibliotekarz WHERE login='$email'");
				if(!$rezultat) throw new Exception($polaczenie->error);
				$ile_takich_maili = $rezultat->num_rows;
				if($ile_takich_maili>0) {
					$wszystko_ok = false;
					$_SESSION['e_email']="Podany adres e-mail już istnieje.";
				}
				
				if($wszystko_ok==true) {
					if($polaczenie->query("INSERT INTO bibliotekarz VALUES (NULL, '$imie', '$nazwisko', '$email', '$haslo_hash')")) {
						$_SESSION['udanarejestracjab'] = true;
						header('Location: witamyb.php');
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
	<title>Bibliotekarz - załóż konto</title>
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
			
			<input type="submit" value="Zarejestruj się">
			
		</form>
	</div>
	
</body>
</html>