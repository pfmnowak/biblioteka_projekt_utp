<?php
	
	session_start();
	
	if(!isset($_SESSION['udanarejestracja'])) {
		header('Location: index.php');
		exit();
	} else {
		unset($_SESSION['udanarejestracja']);
	}
	
	// Usuwanie zmiennych sesyjnych pamiętających dane wpisane w formularzu
	if(isset($_SESSION['fr_imie'])) unset($_SESSION['fr_imie']);
	if(isset($_SESSION['fr_nazwisko'])) unset($_SESSION['fr_nazwisko']);
	if(isset($_SESSION['fr_adres'])) unset($_SESSION['fr_adres']);
	if(isset($_SESSION['fr_telefon'])) unset($_SESSION['fr_telefon']);
	if(isset($_SESSION['fr_email'])) unset($_SESSION['fr_email']);
	if(isset($_SESSION['fr_haslo1'])) unset($_SESSION['fr_haslo1']);
	if(isset($_SESSION['fr_haslo2'])) unset($_SESSION['fr_haslo2']);
	
	// Usuwanie błędów rejestracji
	if(isset($_SESSION['e_imie'])) unset($_SESSION['e_imie']);
	if(isset($_SESSION['e_nazwisko'])) unset($_SESSION['e_nazwisko']);
	if(isset($_SESSION['e_adres'])) unset($_SESSION['e_adres']);
	if(isset($_SESSION['e_telefon'])) unset($_SESSION['e_telefon']);
	if(isset($_SESSION['e_email'])) unset($_SESSION['e_email']);
	if(isset($_SESSION['e_haslo1'])) unset($_SESSION['e_haslo1']);
	if(isset($_SESSION['e_haslo2'])) unset($_SESSION['e_haslo2']);
	if(isset($_SESSION['e_bot'])) unset($_SESSION['e_bot']);
	
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Witamy w gronie czytelników!</title>
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="css/fontello.css" type="text/css"/>
</head>

<body>
	
	<header>
		<a href="index.php" style="text-decoration: none;"><h2 class="logo">Biblioteka<i class="icon-book-1"></i></h2></a>
	</header>
	
	<div class="container">
		<div>
			Dziękujemy za rejestrację w serwisie! Możesz teraz w pełni korzystać ze swojego konta.<br>
			Kartę biblioteczną przypisaną do twojego konta możesz odebrać podczas najbliższej wizyty w bibliotece.<br><br>
			<a href="index.php">Zaloguj się na swoje konto.</a>
		</div>
	</div>
	
</body>
</html>