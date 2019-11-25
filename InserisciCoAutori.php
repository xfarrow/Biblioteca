<html>
<head>
<link rel="icon" href="./images/resources/books.ico">
<link href="./css/columns.css" rel="stylesheet" type="text/css">
<title>Inserisci co autori</title>
</head>
<body>
<h6><a href="./home.php">Home</a></h6>
<p><h1>Aggiungi co-autori</h1></p>
<?php
	include 'create_connection.php';
	session_start();
	$myID = $_SESSION['id'];
	$sql = "SELECT tipo_permesso,descrizione FROM Permesso WHERE id='$myID'; ";
	$result=$conn->query($sql);
	$row = $result->fetch_assoc();
	$mioPermesso = $row['tipo_permesso']; //Memorizzo
	if(!isset($_SESSION['login']) or $mioPermesso!=2)
		die("Solo gli Autori possono accedere a questa pagina");
	
	if(isset($_GET['id_libro']))
		$id_libro = $_GET['id_libro'];

	function printResult($result,$conn){
		//Stampo i valori
			echo "<div align=\"left\" class=\"heart\">";
			echo "<form name=\"frm2\" action=\"\" method=\"POST\" >"; //form di convalida co-autori
			while($row=$result->fetch_assoc()){
				
				//Controllo il suo permesso (ovvero che è un Autore e quindi visualizzabile)
				$suoID = $row['id'];
				$sql_permesso = "SELECT tipo_permesso FROM Permesso WHERE id='$suoID';";
				$result_permesso = $conn->query($sql_permesso);
				$row_permesso = $result_permesso->fetch_assoc();
				$suoPermesso = $row_permesso['tipo_permesso'];
				if($suoPermesso!=2)//Se non è autore
					continue;
				
				echo "<p>";
				echo "<a href=\"./profilo.php?id=".$suoID."\" style=\"bottom:50%;\">";
				if(is_file("./images/profile/_".$suoID.".jpg")) //Verifica se c'è l'immagine di profilo
					echo "<img src=\" .\images\profile\_".$suoID.".jpg"."\" width=\"65\" height=\"65\" style=\"float:left;\">";
				else
					echo "<img src=\" .\images\profile\unknow.png\" width=\"65\" height=\"65\" style=\"float:left;\">";
				echo $row['firstname']." ".$row['lastname']."</a>&nbsp&nbsp";
				echo "<input type=\"checkbox\" name=\"$suoID\" value=\"1\"><br><br><br>";
				echo "</p>";
			}
			echo "<input type=\"submit\" name=\"end_btn\"> ";
			print " </form> ";
			
	}
	
	echo "
		<form name=\"frm\" action=\"\" method=\"POST\">
		<input type=\"text\" name=\"inputSearch\" placeholder=\"Cerca un co-autore\" ><button name=\"search_button\" class=\"button button3\">Invia</button>";
		echo "</form>";
		if(isset($_POST['inputSearch'])){
			$totalString = $_POST['inputSearch'];
			$pieces = explode(" ",$totalString); //Spezza in sottostringhe quando c'è uno spazio http://php.net/manual/en/function.explode.php
			$final = end($pieces); //Prende l'ultima parola per una questione di controllo nel ciclo while dopo
			$sql = "SELECT id,firstname,lastname FROM Anagrafica WHERE firstname='$totalString' OR lastname='$totalString'";
			for($i=0;$i<5;$i++){ 
				$sql=$sql." OR firstname= '".$pieces[$i]."' OR lastname='".$pieces[$i]."'";
				if($pieces[$i]==$final) //Se non facessi questo controllo va a finire che cicla 5 volte anche se la stringa contiene solo 2/3 valori e non trova l'indice
					$i=5;
			}
			$sql = $sql.";"; //Aggiungo il punto e virgola
			$result = $conn->query($sql);
			printResult($result,$conn);
		}
		
		//Salva i co autori
		if(isset($_POST['end_btn'])){
			$today = date("Y-d-m");
			foreach($_POST as $id => $value){
				//Aggiorna il numero di libri scritti
				if($value==1){ //Faccio questo controllo perchè foreach trova variabili strane, mentre adesso prende il value del checkbox di sopra
				$sql = "SELECT n_libri FROM Autore WHERE id_anagrafica='$id';";
				$result = $conn->query($sql);
				$row = $result->fetch_assoc();
				$libri_scritti = $row['n_libri'] + 1;
				$sql = "UPDATE Autore SET n_libri='$libri_scritti' WHERE id_anagrafica='$id';";
				$conn->query($sql);
				
				//ID AUTORE: (che è diverso da id dell'anagrafica ($id)
				$sql = "SELECT id_autore FROM Autore WHERE id_anagrafica='$id';";
				$result = $conn->query($sql);
				$row = $result->fetch_assoc();
				$id_autore = $row['id_autore'];
				
				//Aggiuge record alla tabella Scrive
				$sql = "INSERT INTO Scrive(id_libro,id_autore,data)
					VALUES('$id_libro','$id_autore','$today');";
					if($conn->query($sql)){
						echo "Co-autore aggiunto";
					}
			}
		}
		}
	echo "</body><html>";
?>