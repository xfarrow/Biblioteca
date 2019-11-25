<html>
	<head>
		<link rel="icon" href="./images/resources/books.ico">
		<link href="./css/inserisciLibro.css" rel="stylesheet" type="text/css">
	</head>
	<body>
<?php
	session_start();
	include 'create_connection.php';
	if(!isset($_SESSION['login'])) //Se non sei loggato
		die("Solo gli autori possono accedere a questa pagina<br>");
	
	$myid = $_SESSION['id']; 
	$sql = "SELECT tipo_permesso,descrizione FROM Permesso WHERE id='$myid'; ";
	$result=$conn->query($sql);
	$row = $result->fetch_assoc();
	$mioPermesso = $row['tipo_permesso']; //Memorizzo il mio tipo di permesso (0,1,2,3)
	if($mioPermesso!=2) //Solo gli autori possono accedere alla pagina
		die("Solo gli autori possono accedere a questa pagina<br>");
	
	//Grafica
	if(!isset($_POST['btn'])){
	echo "<h2>Inserisci un nuovo libro<h2>";
	echo "
	<div class=\"container\">
		<form action=\"inserisciLibro.php\" method=\"POST\" enctype=\"multipart/form-data\">
			<div class=\"row\">
				<div class=\"col-25\">
					<label>Titolo</label>
				</div>
				<div class=\"col-75\">
					<input type=\"text\"  name=\"titolo\" placeholder=\"La divina commedia\" value=\"\" >
				</div>
			</div>
			
			<div class=\"row\">
				<div class=\"col-25\">
					<label>ISBN</label>
				</div>
				<div class=\"col-75\">
					<input type=\"text\"  name=\"ISBN\" placeholder=\"978-88-203-5156-4\" value=\"\" >
				</div>
			</div>
		
		<div class=\"row\">
				<div class=\"col-25\">
					<label>Copertina</label>
				</div>
				<div class=\"col-75\">
					<select type=\"select\" name=\"copertina\">
						<option value=\"Rigida\">Rigida</option>
						<option value=\"Morbida\">Morbida</option>
					</select>
				</div>
			</div>
		
		<div class=\"row\">
				<div class=\"col-25\">
					<label>Tipo</label>
				</div>
				<div class=\"col-75\">
					<select type=\"select\"  name=\"tipo\">
						<option value=\"Libro\">Libro</option>
						<option value=\"Rivista\">Rivista</option>
						<option value=\"Giornale\">Giornale</option>
						<option value=\"Audiolibro\">Audiolibro</option>
						<option value=\"CD\">Compact-disk</option>
					</select>
				</div>
			</div>
			
			<div class=\"row\">
				<div class=\"col-25\">
					<label>Lingua</label>
				</div>
				<div class=\"col-75\">
					<select type=\"select\" name=\"lingua\">
						<option value=\"IT\">Italiano</option>
						<option value=\"EN\">Inglese</option>
						<option value=\"ES\">Spagnolo</option>
						<option value=\"FR\">Francese</option>
						<option value=\"DE\">Tedesco</option>
						<option value=\"PL\">Polacco</option>
					</select>
				</div>
			</div>
		
			<div class=\"row\">
				<div class=\"col-25\">
					<label>Citt&agrave</label>
				</div>
				<div class=\"col-75\">
					<input type=\"text\" name=\"citta\" placeholder=\"Roma\" value=\"\">
				</div>
			</div>
		
			<div class=\"row\">
				<div class=\"col-25\">
					<label>Genere</label>
				</div>
				<div class=\"col-75\">
					<input type=\"checkbox\" name=\"Romanzo\" value=\"3\">Romanzo &nbsp <input type=\"checkbox\" name=\"Poesia\" value=\"4\">Poesia &nbsp <input type=\"checkbox\" name=\"Rosa\" value=\"1\">Rosa &nbsp <input type=\"checkbox\" name=\"Giallo\" value=\"2\">Giallo
				</div>
			</div>
		
			<div class=\"row\">
				<div class=\"col-25\">
					<label>Quantit&agrave</label>
				</div>
				<div class=\"col-75\">
					<input type=\"text\" name=\"quantita\" value=\"\">
				</div>
			</div>
		
			 <div class=\"row\">
				<div class=\"col-25\">
        <label>Descrizione</label>
      </div>
      <div class=\"col-75\">
        <textarea name=\"descrizione\" placeholder=\"Una breve descrizione (max 250 caratteri)\" style=\"height:100px\"></textarea>
      </div>
    </div>
	
	<div class=\"row\">
		<div class=\"col-25\">
					<label>Immagine</label>
			</div>
			<br>
			<input type=\"FILE\" name=\"pic\" accept=\"image/jpeg\">
			
      <input type=\"submit\" value=\"Carica\" name=\"btn\">
    </div>
	</form>
	</div>";
	}else{//isset
		if($_POST['titolo']=="" or $_POST['ISBN']=="")
			die("Il titolo e l'ISBN sono obbligatori");
		else{
			$titolo = $_POST['titolo'];
			$isbn = $_POST['ISBN'];
		}
		$copertina = $_POST['copertina'];
		$tipo = $_POST['tipo'];
		$lingua = $_POST['lingua'];
		$citta = $_POST['citta'];
		$descr = $_POST['descrizione'];
		$quantita = $_POST['quantita'];
		
		//Verifico se l'ISBN esiste già
		$sql = "SELECT isbn FROM Libro WHERE isbn='$isbn';";
		$result = $conn->query($sql);
		if($result->num_rows>0)
			die("L'ISBN $isbn &egrave gi&agrave presente!");
		
		//Aggiorna il numero di libri scritti dall'autore
		$sql = "SELECT n_libri FROM Autore WHERE id_anagrafica='$myid';";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		$libri_scritti = $row['n_libri'] + 1;
		$sql = "UPDATE Autore SET n_libri='$libri_scritti' WHERE id_anagrafica='$myid';";
		$conn->query($sql);
		
		//Inserisce i valori nella tabella Libro
		$sql = "INSERT INTO Libro(titolo,isbn,copertina,tipo,lingua,citta,descrizione,quantita)
			VALUES('$titolo','$isbn','$copertina','$tipo','$lingua','$citta','$descr','$quantita');";
		if(!$conn->query($sql))
			die("ERRORE INSERIMENTO LIBRO<br>");
		
		//Aggiorna tabella "scrive". Per farlo ha bisogno dell'id autore e id libro
		//ID LIBRO:
		$sql = "SELECT id FROM Libro WHERE isbn='$isbn';";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		$id_libro = $row['id'];
		
		//ID AUTORE:
		$sql = "SELECT id_autore FROM Autore WHERE id_anagrafica='$myid';";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		$id_autore = $row['id_autore'];
		
		$sql = "INSERT INTO Scrive(id_libro,id_autore,data)
			VALUES('$id_libro','$id_autore',NOW());"; //NOW() restituise la data di oggi
		if(!$conn->query($sql))
			 die("ERRORE TABELLA SCRIVE<br>".$conn->error);
		
		//Inserimento dei generi letterari - Nell'HTML vano valori apparentemente a caso (3,4,2,1) perchè è il loro ID
		$sql="";
		if(isset($_POST['Romanzo'])){
			$genereID = $_POST['Romanzo'];
			$sql=$sql. "INSERT INTO Inserito(id_libro,id_genere)
					VALUES('$id_libro','$genereID');";
		}
		if(isset($_POST['Poesia'])){
			$genereID = $_POST['Poesia'];
			$sql=$sql. "INSERT INTO Inserito(id_libro,id_genere)
					VALUES('$id_libro','$genereID');";
		}
		if(isset($_POST['Rosa'])){
			$genereID = $_POST['Rosa'];
			$sql=$sql. "INSERT INTO Inserito(id_libro,id_genere)
					VALUES('$id_libro','$genereID');";
		}
		if(isset($_POST['Giallo'])){
			$genereID = $_POST['Giallo'];
			$sql=$sql."INSERT INTO Inserito(id_libro,id_genere)
					VALUES('$id_libro','$genereID');";
		}
		
		if($conn->multi_query($sql))
			echo "Gneri inseriti";
		else
			echo "Errore insermento generi".$conn->error;
		
		//Immagine di copertina
		$img_name = $id_libro.".jpg";  //Il nome dell'immagine è il numero del libro (id libro) + jpg
		$path = "./images/copertine/_".$img_name; //Qui viene aggiunto il trattino basso in quanto le immagini non possono iniziare con un numero, poi è il path di destinazione
		$imagetemp = $_FILES['pic']['tmp_name'];
			if(is_uploaded_file($imagetemp)) {
				if(move_uploaded_file($imagetemp, $path)) { //Salva l'immagine
					echo "Foto caricata con successo.";
				}
			}
		
		//Script che aggiunge co-autori
		header("Location: ./InserisciCoAutori.php?id_libro=".$id_libro);
	}
		$conn->close();
?>
<body>
</html>