<?php
class PDOException extends Exception {
	public $errorInfo = null;    // correspond à PDO::errorInfo()
	// ou PDOStatement::errorInfo()
	protected $message;          // message d'erreur textuel
	// utiliser Exception::getMessage() pour y accéder
	protected $code;             // code erreur SQLSTATE
	// utiliser Exception::getCode() pour y accéder
}
?>