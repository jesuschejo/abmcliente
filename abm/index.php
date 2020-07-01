<?php 

ini_set('display_errors','1');
ini_set('display_startup_errors','1');
ini_set('error_reporting', E_ALL );

if (file_exists("data.txt")) {
    $jsonClientes = file_get_contents("data.txt");
    $aClientes = json_decode($jsonClientes, true);
}else{
    $aClientes = array();
}

$id = isset($_GET["id"]) ? $_GET["id"] : '';

if(isset($_GET["do"]) && isset($_GET["do"]) && $_GET["do"] == "eliminar"){
    //eliminar la lista de cliente con la funcion : (UNSET)
    unset($aClientes[$id]);
   //guardar en el archivo
    $jsonClientes = json_encode($aClientes);
    file_put_contents("data.txt", $jsonClientes);
}

if ($_POST){ /* ES LA DEVOLUCION DE DATOS, PORQUE ALGUIEN HIZO CLICK EN GUARDAR */
//DEFINNICION DE VARIABLES
  $dni = $_POST["txtDni"];
  $nombre = $_POST["txtNombre"];
  $telefono = $_POST["txtTelefono"];
  $correo = $_POST["txtCorreo"];
  $nombreImagen = "";

  if ($_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {
    $nombreAleatorio = date("Ymdhmsi");
    $archivo_tmp = $_FILES["archivo"]["tmp_name"];
    $nombreArchivo = $_FILES["archivo"]["name"];
    $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
    $nombreImagen = $nombreAleatorio . "." . $extension;
    move_uploaded_file($archivo_tmp, "archivos/$nombreImagen");
}

  if (isset($_GET["id"])){
      //Si hay una imagen anterior eliminarla, siempre y cuando se suba una nueva imagen
      $imagenAnterior = $aClientes[$id]["imagen"];

      if ($_FILES["archivo"]["error"] === UPLOAD_ERR_OK){
        if($imagenAnterior != ""){
            unlink("archivos/$imagenAnterior");
        }
    }        
    if ($_FILES["archivo"]["error"] !== UPLOAD_ERR_OK) {
        $nombreImagen = $imagenAnterior;
    }
      //MODIFICAR
      $aClientes[$id] = array("dni" => $dni,
                      "nombre" => $nombre,
                      "telefono" => $telefono,
                      "correo" => $correo,
                      "imagen" => $nombreImagen
    ); 
$jsonClientes = json_encode($aClientes);
file_put_contents("data.txt", $jsonClientes);

  } else {
     //CONVERTIR LOS DATOS DEL FORMULARIO EN UN ARRAY
    $aClientes[] = array("dni" => $dni,
                      "nombre" => $nombre,
                      "telefono" => $telefono,
                      "correo" => $correo,
                      "imagen" => $nombreImagen  
      );

//CONVERTIR EL ARRAY EN JSON
$jsonClientes = json_encode($aClientes);
//GUARDAR EL JSON EN UN ARCHIVO DATA.TXT CON File_put_contents
file_put_contents("data.txt", $jsonClientes);
   }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABM clientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link href="css/fontawesome/css/all.min.css" rel="stylesheet">
    <link href="css/fontawesome/css/fontawesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">


</head>
<body>
<div class="container">
        <div class="row">
            <div class="col-12 text-center py-3">
                <h1>Registro de clientes</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-12 form-group">
                            <label for="txtDni">DNI:</label>
                            <input type="text" id="txtDni" name="txtDni" class="form-control" required value= "<?php echo isset($aClientes[$id]["dni"])? $aClientes[$id]["dni"] : ''; ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtNombre">Nombre:</label>
                            <input type="text" id="txtNombre" name="txtNombre" class="form-control" required value="<?php echo isset($aClientes[$id]["nombre"])? $aClientes[$id]["nombre"] : ''; ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtTelefono">Teléfono:</label>
                            <input type="text" id="txtTelefono" name="txtTelefono" class="form-control" required value="<?php echo isset($aClientes[$id]["telefono"])? $aClientes[$id]["telefono"] : ''; ?>"> 
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtCorreo">Correo:</label>
                            <input type="text" id="txtCorreo" name="txtCorreo" class="form-control" required value="<?php echo isset($aClientes[$id]["correo"])? $aClientes[$id]["correo"] : ''; ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtArchivo">Archivo Adjunto</label>
                            <input type="file" id="archivo" name="archivo" class="form-control">
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" id="btnGuardar" name="btnGuardar" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-6">
                <table class="table table-hover border">
                    <tr>
                        <th>Imagen</th>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                
                    <?php foreach ($aClientes as $key => $cliente ): ?>
                    <tr>
                            <td><img src="archivos/<?php echo $cliente["imagen"];?>" class="img-thumbnail"></td>
                            <td><?php echo $cliente["dni"]; ?></td>
                            <td><?php echo $cliente["nombre"]; ?></td>
                            <td><?php echo $cliente["correo"]; ?></td>
                            <td style="width: 110px;">
                            <a href="index.php?id=<?php echo $key ?>"><i class="fas fa-edit"></i></a>
                            <a href="index.php?id=<?php echo $key ?>&do=eliminar"><i class="fas fa-trash"></i></a>    
                    </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <a href="index.php"><i class="fas fa-user-plus"></i></a>
            </div>
        </div>
    </div>
</body>
</html>