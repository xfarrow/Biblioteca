<html>
<head>
<link href="./css/columns.css" rel="stylesheet" type="text/css">
<link rel="icon" href="./images/resources/books.ico">
<title>Home</title>
</head>
<body> 
<div class="one">
<p>Sezioni</p>

<?php
	include 'create_connection.php';
	session_start();
	
	//Memorizzo il mio permesso(0,1,2) poiché solo gli admin e i moderatori possono accedere alla pagina "creaSezione"
	if(isset($_SESSION['login'])){ //verifico se è loggato, perché se non lo è, è inutile controllare il permesso
		$myid = $_SESSION['id']; 
		$sql = "SELECT tipo_permesso,descrizione FROM Permesso WHERE id='$myid'; ";
		$result=$conn->query($sql);
		$row = $result->fetch_assoc();
		$mioPermesso = $row['tipo_permesso']; //Memorizzo
		if($mioPermesso<2) //Se non è loggato e non è un moderatore/admin non deve vedere il link
			echo "<a href=\"creaSezione.php\">Crea nuova sezione</a><br>";
	}
	
	if($exists){ //se il db esiste (variabile contenuta in create_connection.php
		$sql = "SELECT link,nome FROM Sezione";
		$result=$conn->query($sql);
		while($row=$result->fetch_assoc()){
			echo " <a href =".$row['link']. ">" .$row['nome']."</a> <br>";
		}
	}
	$conn->close();
?>
</div>
<div class="two">
<p><h2> Home biblioteca </h2></p>
<form action="" method="POST">
<input type="text" name="inputSearch" placeholder="Cerca" ><button name="search_button" class="button button3">Invia</button>
</form>
<?php
	//Script di ricerca utente
	include 'create_connection.php';
	echo "<div align=\"left\" class=\"heart\">";
		
		if(isset($_POST['inputSearch'])){
			$totalString = $_POST['inputSearch']; //La stringa che è scritta direttamente nel box
			
			
			$pieces = explode(" ",$totalString); //Spezza in sottostringhe quando c'è uno spazio http://php.net/manual/en/function.explode.php
			$final = end($pieces); //Prende l'ultima parola per una questione di controllo nel ciclo while dopo
			
			/*
				Cicla per 5 volte (il che significa che non prenderà in considerazione la sesta parola) e aggiunge all'OR
				i valori contenuti nella sottostringa, perché se non lo facessi, potrebbe succedere
				inputSearch: Alessandro Ferro
				SELECT id,firstname,lastname FROM Anagrafica WHERE firstname='Alessandro Ferro' OR lastname='Alessandro Ferro'
				e non troverà nulla, ma in realtà c'è sia Alessandro che Ferro nel DB.
				
				In questo modo fa:
				SELECT id,firstname,lastname FROM Anagrafica WHERE firstname='Alessandro Ferro' OR lastname='Alessandro Ferro'
				OR firstname='Alessandro' OR lastname='Ferro'
				e quindi trova l'elemento
				
				WARNING: In questo modo funziona su una sola tabella. Per farlo funzionare su più tabelle dovremmo in qualche
					modo unire le due tabelle. Ad esempio se vogliamo fare la ricerca anche sull'email dovremmo creare una tabella
					id,firstname,lastname,email
					
			*/
			
			$sql = "SELECT id,firstname,lastname FROM Anagrafica WHERE firstname='$totalString' OR lastname='$totalString'";
			for($i=0;$i<5;$i++){ 
				$sql=$sql." OR firstname= '".$pieces[$i]."' OR lastname='".$pieces[$i]."'";
				if($pieces[$i]==$final) //Se non facessi questo controllo va a finire che cicla 5 volte anche se la stringa contiene solo 2/3 valori e non trova l'indice
					$i=5;
			}
			$sql = $sql.";"; //Aggiungo il punto e virgola
			$result = $conn->query($sql);
			//Stampo i valori
			while($row=$result->fetch_assoc()){
				echo "<p>";
				echo "<a href=\"./profilo.php?id=".$row['id']."\" style=\"bottom:50%;\">";
				if(is_file("./images/profile/_".$row['id'].".jpg")) //Verifica se c'è l'immagine di profilo
					echo "<img src=\" .\images\profile\_".$row['id'].".jpg"."\" width=\"65\" height=\"65\" style=\"float:left;\">";
				else
					echo "<img src=\" .\images\profile\unknow.png\" width=\"65\" height=\"65\" style=\"float:left;\">";
				echo $row['firstname']." ".$row['lastname']."<br><br><br>";
				echo "</p>";
				echo "</a>";
			}
		}else{
			//Visualizza le novità se non è stato premuto il bottone
			echo "<b>Novit&agrave</b><br><br>";
			$sql = "SELECT id,titolo FROM Libro ORDER BY id DESC LIMIT 4;";
			$result = $conn->query($sql);
			while($row = $result->fetch_assoc()){
				echo "<a href=\"visualizzaLibro.php?id_libro=".$row['id']."\">";
				//if(is_file("./images/copertine/_".$row['id'].".jpg"))
					echo "<img src=\"./images/copertine/_".$row['id'].".jpg\" height=\"85px\" width=\"70px\" align=\"middle\">";
		
				echo $row['titolo']."</a><br><br>";
			}
		}
			echo "</div>";
?>
</div>
<div class="three">
<p>Link utili</p>
<?php
// Il session_start() si trova prima, quindi qui non è necessario
include 'create_connection.php';
if(!isset($_SESSION['login'])){ //Se non si è loggati
	echo "<a href=\"login.html\">Login</a><br>";
	echo "<a href=\"iscrizione.html\">Iscrizione</a><br>";
	echo "<a href=\"amministrazioneServer.php\">Amministrazione server </a><br>";
	
	//Visualizza il numero di utenti registrati in TOTALE 
	$sql = "SELECT id FROM Accesso;"; 
	$result = $conn->query($sql);
	$reg = $result->num_rows - 1; //Il -1 è per togliere il superadmin
	echo "<h6>Utenti registrati: ".$reg."</h6>";
	
}else{ //Se si è loggati
	$id = $_SESSION['id']; 
	$sql = "SELECT tipo_permesso,descrizione FROM Permesso WHERE id='$id'; ";
	$result=$conn->query($sql);
	$row = $result->fetch_assoc();
	$mioPermesso = $row['tipo_permesso']; //Memorizzo il mio tipo di permesso (0,1,2,3)
	echo "<a href=\"profilo.php?id=".$id."\">Mio profilo</a><br>"; //Questa pagina vuole un link GET
	echo "<a href=\"miePrenotazioni.php\">Prenotazioni</a><br>";
	echo "<a href=\"permessi.php\">Visualiza permessi</a><br>";
	
	//Se è un autore gli do la possibilità di creare un libro
	if($mioPermesso==2)
		echo "<a href=\"inserisciLibro.php\">Inserisci un libro</a><br>";
	
	echo "<a href=\"logout.php\">Logout</a><br>";
	echo "<a href=\"unsubscribe.php\">Disiscriviti</a><br>";
	//Visualiza le credenziali dell'utente che ha fatto accesso
	$sql = "SELECT firstname,lastname FROM Anagrafica WHERE id='$id' ;";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	echo "<h6>".$row['firstname']." ".$row['lastname']."</h6>";
	$conn->close();
}
?>
</div>
</body>
</html>