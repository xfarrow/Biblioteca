<html>
	<!-- Questa pagina permette di accedere comodamente agli script necessari al corretto funzionamento del server !-->
	<head>
		<title>Pagina riservata</title>
	</head>
	<body>
	<h1><b>Pagina riservata all'amministrazione</b></h1><br>
	Per creare il database e le tabelle necessarie, fai click <a href="createDB.php">qui</a>.<br>
	<b>NOTA:</b>
	<?php
		include 'create_connection.php';
		if($exists==1){ //Variabile in create_connection.php
			echo "Il database $dbname esiste gia'. Non e' necessario clicare il link<br>";
		}
		else{
			echo "Il database $dbname non esiste. Cliccare il collegamento per crearlo<br>";
		}
	?>
	</body>
</html>