<?php
/**
 * @name Response
 * Returns a well formed response to the client
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

use \Catapult\Core\EventDispatcher;

class Response {
    private $headers = array();
    private $engine = null;

    public function __construct() {
        $this->engine = function($data) {
            func_get_args();
            echo $data;
        };
    }

    public function addHeader($header) {
        $this->headers[] = $header;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function render($data = null) {
        EventDispatcher::trigger('process_response', array($this));

        foreach ($this->getHeaders() as $header) {
            header($header, true);
        }

        call_user_func_array($this->engine, array($data));

        EventDispatcher::trigger('process_tear_down', array($this));
    }

    public function setEngine($engine) {
        if (!is_callable($engine, true)) {
            throw new \Catapult\Exceptions\NotFoundException('Invalid engine method.');
        }

        $this->engine = $engine;
    }

    public function noContent() {
        return $this->abort(204);
    }

    public function redirect($url) {
        $this->clearHeaders();
        $this->addHeader('HTTP/1.0 303 Redirect');
        $this->addHeader('Location: '.$url);
        $this->render();
        die();
    }

    public function badRequest($title = null) {
        return $this->abort(400, $title);
    }

    public function unauthorized($title = null) {
        return $this->abort(401, $title);
    }

    public function forbidden($title = null) {
        return $this->abort(403, $title);
    }

    public function notFound($title = null) {
        return $this->abort(404, $title);
    }

    public function internalServerError($title = null) {
        return $this->abort(500, $title);
    }

    public function abort($code, $title = null, $params = null) {
        if (!is_int($code)) {
            throw new \Catapult\Exceptions\InvalidParameterException('Parameter "code" must be an integer.');
        }

        $route = \Catapult\Controller\Router::getError($code);

        if (is_null($route)) {
            $this->addHeader('HTTP/1.0 '.$code.(is_null($title) ? '' : ' '.$title));
            if(!is_null($title)) {
                $this->render($title);
            } else {
                $this->render();
            }
        } else {
            \Catapult\Core\EventDispatcher::trigger('process_view', array($route, array($params)));

            $this->addHeader('HTTP/1.0 '.$code.(is_null($title) ? '' : ' '.$title));
            $result = call_user_func_array($route, array($params));
            $this->render($result);
        }

        die();
    }
}
