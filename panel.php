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
		<div style="margin-top: 100px;">
			<form method="post" action="wyniki.php">
				<input class="search" type="text" name="pytanie" placeholder="Wyszukiwarka...">
				<input class="searchbtn" type="submit" value="Szukaj">
			</form>
		</div>
	</div>
	
</body>
</html>