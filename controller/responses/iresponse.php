<?php
/**
 * @name IResponse
 * Force responses to be compliants
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

interface IResponse {
    static function make();
    function render();
}
