<html>
	<head>
		<link rel="icon" href="./images/resources/books.ico">
	</head>
	<body>
<?php
	session_start();
	include 'create_connection.php';
	if(!isset($_SESSION['login'])) //Se non sei loggato
		die("Solo gli iscritti possono prenotare un libro<br>");
		
	$id_libro=$_GET['id_libro'];
	$myID = $_SESSION['id'];
	
	//Sottrazione dei numeri di libri disponibili
	$sql = "SELECT quantita FROM Libro WHERE id='$id_libro';";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$quantita = $row['quantita'] - 1;
	if($quantita == -1) //ovvero se la quantita iniziale è 0
		die("Non disponibile");
	
	$sql = "INSERT INTO Prenota(id_utente,id_libro,data)
			VALUES('$myID','$id_libro',NOW());";
	$sql=$sql."UPDATE Libro SET quantita='$quantita' WHERE id='$id_libro';";
	if($conn->multi_query($sql))
		echo "Prenotazione avvenuta";
	else
		echo "Errore prenotazione";
	$conn->close();
	header("Location: ./visualizzaLibro.php?id_libro=".$id_libro);
?>
</body>
</html>
	