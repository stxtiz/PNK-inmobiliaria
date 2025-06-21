<?php


include("setup/config.php");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muestra de propiedades</title>
    <link rel="stylesheet" href="css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="js/jquery.Rut.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php

$sql="SELECT 
p.idpropiedades,p.estado,
p.titulopropiedad AS titulo,
p.precio_pesos,
g.foto
FROM 
propiedades p
JOIN 
galeria g ON p.idpropiedades = g.idpropiedades
WHERE 
g.principal = 1";
$result = mysqli_query(conectar(), $sql);

while($datos = mysqli_fetch_array($result)) 

{
    $fmt = numfmt_create('es_CL', NumberFormatter::CURRENCY);
    $precio = numfmt_format_currency($fmt, $datos['precio_pesos'], "CLP");


?>
    <div id="propiedad">
        <div class="card">
            <div class="card-header titulo"><?php echo $datos['titulo']; ?></div>
            <div class="card-body"><img src="propiedades/<?php echo $datos['foto'];?>" width=260px><br>Precio:<?php echo $precio; ?></div>
            <div class="card-footer"><center><a href="vermas.php?idpro=<?php echo $datos['idpropiedades'];?>">Ver Detalle<a></center></div>
        </div>
    </div>
<?php    
}
?>
</body>
</html>