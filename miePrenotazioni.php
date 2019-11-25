<html>
	<head>
		<link href="./css/bootstrap.css" rel="stylesheet" type="text/css"> <!-- CSS di  Bootstrap v3.3.7 -->
		<link rel="icon" href="./images/resources/books.ico">
	</head>
	<body>
	<table class="table">
<?php
	session_start();
	include 'create_connection.php';
	if(!isset($_SESSION['login'])) //Se non sei loggato
		die("Solo gli iscritti possono accedere qui<br>");
	$myID = $_SESSION['id'];
	echo "
		<thead>
			<tr>
				<th>ID Prenotazione</th>
				<th>Titolo</th>
				<th>ISBN</th>
				<th>Data</th>
			</tr>
		</thead>
		";
	$sql = "SELECT Prenota.id_prenotazione,Prenota.data,Libro.titolo,Libro.isbn FROM Prenota JOIN Libro ON Prenota.id_libro = Libro.id WHERE Prenota.id_utente='$myID';";
	if(!$result=$conn->query($sql))
		die("Errore".$conn->error);
	if($result->num_rows==0)
		die("Non hai prenotazioni");

	echo "<tbody>";
	while($row=$result->fetch_assoc()){
		echo "<tr>";
			echo "<td>".$row['id_prenotazione']."</td>";
			echo "<td>".$row['titolo']."</td>";
			echo "<td>".$row['isbn']."</td>";
			echo "<td>".$row['data']."</td>";
		echo "</tr>";
	}
	echo "</tbody>";
	$conn->close();
?>
</table>
</body>
</html>
	