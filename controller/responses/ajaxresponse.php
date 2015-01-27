<?php
/**
 * @name AjaxResponse
 * Returns a well formed json response to the client
 *
 * @package Catapult.Controller.Responses
 *
 * @author Cyril Nicodème
 * @version 1.0
 *
 * @since 08/2014
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Catapult\Controller\Responses;

class AjaxResponse extends \Catapult\Controller\Response {
    public static function make($data = null) {
        if (is_null($data) || (!is_array($data) && !is_object($data))) {
            throw new \Catapult\Exceptions\InvalidParameterException('Parameter "data" is required and must be an object or array.');
        }

        $response = new self();
        $response->addHeader('Content-type: application/json; charset=utf-8');
        $response->setData($data);

        return $response;
    }

    private $data = array();

    public function setData($data) {
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }

    protected function output() {
        return json_encode($this->data);
    }
}
