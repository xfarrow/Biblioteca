<?php
	//Disiscrive l'utente
	session_start();
	include 'create_connection.php';
	$id = $_SESSION['id']; //Creata in checkIscrizione
	$sql="DELETE FROM Accesso WHERE id='$id';";
	$sql=$sql."DELETE FROM Anagrafica WHERE id='$id';";
	$sql=$sql."DELETE FROM Permesso WHERE id='$id';";
	 if($conn->multi_query($sql)==TRUE){
		 echo "Utente numero $id cancellato con successo<br>";
	 }else{
		 echo "Per qualche motivo non riusciamo a cancellarti :-(<br>".$conn->error;
	 }
	 $conn->close();
	 
	 //Elimina immagine di prfilo
	if(is_file("./images/profile/_".$id.".jpg")){
		$currentPath = getcwd(); //Path attuale
		$imagePath = $currentPath."/images/profile/_".$id.".jpg"; //Path immagine
		unlink($imagePath);
	}
	include 'logout.php'; //Se si cancella deve anche disconnettersi
?>