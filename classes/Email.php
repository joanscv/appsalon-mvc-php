<?php 
namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {

  public $email;
  public $nombre;
  public $token;

  public function __construct($nombre, $email, $token)
  {
    $this->nombre = $nombre;
    $this->email = $email;
    $this->token = $token;
  }

  public function enviarConfirmacion() {
    
    // Crear el objeto de email
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = $_ENV['EMAIL_HOST'];
    $mail->SMTPAuth = true;
    $mail->Port = $_ENV['EMAIL_PORT'];
    $mail->Username = $_ENV['EMAIL_USER'];
    $mail->Password = $_ENV['EMAIL_PASSWORD'];

    $mail->setFrom("cuentas@appsalon.com");
    $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
    $mail->Subject = "Confirma tu cuenta";

    // Set HTML
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';

    $contenido = "<html>";
    $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> has creado tu cuenta en AppSalon, solo debes confirmarla presionando el siguiente enlace</p>";
    $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['APP_URL'] ."/confirmar-cuenta?token=" . $this->token ."'>Confirmar Cuenta</a></p>";
    $contenido .= "<p>Si tú no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
    $contenido .= "</html>";

    $mail->Body = $contenido;

    // Enviar mail

    $mail->send();
  }

  public function enviarInstrucciones() {

     // Crear el objeto de email
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = $_ENV['EMAIL_HOST'];
    $mail->SMTPAuth = true;
    $mail->Port = $_ENV['EMAIL_PORT'];
    $mail->Username = $_ENV['EMAIL_USER'];
    $mail->Password = $_ENV['EMAIL_PASSWORD'];

    $mail->setFrom("cuentas@appsalon.com");
    $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
    $mail->Subject = "Restablece tu password";

    // Set HTML
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';

    $contenido = "<html>";
    $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> has solicitado restablecer tu password, sigue el siguiente enlace para hacerlo.</p>";
    $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['APP_URL'] . "/recuperar?token=" . $this->token ."'>Restablecer Password</a></p>";
    $contenido .= "<p>Si tú no solicitaste este cambio, puedes ignorar el mensaje</p>";
    $contenido .= "</html>";

    $mail->Body = $contenido;

    // Enviar mail

    $mail->send();

  }
}