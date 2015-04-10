<?php
/**
 * @name Request
 * Dispatch the request on the correct controller:method
 *
 * @package Catapult.Controller
 *
 * @author Cyril Nicodème
 * @version 1.0
 *
 * @since 08/2014
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Catapult\Controller;

class Request {
    private $path    = null;
    private $method  = null;
    private $headers = null;
    private $route   = null;
    private $data    = null;

    public function __construct() {
        if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
            $path = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
        } else {
            $path = $_SERVER['REQUEST_URI'];
        }

        $base_uri = \Catapult\Core\Config::get('base_uri');

        if (!is_null($base_uri)) {
            if (substr($path, 0, strlen($base_uri)) === $base_uri) {
                $path = '/'.substr($path, strlen($base_uri));
                $path = str_replace('//', '/', $path);
            }
        }

        $this->path = $path;
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function getPath() {
        return $this->path;
    }

    public function getMethod() {
        return $this->method;
    }

    public function isMethod($method) {
        $method = strtoupper($method);
        return $this->method === $method;
    }

    public function setRoute(\Catapult\Controller\Route $route) {
        $this->route = $route;
    }

    public function isAjax() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    public function isSecure() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    }

    public function getHeader($name) {
        $name = strtolower($name);
        if (is_null($this->headers)) {
            $tmpHeaders = getallheaders();
            $this->headers = array();
            foreach ($tmpHeaders as $key=>$value) {
                $this->headers[strtolower($key)] = $value;
            }
        }

        if (!isset($this->headers[$name])) return null;
        return $this->headers[$name];
    }

    public function getQueryString($name = null) {
        if (is_null($name)) {
            return $_GET;
        }

        if (!isset($_GET[$name])) {
            return null;
        }

        return $_GET[$name];
    }

    // TODO: Test JSON body, File upload as BODY
    public function getData($name = null) {
        if (is_null($this->data)) {
            $this->data = array();
            switch(self::getMethod()) {
                case 'GET':
                    $this->data = $_GET;
                    break;
                case 'POST':
                    $this->data = $_GET;
                    break;
                case 'PUT':
                case 'DELETE':
                    parse_str (file_get_contents ("php://input"), $this->data);
                    break;
            }

            if(count($_FILES) > 0) {
                var_dump('TODO: MANAGE FILES');
                foreach($_FILES as $key=>$file) {
                    // TODO !
                    var_dump(
                        $key,
                        $file
                    );
                }
            }
        }

        if (!is_array($this->data)) return null;

        if (is_null($name)) {
            return $this->data;
        } else {
            return $this->data[$name];
        }
    }
}
