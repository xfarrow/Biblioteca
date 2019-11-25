<?php
	//Prende i dati di input da "permessi.php"
	include 'create_connection.php';
	session_start();
	
	$myid = $_SESSION['id']; 
	$sql = "SELECT tipo_permesso,descrizione FROM Permesso WHERE id='$myid'; ";
	$result=$conn->query($sql);
	$row = $result->fetch_assoc();
	$mioPermesso = $row['tipo_permesso']; //Memorizzo il mio tipo di permesso (0,1,2,3)
	
	/*
	La foreach cicla tutto l'array associativo, e mentre cicla, memorizza le due informazioni ad ogni indice (l'id e il tipo di permesso) in due variabili
	*/
	// Tipo rappresenta il tipo AL QUALE voglio elevare
	foreach($_POST as $id => $tipo){ 
		
		//Impedisco che si cambi il permesso ad un utente che ha lo stesso Permesso
		//Ad esempio se prima andavo su Permessi.php e cliccavo sul bottone senza fare nient'altro, aggiornava TUTTI (tranne i disabled) gli utenti
		$sql = "SELECT tipo_permesso FROM Permesso WHERE id='$id';";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		if($tipo == $row['tipo_permesso'])
			continue;
		
        //La descrizione che verrà memorizzata nel database	
		if($tipo==0)
			$descr = "Admin";
		else if($tipo==1)
			$descr = "Moderatore";
		else if($tipo==2)
			$descr = "Autore";
		else
			$decr = "Utente";
		
		//Impedisco ad un moderatore(1) di elevare i permessi di un utente(2) ad Admin(0)
		if($mioPermesso>$tipo)
			die("Non hai i permessi giusti per elevare a $descr l'utente $id.");
		
		$sql = "UPDATE Permesso SET tipo_permesso='$tipo',descrizione='$descr' WHERE id='$id';";
		if($conn->query($sql)==TRUE){
            echo "Utente $id aggiornato<br>";
        }else{
            echo "Errore aggiornamento dell'utente $id ".$conn->error;
        }
		
		//Se elevo a Autore deve anche creare l'opportuna tabella
		if($tipo==2){
			$sql = "INSERT INTO Autore(id_anagrafica)
				VALUES('$id');";
			if($conn->query($sql))
				echo " e passato a Autore<br><br>";
			else
				echo "Errore passaggio Autore";
		}
}
$conn->close();
?>

