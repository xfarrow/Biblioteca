<?php
//PAGINA CHE CREA UNA NUOVA SEZIONE
if(!isset($_POST['submit'])) { //Se il tasto non è stato premuto ancora
	session_start();
	include 'create_connection.php';
	
	//Memorizzo il mio permesso(0,1,2) poiché solo gli admin e i moderatori possono accedere alla pagina 
	$myid = $_SESSION['id']; 
	$sql = "SELECT tipo_permesso,descrizione FROM Permesso WHERE id='$myid'; ";
	$result=$conn->query($sql);
	$row = $result->fetch_assoc();
	$mioPermesso = $row['tipo_permesso']; //Memorizzo
	if(!isset($_SESSION['login']) or $mioPermesso==2 or $mioPermesso==3) //Se non sono loggato o se il mio permesso è 3 (utente semplice) o 2 (Autore)
		die("Solo gli admin e i moderatori possono accedere a questa pagina");
	
//CODICE CHE MOSTRA IL FORM 
echo "<html>";
echo "<head>";
		echo"<title> Crea sezione </title>";
		echo"<link rel=\"Shortcut Icon\" href=\"books.ico\">";
	echo "</head>";
	echo "<body>";
	echo "<p><b>Crea nuova sezione</b></p>";
	echo "<form name=\"createIndex\" method=\"post\" action=\"creaSezione.php\">";
		echo "<input type=\"text\" name=\"titolo\" placeholder=\"Titolo\"><br>";
		echo "<input style=\"width:70%; height:75%\" type=\"text\" name=\"body\" placeholder=\"Contenuto\"><br>";
		echo "<input type=\"checkbox\" name=\"check\" value=\"1\"> Cancella sezione invece di crearla (necessario solo titolo)<br>";
		echo "<input type=\"submit\" value=\"Invia\" name=\"submit\">";
		echo "</form>";
	echo "</body>";
echo "</html>";
}else {	
//SCRIPT CHE RECUPERA LE VARIABILI ED ESEGUE OPERAZIONI
	include 'create_connection.php';
	session_start();
	$titolo=$_POST['titolo'];
	$body=$_POST['body'];
	if($titolo=="")
		die("Errore: Titolo mancante");
	$link = "./sezioni/$titolo.html"; //Posizione della nuova pagina
	$link = str_replace(" ","_","$link"); //dato che il browser non interpreta gli spazi e i file con gli spazi sono sgradevoli, li sostituisco con "_"
	
	if(!isset($_POST['check'])){ //Se il checkbox non è selezionato, allora crea sezione
		$sql="SELECT nome FROM Sezione WHERE nome='$titolo';"; //Controlla se il titolo esiste nel db
		$result=$conn->query($sql);
		if($result->num_rows>0){
			exit("Non e' possibile inserire una sezione con titolo \"$titolo\" in quanto gia' esiste");
		}
		
		
		//Ci sono alcuni caratteri che l'html non interprta, che vengono opportunamente sostituiti
		$illegal = array("è","à","ù","ò");
		$toReplace = array("&egrave","&agrave","&ugrave","&ograve");
		$body = str_replace($illegal,$toReplace,$body);
		
		//Crea il codice della pagina
		$total="
			<html> <head> <title> $titolo </title> </head>
			<body> <p><b><h1>$titolo</h1></b></p> $body </body> </html>";
		$fd = fopen($link,"a");
		fwrite($fd,$total);
		fclose($fd);
		
		//Memorizzo chi ha creato questa sezione
		$creatoDa = $_SESSION['id'];
		
		$sql = "INSERT INTO Sezione(link,nome,creatoDa)
		VALUES('$link','$titolo','$creatoDa');";
		if($conn->query($sql)==TRUE)
			echo "$titolo inserito nel database";
		else
			echo "Errore inserimento di $titolo nel database ".$conn->error;
	}else{ //se il checkbox è selezionao, cancella quella sezione
		unlink($link); //cancella il file .html
		$sql="DELETE FROM Sezione WHERE link='$link'";
		if($conn->query($sql)==TRUE)
			echo "Cancellazione di $titolo avvenuta con successo";
		else
			echo "Errore nella cancellazione di $titolo ".$conn->error;
	}
	$conn->close();
} 
?>



