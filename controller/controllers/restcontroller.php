<?php
/**
 * @name RestController
 * Controller for managing typical RESTful controllers (list, details, create, update, delete)
 *
 * @package Catapult.Controller.Controllers
 *
 * @author Cyril Nicodème
 * @version 1.0
 *
 * @since 04/2015
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Catapult\Controller\Controllers;

abstract class RestController extends \Catapult\Controller\Controller {
    public static function lists() {
        return self::getResponse()->notFound();
    }

    public static function create() {
        return self::getResponse()->notFound();
    }

    public static function details($id) {
        return self::getResponse()->notFound();
    }

    public static function update($id) {
        return self::getResponse()->notFound();
    }

    public static function delete($id) {
        return self::getResponse()->notFound();
    }
}
