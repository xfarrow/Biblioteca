<?php
//File che riceve i dati dal form di iscrizione e li inserisce nel database
$email = $_POST['email'];
$passwordFRM = $_POST['psw'];
$nome = $_POST['nome'];
$cognome = $_POST['cognome'];
if($email=="" or $passwordFRM=="" or $nome=="" or $cognome==""){
	die("Qualche dato è mancante. Verifica la correttezza delle informazioni");
}
$passwordFRM = sha1($passwordFRM); //Cripta in sha1 la password
include 'create_connection.php';
        $sql="SELECT email FROM Accesso WHERE email='$email';";
        $result=$conn->query($sql);
        if($result->num_rows>0){
        die("Errore, l'e-mail gia' esiste");
        }
        $sql="INSERT INTO Anagrafica(firstname,lastname) 
        VALUES('$nome','$cognome');";
        $sql=$sql."INSERT INTO Accesso(email,password) 
        VALUES('$email','$passwordFRM');";
		$sql=$sql."INSERT INTO Permesso(tipo_permesso,descrizione)
		VALUES('3','Utente');";
        if($conn->multi_query($sql)==TRUE){
            echo "Dati inseriti correttamente";
			header("Location: ./login.html");
        }else{
            echo "Errore inserimento dati".$conn->error;
        }
    $conn->close();
   ?>

