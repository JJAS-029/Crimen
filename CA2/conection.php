<?php
// Conectando y seleccionado la base de datos  
$dbconnect = pg_connect("host=70.35.196.78  dbname=sigdis user=sigdis password=sigdis")
    or die('No se ha podido conectar: ' . pg_last_error());
// Realizando una consulta SQL schema
$query = 'SELECT * FROM jjas.usuarios';
$result = pg_query($query) or die('La consulta fallo: ' . pg_last_error());
//LOGIN USUARIO
$login = $_POST["login"];
$pass = $_POST["password"];

$query1="SELECT*from jjas.usuarios where nombre='$login' and contraseña='$pass'";
$result=pg_query($dbconnect,$query1);
$rows=pg_fetch_array($result);
if($rows['id_cargo']==1){ //administrador
    header("location:Admin.php");
  
  }else
  if($rows['id_cargo']==2){ //cliente
  header("location:client.php");
  }
  else{
    ?>
    <?php
    include("index.php");
    ?>
    <h1 class="bad">ERROR EN LA AUTENTIFICACION</h1>
    <?php
  }
// Cerrando la conexión
pg_close($dbconnect);
?>