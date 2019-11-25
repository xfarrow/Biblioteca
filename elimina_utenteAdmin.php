<?php
	//Questo script elimina un utente solo nel caso in cui chi lo elimina è' l'ADMIN (permessi.php)
	//permessi.php genera un link GET 
	//es: /elimina_utenteAdmin.php?id=2
	
	session_start();
	if(!isset($_SESSION['login']))
		die("Solo gli Admin possono eliminare un altro utente! ");
	include 'create_connection.php';
	$myid = $_SESSION['id']; //Memorizzo il mio ID
	$sql = "SELECT tipo_permesso,descrizione FROM Permesso WHERE id='$myid'; ";
	$result=$conn->query($sql);
	$row = $result->fetch_assoc();
	$mioPermesso = $row['tipo_permesso']; //Memorizzo il mio tipo di permesso (0,1,2)
	
	if($mioPermesso!=0) //Se chi prova a cancellare non è un Admin
		die("Solo gli Admin possono eliminare un altro utente! ");
	
	$id_da_cancellare = $_GET['id']; //id della persona da cancellare
	
	//Per una questione di sicurezza impedisco che un admin possa cancellare un altro admin (comunque in permessi.php il link non compare, ma per sicurezza nel caso inviino manualmente una GET)
	$sql = "SELECT tipo_permesso FROM Permesso WHERE id='$id_da_cancellare'; ";
	$result=$conn->query($sql);
	$row = $result->fetch_assoc();
	$suoPermesso = $row['tipo_permesso']; //Memorizzo il permesso dell'utente che si vuole cancellare
	if($suoPermesso==0)
		die("Non puoi cancellare un altro Admin! ");
	
	//Istruzioni per eliminare l'utente
	$sql="DELETE FROM Accesso WHERE id='$id_da_cancellare';";
	$sql=$sql."DELETE FROM Anagrafica WHERE id='$id_da_cancellare';";
	$sql=$sql."DELETE FROM Permesso WHERE id='$id_da_cancellare';";
	 if($conn->multi_query($sql)==TRUE){
		 echo "Utente numero $id_da_cancellare cancellato con successo<br>";
	 }else{
		 echo "Per qualche motivo non riusciamo a cancellare l'utente $id_da_cancellare<br>".$conn->error;
	 }
	 $conn->close();
	 
	//Elimina immagine del profilo
	if(is_file("./images/profile/_".$id_da_cancellare.".jpg")){
	$currentPath = getcwd(); //Path attuale
	$imagePath = $currentPath."/images/profile/_".$id_da_cancellare.".jpg"; //Path immagine
	unlink($imagePath);
	}
?>