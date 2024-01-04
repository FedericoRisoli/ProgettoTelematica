<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'C:\xampp\htdocs\bdp\vendor\phpmailer\phpmailer\src\Exception.php';
require 'C:\xampp\htdocs\bdp\vendor\phpmailer\phpmailer\src\PHPMailer.php';
require 'C:\xampp\htdocs\bdp\vendor\phpmailer\phpmailer\src\SMTP.php';


$conn=new mysqli('localhost','root','','bonsaistore');
session_start();
if($conn->connect_error)
{
    die('connessione fallita' .$conn->connect_error);
}
//REGISTRATION
if(isset($_POST["chekoperation"])) 
  {
  $operation=$_POST["chekoperation"];
  if($operation=="reg")
    {
    $n = $_POST["n"];     //nome
    $sn= $_POST["sn"];     //surname
    $add= $_POST["addr"];  //indirizzo
    $date= $_POST["brt"];   //nascita
    $un= $_POST["un"];      //username
    $pss= $_POST["pw"];     //password
    $mail= $_POST["mail"];     //password
    $sqlregistration="INSERT INTO `utenti`(`username`, `password`, `nome`, `cognome`, `datanascita`, `indirizzo`,`mail`) VALUES ('$un','$pss','$n','$sn','$date','$add','$mail')" ;

    //registrazione andata a buon fine?
    if ($conn->query($sqlregistration) === TRUE) 
      {
        //echo "<script>alert('Login Avvenuto.');</script>";
        login($un,$pss,$conn);
      } 
      else
      {
        //controllo se l'errore è duplicate entry, ovvero se la chiave primaria (username) è già presente
        $uguali = substr_compare($conn->error, "Duplicate entry ", 0, 16);
        if ( $uguali == 0 ){
          echo "<script>alert('Username già in uso , sceglierne uno differente');</script>";
        }
        else{
          echo "<script>alert('Error inserting record:  . $conn->error');</script>";
        }
        echo file_get_contents("registrati.php");
        exit(0);
      }

    }
  else if($operation=="login")
  {
    $name= $_POST["usr"];
    $psw=$_POST["psw"];
    login($name,$psw,$conn);
  }
  else if($operation=="comprato"){
    //id usr idprod, data
    $name= $_SESSION["usr"];
    $data = date("Y-m-d");
    $prodotto = $_SESSION['idprod'];
    $prezzo = $_POST['prezzo'];

    $compro="INSERT INTO `acquisti`(`id`, `usr`, `idprod`, `data`, `prezzo`) VALUES (null,'$name',$prodotto,'$data', $prezzo);";
    //eseguo la query
      // Prepara i dettagli dell'ordine per l'email
      $nomeProdotto=$_POST['nome'];   // Recupera il nome del prodotto
      $prezzo = $_POST['prezzo']; // Recupera il prezzo del prodotto
      $mail=$_SESSION["mail"];
      
     
      
      
      //Load Composer's autoloader
      require 'vendor/autoload.php';
      //psw oich jhsi jutv zkdb
      //Create an instance; passing `true` enables exceptions
      $mail = new PHPMailer(true);
      
      try {
          //Server settings
          $mail->SMTPDebug = 0;                      //Enable verbose debug output
          $mail->isSMTP();                                            //Send using SMTP
          $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
          $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
          $mail->Username   = 'progettiuniversita9@gmail.com';                     //SMTP username
          $mail->Password   = 'oichjhsijutvzkdb';                               //SMTP password
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;;            //Enable implicit TLS encryption
          $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
      
          //Recipients
          $mail->setFrom('progettiuniversita9@gmail.com', 'no-reply Bonsai Confirmation');
          $mail->addAddress($_SESSION["mail"]);     //Add a recipient

 
          //Content
          $mail->isHTML(true);                                  //Set email format to HTML
          $mail->Subject = 'Bonsai Order Confirmationl';
          $mail->Body    = "<div style='font-family: Brush Script MT, sans-serif; color: #FCA311;'>
          <h1 style='font-size: 20px; color: #FCA311;'><b>Conferma Ordine</b></h1>
          <p>Grazie per aver acquistato su Bonsai Store!</p>
          <p style='color: #607466;'><b>Prodotto:</b> {$nomeProdotto}</p>
          <p style='color: #607466;'><b>Prezzo:</b> €{$prezzo}</p>
          <p>Il prodotto arriverà in 3 giorni lavorativi</p>
          </div>";
         
      
          $mail->send();
          echo "<script>alert('La conferma dell\'ordine è stata inviata via email.');</script>";
      } catch (Exception $e) {
        echo "<script>alert('L'email non è stata inviata. Errore: {$mail->ErrorInfo}');</script>";
      }
  }}


$sql2="SELECT id, nome, promo, prezzo, nomeimg FROM prodotti ORDER BY promo DESC" ;


$result2=mysqli_query($conn,$sql2);//eseguo la query

function login($u, $p, $conn) {
  $sql = "SELECT * FROM utenti WHERE username LIKE '$u' AND password LIKE '$p'";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
      while ($row = $result->fetch_assoc()) {
          $_SESSION["name"] = $row['nome'];
          $_SESSION["usr"] = $row['username'];
          $_SESSION["mail"] = $row['mail']; 
      }
     
  }
  else if((empty($u) || empty($p)|| ($u==""||$p==""))){
    $_SESSION["login_failed"] = true; 
    header("location: login.php"); //questo è un redirect
    exit; 
  }
  else 
  {
    $_SESSION["login_failed"] = true; 
    header("location: login.php"); //questo è un redirect
    exit; 
  }
  
}
?>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>home</title>
  <link rel="stylesheet" href="Style.css">
</head>
<body>
<ul>
    <li><a  href="logged.php"> <img class="logo" src="imgsito/logo.png"></a></li>
    <li><h2 class="title">Bonsai Store</h2></li>
    
    <li class="acc_button"><a class="normalbutton" href="index.php">Log Out</a></li>
    <li class="saluto"><h4> Bentornato  <?php print $_SESSION["name"]?> </h4> </li>
    
    <?php
    if($_SESSION["usr"]=="admin"){
      print('<li class="acc_button"><a class="normalbutton" href="insight.php">Insight</a></li>');
      print('<li class="dropdown">
      <a href="javascript:void(0)" class="dropbtn">Gestisci prodotti</a>
      <div class="dropdown-content">
        <a href="add.php">Aggiungi Prodotto</a>
        <a href="modify.php">Modifica Prodotto</a>
        <a href="remove.php">Rimuovi Prodotto</a>
      </div>
      <li class="acc_button"><a class="normalbutton" href="order.php">Ordini</a></li>
    </li>');
    }
    else{
      print('<li class="acc_button"><a class="normalbutton" href="myorder.php">i miei ordini</a></li>');
    }
    ?>
  </ul>

      

  <div class="table-container">
    <table>
      <tbody>
        <tr>
          <?php 
          if(mysqli_num_rows($result2)>0)  //questa è la prima riga della tabella che mostra tutti i nomi
          {
              while($row=$result2->fetch_assoc())
              {
                if ($row['promo']==1)
                {
                  print("<td class=\"titolo_prod\">".$row['nome']."<br>PROMO -10%</td>");
                }
                else{
                  print("<td class=\"titolo_prod\">".$row['nome']."</td>");
                }
              }   
          } 
          ?>
        </tr>
        <tr>
        <?php 
        mysqli_data_seek($result2, 0); 
          if($result2->num_rows>0) //questo è un modo alternativo per vedere se ci sono righe (scegli tu tanto è uguale)
          { 
            //questa è la seconda riga della tabella che mostra tutte le immagini
              while($row=$result2->fetch_assoc())
              {
                  print"<td>"."<img class=\"prod-img\" src='imgsito/".$row['nomeimg']."'>"."</td>";
                 
              }
          }
          ?>
        </tr>
        <tr>
          <?php
        mysqli_data_seek($result2, 0); 
        if($result2->num_rows>0) //questa è la terza riga della tabella che mostra tutti i prezzi
          {
              while($row=$result2->fetch_assoc())
              {
                if($row['promo']==0)
                {
                  print("<td>".$row['prezzo']." $"."</td>");
                }
                else
                {
                  print("<td>".round($row['prezzo']*0.9, 2)." $"."</td>");
                }
              }
          }
          ?>
        </tr>
      </tbody>
    </table>
  </div>
  <form class="lform" name="form" method="POST" action="pagamento.php">
    <H4 class=titolo_prod>Quale Bonsai desideri?</H4>
    <SELECT name="bonsai">
    <?php
      mysqli_data_seek($result2, 0); 
      foreach ($result2 as $row) {
        echo "<option value=".$row['id'].">" . $row['nome'] . "</option>";
      }
            ?>
    </SELECT>
    <BR>
    <input class="sub" type="submit" value="Compra">
  </form>
</body>
</html>
