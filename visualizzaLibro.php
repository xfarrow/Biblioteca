<html>
	<head>
		<link href="./css/visualizzaLibro.css" rel="stylesheet" type="text/css">
		<link rel="icon" href="./images/resources/books.ico">
		<title>Visualizza libro</title>
	</head>
	<body background="./images/resources/bib.jpg">
	<div id="center">
		
	<?php
		include 'create_connection.php';
		if(isset($_GET['id_libro']))
			$id_libro = $_GET['id_libro'];
		else
			die("La pagina non riceve alcun libro");
		
		$sql_libro = "SELECT * FROM Libro WHERE id='$id_libro';";
		if(!$result_libro=$conn->query($sql_libro))
			die("Errore: Libro non esistente<br>");
		$row_libro = $result_libro->fetch_assoc();
		
		$sql_scrive = "SELECT id_autore FROM Scrive WHERE id_libro='$id_libro';";
		$result_scrive=$conn->query($sql_scrive);
		
		$sql_inserito = "SELECT * FROM Inserito WHERE id_libro='$id_libro';";
		$result_inserito=$conn->query($sql_inserito);
		$row_inserito = $result_inserito->fetch_assoc();
		
		//Div di copertina 
		
		echo "<div id=\"center-left\">";
			echo "<img src=\"./images/copertine/_".$id_libro.".jpg\" alt=\"Copertina\" height=\"80%\" width=\"65%\">";
		echo "</div>";
		
		//Div di informazioni libro
		echo "<div id=\"center-right\">";
		//Titolo e ISBN
		echo "<br><h1><b>".$row_libro['titolo']."</b></h1><i><h6>ISBN: ".$row_libro['isbn']."</h6></i>";
		
		//Autori
		echo "<b>Autori:</b> ";
		while($row_scrive=$result_scrive->fetch_assoc()){
			echo "  ";
			$id_autore = $row_scrive['id_autore'];
			$sql_autore = "SELECT id_anagrafica FROM Autore WHERE id_autore='$id_autore';";
			$result_autore = $conn->query($sql_autore);
			$row_autore = $result_autore->fetch_assoc();
			$id_anagrafica = $row_autore['id_anagrafica'];
			$sql_anagrafica = "SELECT firstname,lastname FROM Anagrafica WHERE id='$id_anagrafica';";
			$result_anagrafica = $conn->query($sql_anagrafica);
			$row_anagrafica = $result_anagrafica->fetch_assoc();
			echo "<a href=\"./profilo.php?id=".$id_anagrafica."\">";
			echo $row_anagrafica['firstname']." ".$row_anagrafica['lastname'];
			echo "</a>";
		}
		
		//Altre info
		echo "<br><b>Copertina:</b> ".$row_libro['copertina']."<br>";
		echo "<b>Tipo</b>: ".$row_libro['tipo']."<br>";
		echo "<b>Lingua</b>: ";
			if($row_libro['lingua']=="IT")
				echo "<img src=\"./images/resources/italian_flag.png\" alt=\"Italiano\" width=\"25\" height=\"25\"> ";
			else if($row_libro['lingua']=="EN")
				echo "<img src=\"./images/resources/english_flag.png\" alt=\"Inglese\" width=\"25\" height=\"25\"> ";
			else if($row_libro['lingua']=="ES")
				echo "<img src=\"./images/resources/spanish_flag.png\" alt=\"Spagnolo\" width=\"25\" height=\"25\"> ";	
			else if($row_libro['lingua']=="FR")
				echo "<img src=\"./images/resources/french_flag.png\" alt=\"Francese\" width=\"25\" height=\"25\"> ";
			else if($row_libro['lingua']=="DE")
				echo "<img src=\"./images/resources/german_flag.png\" alt=\"Tedesco\" width=\"25\" height=\"25\"> ";
			else if($row_libro['lingua']=="PL")
				echo "<img src=\"./images/resources/polish_flag.png\" alt=\"Polacco\" width=\"25\" height=\"25\"> ";
			
		echo"<br><b>Scritto a</b> ".$row_libro['citta']."<br>";
		echo "<b>Disponibili:</b> ".$row_libro['quantita']."<br><br><br>";
		echo "<b>Descrizione</b><br>";
		echo "<i>".$row_libro['descrizione']."</i><br>";
		
		//Tasto di prenotazione
		echo "<form action=\"./prenota.php?id_libro=".$id_libro."\" method=\"POST\" >";
			echo "<div id=\"buttonDIV\">";
				echo "<input type=\"submit\" value=\"Prenota\">";
			echo "</div>";
		echo "</form>";
		echo "</div>";
	?>
	<div>
	<div>
	</body>
	</html>