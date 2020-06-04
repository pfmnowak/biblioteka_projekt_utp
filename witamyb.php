<?php
	
	session_start();
	
	if(!isset($_SESSION['udanarejestracjab'])) {
		header('Location: index.php');
		exit();
	} else {
		unset($_SESSION['udanarejestracjab']);
	}
	
	// Usuwanie zmiennych sesyjnych pamiętających dane wpisane w formularzu
	if(isset($_SESSION['fr_imie'])) unset($_SESSION['fr_imie']);
	if(isset($_SESSION['fr_nazwisko'])) unset($_SESSION['fr_nazwisko']);
	if(isset($_SESSION['fr_email'])) unset($_SESSION['fr_email']);
	if(isset($_SESSION['fr_haslo1'])) unset($_SESSION['fr_haslo1']);
	if(isset($_SESSION['fr_haslo2'])) unset($_SESSION['fr_haslo2']);
	
	// Usuwanie błędów rejestracji
	if(isset($_SESSION['e_imie'])) unset($_SESSION['e_imie']);
	if(isset($_SESSION['e_nazwisko'])) unset($_SESSION['e_nazwisko']);
	if(isset($_SESSION['e_email'])) unset($_SESSION['e_email']);
	if(isset($_SESSION['e_haslo1'])) unset($_SESSION['e_haslo1']);
	if(isset($_SESSION['e_haslo2'])) unset($_SESSION['e_haslo2']);
	
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Witamy w gronie bibliotekarzy!</title>
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="css/fontello.css" type="text/css"/>
</head>

<body>
	
	<header>
		<a href="index.php" style="text-decoration: none;"><h2 class="logo">Biblioteka<i class="icon-book-1"></i></h2></a>
	</header>
	
	<div class="container">
		<div>
			Dziękujemy za rejestrację w serwisie! Możesz teraz w pełni korzystać ze swojego konta.<br><br>
			<a href="logout.php">Zaloguj się na swoje konto.</a>
		</div>
	</div>
	
</body>
</html>