<?php
require_once(MODELS.'Mailer.php');

// Clase base para manejar la conexión y las operaciones con la base de datos
class Database
{
    private $servername;
    private $username;
    private $password;
    private $charset;
    private $dbname;
    private $conn;
    private $logFile;
    private $adminEmail;
    private $mailer;

    // Constructor de la clase
    public function __construct($servername, $username, $password, $charset, $dbname, $logFile=LOGS . 'errors.log', $adminEmail="rimiss@rimisszoic.live")
    {
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->charset = $charset;
        $this->dbname = $dbname;
        $this->logFile = $logFile;
        $this->adminEmail = $adminEmail;
        $this->mailer = new Mailer();
    }

    /**
     * Método para establecer la conexión con la base de datos utilzando PDO
     * @throws Exception Si la conexión falla
     */
    public function connect()
    {
        try{
            $dsn = "mysql:host=$this->servername;dbname=$this->dbname;charset=$this->charset";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("SET time_zone = '+01:00'");
        } catch (PDOException $e) {
            $this->handleError("Connection failed: ".$e->getMessage());
            throw new Exception("Ha ocurrido un error. Por favor, inténtelo de nuevo más tarde.");
        }
    }
    
    /**
     * Método para ejecutar una consulta SQL
     * @param string $query Consulta SQL a ejecutar
     * @return PDOStatement Resultado de la consulta
     * @throws Exception Si la consulta falla
     */
    public function query($query)
    {
        try {
            return $this->conn->query($query);
        } catch (PDOException $e) {
            $this->handleError("Query failed: ".$e->getMessage());
            throw new Exception("Ha ocurrido un error. Por favor, inténtelo de nuevo más tarde.");
        }
    }

    /**
     * Método para ejecutar una consulta preparada
     * @param string $query Consulta SQL a ejecutar
     * @return PDOStatement Resultado de la consulta
     * @throws Exception Si la consulta falla
     */
    public function prepare($query){
        return $this->conn->prepare($query);
    }

    /**
     * Método para obtener la última fila insertada en la base de datos
     * @return self ID de la última fila insertada
     * @throws Exception Si la consulta falla
     */
    public function returnConnection(){
        return $this->conn;
    }

    /**
     * Método para obtener la última fila insertada en la base de datos
     * @return int ID de la última fila insertada
     * @throws Exception Si la consulta falla
     */
    public function lastInsertId(): int{
        try {
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            $this->handleError("Last insert ID failed: ".$e->getMessage());
            throw new Exception("Ha ocurrido un error. Por favor, inténtelo de nuevo más tarde.");
        }
    }

    /**
     * Método para manejar los errores en un archivo y enviar correos electrónicos al administrador
     * @param string $message Mensaje de error
     * @throws Exception Siempre se lanza una excepción después de manejar el error
     */
    private function handleError($message)
    {
        // Registrar el error en un archivo de logs
        $log="[".date('Y-m-d H:i:s')."] ".$message.PHP_EOL;
        error_log($log, 3, $this->logFile);

        // Enviar un correo electrónico al administrador
        try{
            $subject="Error en la aplicación";
            $template=$this->mailer->loadTemplate(RESOURCES.'templates/error_email.html', ['errorMessage'=>$message]);
            $this->mailer->sendMail($this->adminEmail, $subject, $template);
        } catch (Exception $e) {
            $log="[".date('Y-m-d H:i:s')."] Error sending email: ".$e->getMessage().PHP_EOL;
            error_log($log, 3, $this->logFile);
        }

        throw new Exception("Ha ocurrido un error. Por favor, inténtelo de nuevo más tarde.");
    }
}
?>