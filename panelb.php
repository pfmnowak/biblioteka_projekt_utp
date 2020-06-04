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
		
		<div id="ikony"><!-- Linki do podstron -->
			
			<a href="wypozyczanie1.php" class="linkikona">
				<div class="ikona"><p>Wypożyczanie książek</p></div>
			</a>
			
			<a href="dodawanie.php" class="linkikona">
				<div class="ikona"><p>Dodawanie książek</p></div>
			</a>
			
			<div style="clear: both;"></div>
			
			<a href="kontob.php" class="linkikona">
				<div class="ikona"><p>Profil i statystyki</p></div>
			</a>
			
			<a href="rejestracjab.php" class="linkikona">
				<div class="ikona"><p>Rejestracja bibliotekarzy</p></div>
			</a>
			
			<div style="clear: both;"></div>
		</div>
		
	</div>
	
</body>
</html>