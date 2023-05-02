<?php
require '/PHPMailer.php';
require '/SMTP.php';
require '/Exception.php';

$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'tomy.edfi@gmail.com';
$mail->Password = '2023classes';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
$mail->setFrom('tomy.edfi@gmail.com', 'Tomy');

$nombre = $_POST['nombre'];
$email = $_POST['email'];
$descripcion = $_POST['descripcion'];

if(isset($_FILES['archivos'])) {
  $archivos = $_FILES['archivos'];

  for($i = 0; $i < count($archivos['name']); $i++) {
    $mail->addAttachment($archivos['tmp_name'][$i], $archivos['name'][$i]);
  }
}

$mail->addAddress('tomy.edfi@gmail.com', 'Destinatario');
$mail->Subject = 'Nuevo mensaje desde el formulario de contacto';
$mail->Body = "Nombre: $nombre\nEmail: $email\nDescripción: $descripcion";

if($mail->send()) {
  echo 'Mensaje enviado correctamente';
} else {
  echo 'Error al enviar el mensaje: ' . $mail->ErrorInfo;
}
?>
