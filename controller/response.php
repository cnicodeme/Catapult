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

abstract class Response implements \Catapult\Controller\Responses\IResponse {
    private $headers = array();

    public function addHeader($header) {
        $this->headers[] = $header;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function render() {
        EventDispatcher::trigger('process_response', array($this));

        foreach ($this->getHeaders() as $header) {
            header($header, true);
        }

        echo $this->output();

        EventDispatcher::trigger('process_tear_down', array($this));
    }

    abstract protected function output();
}
