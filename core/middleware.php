<?php
/**
 * @name Middleware
 * Support Middleware system like Django's
 *
 * @package Catapult.Core
 *
 * @author Cyril Nicodème
 * @version 1.0
 *
 * @since 08/2014
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Catapult\Core;

abstract class Middleware {
    public function onProcessRequest() {}   // Listens to : "process_request"
    public function onProcessView() {}      // Listens to : "process_view"

    public function onProcessException() {} // Listens to : "process_exception"
    public function onProcessResponse() {}  // Listens to : "process_response"

    public function onTearDown() {}         // Listens to : "process_tear_down"
}
