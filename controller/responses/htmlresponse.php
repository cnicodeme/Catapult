<?php
/**
 * @name HtmlResponse
 * Returns a well formed html response to the client
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

class HtmlResponse extends \Catapult\Controller\Response {
    public static function make($template = null, $params = null) {
        if (is_null($template) || !is_string($template)) {
            throw new \Catapult\Exceptions\InvalidParameterException('Parameter "template" is required and must be a string.');
        }

        /*
        Config :
            Default response type
            Template base path
            Default charset
        */

        $response = new self();
        $response->addHeader('Content-type: text/html; charset=utf-8');

        return $response;
    }

    protected function output() {
        var_dump('output');
    }
}
