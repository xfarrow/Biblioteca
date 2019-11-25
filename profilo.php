<html>
<head>
<link href="./css/bootstrap.css" rel="stylesheet" id="bootstrap-css">
<link rel="icon" href="./images/resources/books.ico">
<style>
	    .details li {
      list-style: none;
    }
    li {
        margin-bottom:10px;
        
    }
	.button {
    background-color: #e6e5e6; /* Grigio */
    border: none;
    color: black;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    -webkit-transition-duration: 0.4s; /* Safari */
    transition-duration: 0.4s;
	}
	.button_shadow {
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
}
	.button:hover{
		background-color: #737373;
	}
	.button:active {
  background-color: #737373;
  transform: translateY(8px);
}

</style>
</head>
<body>
            
						<?php
						/*RICEVE TRAMITE GET L'ID DELL'UTENTE DA VISUALIZZARE
						es /permesso.php?id=1
						*/
						session_start();
						include 'create_connection.php';
						if(!isset($_POST['save'])){
							
						if(!isset($_SESSION['login']))
							die("Solo gli utenti registrati possono accedere a questa pagina");
						
						$mioID = $_SESSION['id']; //ID di chi ha aperto la pagina
						$profileID = $_GET['id']; //ID del proprietario della pagina
						
						//enctype necessario per quando invia anche immagini
						echo "<form name=\"frm\" method=\"POST\" action=\"profilo.php\" enctype=\"multipart/form-data\">
						<div class=\"container\">  
						<a href=\"./home.php\">Home</a>						
							<div class=\"jumbotron\">
							<div class=\"row\">
							<div class=\"col-md-4 col-xs-12 col-sm-6 col-lg-4\">";
						
						/*
							NOTA IMPORTANTE:
								- Le immagini non possono iniziare con un numero, allora le faccio iniziare con un trattino basso (_) DA TENERE IN CONSIDERAZIONE
								- Le immagini sono solo .jpg (se si mette un altro formato non succede nulla comunque)
						*/
						
						$currentPath = getcwd(); //Path attuale
						$imagePath = $currentPath."/images/profile/_".$profileID.".jpg"; //Path immagine
						if(is_file($imagePath)){ //Se l'immagine del profilo non c'è allora mostra l'unknow (immagine della persona sconosciuta)
							echo "<img src=\" .\images\profile\_".$profileID.".jpg"."\" width=\"256\" height=\"256\" alt=\"stack photo\" class=\"img\">"; //Visualizza immagine di profilo
						}else{
							echo "<img src=\".\images\profile\unknow.png\" width=\"256\" height=\"256\" alt=\"stack photo\" class=\"img\"><br>"; //Visualizza unknow
						}
						if($mioID==$profileID) //Se chi visualizza la pagina coincide col proprietario della stessa
								echo "<h6>Cambia immagine</h6> <input type=\"FILE\" name=\"pic\" accept=\"image/jpeg\">"; //Accetta solo jpeg (accept) 
						echo"
                      </div>
                      <div class=\"col-md-8 col-xs-12 col-sm-6 col-lg-8\">
                          <div class=\"container\" style=\"border-bottom:1px solid black\">";
						  
						 //Seleziona le infomazioni da mostrare 
						 $sql = "SELECT firstname,lastname,phone_number,place FROM Anagrafica WHERE id='$profileID';";
						 $result = $conn->query($sql);
						 $row = $result->fetch_assoc();
						  //Mostra nome e cognome
                        echo "<h2>".$row['firstname']." ".$row['lastname']."</h2>";
						echo "
                          </div>
                            <hr>
                          <ul class=\"container details\">";
						  
						  if($row['phone_number']==NULL){ //Se il numero di telefono non c'è nel database
							  if($mioID!=$profileID){ //Se chi sta visualizzando la pagina è un estraneo (non il proprietario della pagina)
								echo "<li></p>Nessun numero di telefono</p></li>";
							  }else{ //Se chi sta visualizzando è il proprietario della pagina allora può modificare le proprie informazioni
								echo "<li><p><input type=\"text\" name=\"phone_number\" placeholder=\"Aggiungi numero\" value=\"\"</p></li>";
							  }
						  }else{
                            echo "<li><p>".$row['phone_number']."</p></li>";
						  }
						  if($row['place']==NULL){ //Stessa cosa di phone_number
							  if($profileID!=$mioID){
							  echo "<li><p>Luogo non disponibile</p></li>";
							  }else{
							   echo "<li><p><input type=\"text\" name=\"place\" placeholder=\"Aggiungi localit&agrave\" value=\"\"></p></li>";
							  }
						 }else{
							  echo "<li><p>".$row['place']."</p></li>";
						  }
						
						//Visualizza e-mail.
						 $sql = "SELECT email FROM Accesso WHERE id='$profileID' ;";
						 $result = $conn->query($sql);
						 $row = $result->fetch_assoc();
                         echo "<li><p></span>".$row['email']."</p></li>";
                        echo "
                          </ul>
                      </div>
                  </div>
                </div>";
				if($mioID==$profileID)
					echo "<input type=\"submit\" value=\"Salva\" name=\"save\" class=\"button button_shadow\">";
				echo "</form>";
			}else{ //isset
			
				// ****FACCIO QUESTI CONTROLLI PERCHÈ SE NON LI FACESSI NELL'IF DI DOPO DAREBBE UN ERRORE IN QUANTO NON TROVA LE VARIABILI****
				if(isset($_POST['phone_number']))
					$phone = $_POST['phone_number'];
				else
					$phone="";
				
				if(isset($_POST['place']))
					$place = $_POST['place'];
				else
					$place="";
				//****FINE CONTROLLI****
				$myID = $_SESSION['id'];
				
				$img_name = $_SESSION['id'].".jpg";  //Il nome dell'immagine è il numero sessione + jpg
				$path = "./images/profile/_".$img_name; //Qui viene aggiunto il trattino basso in quanto le immagini non possono iniziare con un numero, poi è il path di destinazione
				$imagetemp = $_FILES['pic']['tmp_name'];
				if(is_uploaded_file($imagetemp)) {
					if(move_uploaded_file($imagetemp, $path)) { //Salva l'immagine
						echo "Foto caricata con successo.";
					}
				}
				if($phone!=""){ //Se il numero di telefono è diverso da "vuoto"
					$sql = "UPDATE Anagrafica SET phone_number='$phone' WHERE id='$myID';";
					if($conn->query($sql)){
						echo "Numero di telefono aggiornato";
					}
				}
				if($place!=""){ //Se il luogo è diverso da "vuoto"
					$sql="UPDATE Anagrafica SET place='$place' WHERE id='$myID';";
					if($conn->query($sql)){
						echo "Luogo aggiornato";
					}
				}
				header("Location: ./profilo.php?id=".$myID);
		}
		
			
echo "</body>
</html>";
$conn->close();
?>