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

    private $code = 200;
    private $title = null;
    private $content = null;

    public function __construct($code, $title = null, $content = null) {
        if (!is_int($code)) {
            throw new \Catapult\Exceptions\InvalidParameterException('Parameter "code" must be an integer.');
        }

        $this->engine = function($data) {
            if (!is_null($data)) {
                echo $data;
            }
        };

        $this->code = $code;
        $this->title = $title;
        $this->content = $content;
        $this->addHeader('HTTP/1.0 '.$code.(is_null($title) ? '' : ' '.$title));
    }

    public function addHeader($header) {
        $this->headers[] = $header;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function clearHeaders() {
        $this->headers = array();
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function setEngine($engine) {
        if (!is_callable($engine, true)) {
            throw new \Catapult\Exceptions\NotFoundException('Invalid engine method.');
        }

        $this->engine = $engine;
    }

    public function render() {
        EventDispatcher::trigger('process_response', array($this));

        foreach ($this->getHeaders() as $header) {
            header($header, true);
        }

        call_user_func_array($this->engine, array($this->getContent()));

        EventDispatcher::trigger('process_tear_down', array($this));

        die();
    }

    public static function redirect($url) {
        if (substr($url, 0, 1) === '/') {
            $baseUri = \Catapult\Core\Config::get('base_uri');
            if (!empty($baseUri)) {
                if (substr($baseUri, -1) === '/') {
                    $url = substr($baseUri, 0, -1).$url;
                } else {
                    $url = $baseUri.$url;
                }
            }
        }

        $response = new Response(303, 'Redirect');
        $response->addHeader('Location: '.$url);
        $response->render();
        die();
    }

    public static function abort($code, $title = null, $params = null) {
        if (!is_int($code)) {
            throw new \Catapult\Exceptions\InvalidParameterException('Parameter "code" must be an integer.');
        }

        $route = \Catapult\Controller\Router::getError($code);

        if (is_null($route)) {
            $response = new Response($code, $title);
            $response->render();
        } else {
            if (!is_null($params)) {
                if (!is_array($params)) {
                    $params = array($params);
                }

                $params = array_merge(array($title), $params);
            } else {
                $params = array($title);
            }

            \Catapult\Core\EventDispatcher::trigger('process_view', array($route, $params));
            $response = call_user_func_array($route, $params);
            $response->render();
        }

        die();
    }
}
