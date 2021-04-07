<?php
session_start();

$message = ''; 
if (isset($_POST['uploadBtn']) && $_POST['uploadBtn'] == 'Upload'){
  if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK){
    // Obtiene los detalles del archivo subido
    $fileTmpPath = $_FILES['uploadedFile']['tmp_name'];
    $fileName = $_FILES['uploadedFile']['name'];
    $fileSize = $_FILES['uploadedFile']['size'];
    $fileType = $_FILES['uploadedFile']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
    $target_file = $fileName;

    // Sanitiza el nombre del archivo
    $newFileName = $fileName;//md5(time() . $fileName) . '.' . $fileExtension;

    // check if file has one of the following extensions
    $allowedfileExtensions = array('jpg', 'gif', 'png', 'zip', 'txt', 'xls', 'doc', 'pdf');

    if (in_array($fileExtension, $allowedfileExtensions)){
      // Directorio en el cual el archivo será movido
      $uploadFileDir = './uploaded_files/';
      $dest_path = $uploadFileDir . $newFileName;

      if(move_uploaded_file($fileTmpPath, $dest_path)) {
        $message ='Archivo correctamente cargado';
      } else{
          $message = 'Ocurrió un error al subir el archivo. Asegúrate que la carpeta pueda ser accedida.';
      }
    } else{
        $message = 'Carga fallida. Archivos permitidos: ' . implode(',', $allowedfileExtensions);
      }

      // Revisa si el archivo ya existe
  if (file_exists($target_file)) {
    echo "Lo siento, el archivo ya existe.";
    $uploadOk = 0;
  }

  } else{
    $message = 'Ocurrió un error.<br>';
    $message .= 'Error:' . $_FILES['uploadedFile']['error'];
  }
}

$_SESSION['message'] = $message;
header("Location: index.php");
?>