<?php
// Carga las librerías de Google API para PHP
require_once __DIR__ . '/vendor/autoload.php';

// Configura las credenciales de API
$client = new Google_Client();
$client->setAuthConfig('credentials.json');
$client->addScope(Google_Service_Drive::DRIVE_FILE);

// Inicia sesión en Google Drive
$service = new Google_Service_Drive($client);

// Crea una carpeta en Google Drive para guardar los archivos
$nombreCarpeta = 'Archivos subidos desde mi sitio web';
$carpeta = new Google_Service_Drive_DriveFile(array(
    'name' => $nombreCarpeta,
    'mimeType' => 'application/vnd.google-apps.folder'
));
$carpeta = $service->files->create($carpeta, array(
    'fields' => 'id'
));
$destFolderUrl = 'https://drive.google.com/drive/folders/' . $carpeta->getId();

// Sube los archivos a Google Drive
foreach ($_FILES['archivos']['error'] as $key => $error) {
  if ($error == UPLOAD_ERR_OK) {
    $nombreArchivo = $_FILES['archivos']['name'][$key];
    $archivo = file_get_contents($_FILES['archivos']['tmp_name'][$key]);
    $contentType = $_FILES['archivos']['type'][$key];

    $fileMetadata = new Google_Service_Drive_DriveFile();
    $fileMetadata->setName($nombreArchivo);
    $fileMetadata->setParents([$destFolderUrl]);
    $file = $service->files->create($fileMetadata, array(
        'data' => $archivo,
        'mimeType' => $contentType,
        'uploadType' => 'multipart',
        'fields' => 'id'
    ));
  }
}

// Envía un correo electrónico al destinatario
$destinatario = $_POST['correo'];
$asunto = 'Archivos subidos';
$mensaje = 'Se han subido los siguientes archivos a Google Drive: ' . implode(', ', $_FILES['archivos']['name']);
$headers = 'From: ' . $_POST['nombre'] . ' <' . $_POST['correo'] . '>' . "\r\n";
$headers .= 'Reply-To: ' . $_POST['correo'] . "\r\n";
$headers .= 'X-Mailer: PHP/' . phpversion();

mail($destinatario, $asunto, $mensaje, $headers);

// Redirige al usuario a una página de confirmación
header('Location: confirmacion.html');
