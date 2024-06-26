<?php
require_once(dirname(__DIR__).'/resources/mailer/src/PHPMailer.php');
require_once(dirname(__DIR__).'/resources/mailer/src/Exception.php');
require_once(dirname(__DIR__).'/resources/mailer/src/SMTP.php');
require_once(dirname(__DIR__).'/config/const.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Clase para enviar correos electrónicos
class Mailer
{
    private $mail;

    public function __construct($host=EMAIL_HOST, $port=587, $encryption='tls', $username=EMAIL_USER, $password=EMAIL_PWD)
    {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = $host;
        $this->mail->Port = $port;
        $this->mail->SMTPSecure = $encryption;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $username;
        $this->mail->Password = $password;
        $this->mail->CharSet = 'UTF-8';
    }

    /**
     * Método para enviar un correo electrónico
     * @param string $to Dirección de correo electrónico del destinatario
     * @param string $subject Asunto del correo
     * @param string $body Cuerpo del correo
     * @throws Exception Si el envío del correo falla
     */
    public function sendMail($to, $subject, $body, $altBody='', $from='noreply@noelperez.rimisszoic.live', $fromName='Game Archive')
    {
        try {
            $this->mail->setFrom($from, $fromName);
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->AltBody = $altBody;
            $this->mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $e->getMessage(),3,LOGS . 'errors.log');
            throw new Exception("No se pudo enviar el correo electrónico. Por favor, inténtelo de nuevo más tarde.");
        }
    }

    public function loadTemplate($templatePath, $variables=[])
    {
        if(!file_exists($templatePath)){
            throw new Exception("La plantilla de correo electrónico no existe.");
        }
        $templateContent=file_get_contents($templatePath);

        foreach($variables as $key=>$value){
            $templateContent=str_replace('{{ '.$key.' }}', $value, $templateContent);
        }

        return $templateContent;
    }
}
?>