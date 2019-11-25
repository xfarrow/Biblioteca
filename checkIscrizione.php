<?php
//Controlla se i dati inseriri in accesso.php sono inseriri correttamente
session_start();
$dbname="Progetto";
$email=$_POST['email'];
$psw=$_POST['psw'];
$psw = sha1($psw); //Cripta la password
include 'create_connection.php';
    $sql="SELECT email,password,id FROM Accesso WHERE email='$email' AND password='$psw';";
    $result=$conn->query($sql);
    if($result->num_rows == 0){ //Se non c'è nessuna riga nella nuova tabella creata, significa che email e password non ci sono nel database
    echo "Non sei un utente registrato<br>";
	echo "<a href=\"iscrizione.html\">Iscriviti</a>";
    }else if($result->num_rows == 1) { //Se c'è una riga, significa che email e password esistono. TEORICAMENTE basterebbe solo questo if per convalidare il login, ma vulnerabile alle injection 
		$row=$result->fetch_assoc(); //in row memorizza l'array associativo della riga
		if($row["password"] == $psw and $row["email"] == $email){ //Se quindi trova corrispondenza
			$_SESSION['id'] = $row['id']; //Così gli altri script sanno chi è che ha effettuato l'accesso
			$_SESSION['login']=1;
			header("Location: /Biblioteca/home.php");
			//Azioni di benvenuto qui
		}
	}else{ //num_rows() restituisce o un valore minore di 0 (errore) oppure un valore maggiore di 1 (più utenti con identica username e password).
		die( "Backend query error: Errore query SQL o errore database. num_rows() ritorna un valore non valido.");
    }
    $conn->close();
?>
