<?php
	//Fa il log out dell'utente
	session_start();
	session_unset(); //rimuove le variabili 
	session_destroy(); //distrugge la sessione
	header("Location: /Biblioteca/home.php");
?>