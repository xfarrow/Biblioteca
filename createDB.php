<?php
//Questo file crea il database e le tabelle necessarie.
include 'create_connection.php';
$sql="CREATE DATABASE $dbname";
    if($conn->query($sql)==TRUE){
        echo "Database creato<br>";
        }else{
        die("Errore creazione database ".$conn->error);
        }
	$conn->select_db($dbname); //Sposta la connessione dal database generale al database "Biblioteca"
	
    $sql="CREATE TABLE Anagrafica(
        id INT(5) PRIMARY KEY AUTO_INCREMENT,
        firstname VARCHAR(20) NOT NULL,
        lastname VARCHAR(20) NOT NULL,
		phone_number VARCHAR(20),
		place VARCHAR(30)
        );";

    $sql=$sql."CREATE TABLE Accesso(
        id INT(5) PRIMARY KEY AUTO_INCREMENT,
        email VARCHAR(40) NOT NULL,
        password VARCHAR(100) NOT NULL
        );";

	$sql=$sql."CREATE TABLE Sezione(
		id INT(5) PRIMARY KEY AUTO_INCREMENT,
		link VARCHAR(50) NOT NULL,
		nome VARCHAR(20) NOT NULL,
		creatoDa VARCHAR(5)
	    );";
		
	/* Crea<ione tabella permesso
		0: Admin
		1: Moderatore
		2: Autore
		3: Utente semplice
	*/
	$sql=$sql."CREATE TABLE Permesso(
		id INT(5) PRIMARY KEY AUTO_INCREMENT,
		tipo_permesso INT(2) NOT NULL,
		descrizione VARCHAR(40)
		);";
	
	$sql=$sql."CREATE TABLE Autore(
		id_autore INT(5) PRIMARY KEY AUTO_INCREMENT,
		id_anagrafica INT(5) NOT NULL,
		n_libri INT(3) DEFAULT 0,
		valutazione double DEFAULT 0,
		biografia VARCHAR(100)
		);";
		//todo: Inserire da qualche parte la biografia dell'autore
	
	// Tipo è diverso da genere!
	$sql=$sql."CREATE TABLE Libro(
		id INT(5) PRIMARY KEY AUTO_INCREMENT,
		titolo VARCHAR(25) NOT NULL,
		isbn VARCHAR(20) NOT NULL,
		quantita INT(4) NOT NULL,
		copertina VARCHAR(10),
		tipo VARCHAR(20),
		lingua VARCHAR (3),
		citta VARCHAR(20),
		descrizione VARCHAR(250)
		);";

	$sql=$sql."CREATE TABLE Scrive(
		id INT(5) PRIMARY KEY AUTO_INCREMENT,
		data DATE NOT NULL,
		id_libro INT(5) NOT NULL,
		id_autore INT(5) NOT NULL
		);";
		
	$sql=$sql."CREATE TABLE Inserito(
		id_inserito INT(5) PRIMARY KEY AUTO_INCREMENT,
		id_libro INT(5) NOT NULL,
		id_genere INT(2) NOT NULL
		);";
	
	$sql=$sql."CREATE TABLE Reparto(
		id_genere INT(2) PRIMARY KEY AUTO_INCREMENT,
		genere VARCHAR(15) NOT NULL
		);";
		
	$sql=$sql."CREATE TABLE Prenota(
		id_prenotazione INT(5) PRIMARY KEY AUTO_INCREMENT,
		id_utente INT(5) NOT NULL,
		id_libro INT(5) NOT NULL,
		data DATE NOT NULL
		);";
		
		
	if($result=$conn->multi_query($sql)){
        echo "Tabelle create<br>";
        }else{
        echo "Errore creazione tabelle ".$conn->error;
    }

	while(mysqli_next_result($conn)){;} //PULISCE IL BUFFER ALTRIMENTI VA IN OUT OF SYNC
	
	include 'create_connection.php';
	//CREAZIONE SUPERUSER
	$adminPass = sha1("root");
	$sql="INSERT INTO Anagrafica(firstname,lastname) 
        VALUES('root','root');";
        $sql=$sql."INSERT INTO Accesso(email,password) 
        VALUES('root','$adminPass');";
		$sql=$sql."INSERT INTO Permesso(tipo_permesso,descrizione)
		VALUES('0','Admin');";
		if($conn->multi_query($sql)==TRUE){
        echo "Superutente creato<br>";
        }else{
        echo "Errore creazione superutente ".$conn->error;
        }
	while(mysqli_next_result($conn)){;} 
	//Creazione dei reparti
	$sql="INSERT INTO Reparto(genere)
	VALUES('Rosa');"; //ID 1
	$sql=$sql."INSERT INTO Reparto(genere)
	VALUES('Giallo');"; //ID 2
	$sql=$sql."INSERT INTO Reparto(genere)
	VALUES('Romanzo');"; //ID 3
	$sql=$sql."INSERT INTO Reparto(genere)
	VALUES('Poesie');"; //ID 4
	if($conn->multi_query($sql)==TRUE){
        echo "Reparti creati";
        }else{
        echo "Errore creazione reparti ".$conn->error;
		}
	$conn->close();
?>
