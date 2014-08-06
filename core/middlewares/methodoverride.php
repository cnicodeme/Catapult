<?php
/**
 * @name MethodOverride
 * Enable PUT/DELETE methods for unsupported servers
 *
 * @package Catapult.Core.Middlewares
 *
 * @author Cyril Nicodème
 * @version 1.0
 *
 * @since 08/2014
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Catapult\Core\Middlewares;

class MethodOverride {
    public function __construct() {
        \Catapult\Core\EventDispatcher::on('process_request', array($this, 'onProcessRequest'));
    }

    public function onProcessRequest() {
        if (isset($_SERVER['X_HTTP_METHOD_OVERRIDE']) && \Catapult\Controller\Route::isMethodAllowed($_SERVER['X_HTTP_METHOD_OVERRIDE'])) {
            \Catapult\Controller\Request::setMethod($_SERVER['X_HTTP_METHOD_OVERRIDE']);
        }
    }
}
