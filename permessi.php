<html>
	<head>
	<!--
		Visualizza le informazioni e i permessi degli utenti 
	-->
	<link rel="icon" href="./images/resources/books.ico">
		<link href="./css/bootstrap.css" rel="stylesheet" type="text/css"> <!-- CSS di  Bootstrap v3.3.7 -->
	</head>
	
	<body>
	<form name="permessi_form" method="POST" action="/Biblioteca/salvaPermessi.php">
	<table class="table">
<?php
	function create_table($id,$email,$nome,$cognome,$tipo,$mioPermesso){ //Funzione che crea una riga della tabella. Riceve come parametro tutte le informazioni dell'utente. Tipo è il tipo dell'utente (0,1,2,3)
	echo "
	<tr>
      <td>$id</td>
      <td>$nome</td>
      <td>$cognome</td>
      <td>$email</td>
	  <td>";
	  if($mioPermesso<$tipo) //Un utente con permessi più bassi non può modificare permessi più alti (e quindi disabilita la casella)
		  echo "<select name=\"$id\">";
	  else
		  echo "<select disabled>";
	  
	  if($tipo==0){
		echo "<option value=\"0\" selected>Admin</option>
			  <option value=\"1\">Moderatore</option>
			  <option value=\"2\">Autore</option>
			  <option value=\"3\">Utente</option>";
	  }else if($tipo==1){
		  echo "<option value=\"0\">Admin</option>
			  <option value=\"1\" selected>Moderatore</option>
			  <option value=\"2\">Autore</option>
			  <option value=\"3\">Utente</option>";
	  }else if($tipo==2){
		  echo "<option value=\"0\">Admin</option>
			  <option value=\"1\">Moderatore</option>
			  <option value=\"2\" selected>Autore</option>
			  <option value=\"3\">Utente</option>";
	}else if($tipo==3){
		echo "<option value=\"0\">Admin</option>
			  <option value=\"1\">Moderatore</option>
			  <option value=\"2\">Autore</option>
			  <option value=\"3\" selected>Utente</option>";
	}
	echo "</select></td>";
	
	//È possibile cancellare un utente SOLO se tu sei un Admin, e non puoi cancellare altri admin
	if($mioPermesso==0 and $tipo!=0){
		echo "
		<td>
		<a href=\"elimina_utenteAdmin.php?id=$id\"><img src=\"trafficAlert.png\"> Elimina</a>
		</td>";
    }else{
		echo "<td></td>"; //Metto un TD vuoto in modo che si abbini esteticamente alla riga nel caso ci fosse "Elimina" prima o dopo questa riga
	}
	echo "</tr>";
	}
	
	//LO SCRIPT INIZIA DA QUA
	session_start();
	if(!isset($_SESSION['login']))
		die("Solo gli utenti registrati possono accedere a questa pagina");
	
	include 'create_connection.php';
	$myid = $_SESSION['id']; //Memorizzo il mio ID
	$sql = "SELECT tipo_permesso,descrizione FROM Permesso WHERE id='$myid'; ";
	$result=$conn->query($sql);
	$row = $result->fetch_assoc();
	$mioPermesso = $row['tipo_permesso']; //Memorizzo il mio tipo di permesso (0,1,2)
	echo "Sei un utente di livello ". $mioPermesso." (".$row['descrizione'].") <br>";
	//if($mioPermesso==2) die();
	
	echo "<thead>
			<tr>
				<th>ID</th>
				<th>Nome</th>
				<th>Cognome</th>
				<th>e-mail</th>
				<th>Permesso</th>
			</tr>
		</thead>
		<tbody>";
	
	//Seleziona le informzioni necessarie dell'utente 
	$sql = "SELECT id,email FROM Accesso;";
	$AccessoResult = $conn->query($sql);
	$sql = "SELECT firstname,lastname FROM Anagrafica;";
	$AnagraficaResult = $conn->query($sql);
	$sql = "SELECT tipo_permesso,descrizione FROM Permesso;";
	$PermessoResult = $conn->query($sql);
	
	//Deve creare una riga alla volta
	while($rowAccesso = $AccessoResult->fetch_assoc()){
		$rowAnagrafica = $AnagraficaResult->fetch_assoc();
		$rowPermesso = $PermessoResult->fetch_assoc();
		$id = $rowAccesso['id'];
		$email = $rowAccesso['email'];
		$nome = $rowAnagrafica['firstname'];
		$cognome = $rowAnagrafica['lastname'];
		$tipo = $rowPermesso['tipo_permesso'];
		create_table($id,$email,$nome,$cognome,$tipo,$mioPermesso);
	}
	echo "</tbody></table>";
	echo "<input type=\"submit\" value=\"Conferma\">";
	echo "</form></body></html>";
	$conn->close();

?>

