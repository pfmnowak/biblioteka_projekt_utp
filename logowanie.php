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
		<a href="index.php" style="text-decoration: none;"><h2 class="logo">Biblioteka<i class="icon-book-1"></i></h2></a>
		<br><a href="index.php" class="link">Powrót na stronę główną</a>
	</header>
	
	<div class="inputbox"><!-- Formularz logowania -->
		<form action="zalogujb.php" method="post" >
			Login: <br><input type="text" name="login"><br>
			Hasło: <br><input type="password" name="haslo"><br><br>
			<input type="submit" value="Zaloguj się" />
		</form>
		<?php
			if (isset($_SESSION['bladb'])) {
				echo '<div class="error">'.$_SESSION['bladb'].'</div>';
			}
		?>
	</div>
	
</body>
</html>