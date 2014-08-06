<?php
/**
 * @name Model
 * Abstracted model class for creating (ORM like) model from database.
 *
 * @package Catapult.Database
 *
 * @author Cyril Nicodème
 * @version 1.0
 *
 * @since 08/2014
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Catapult\Database;

abstract class Database {
    abstract public function findById($id);
    abstract public function find($filters = null);
    abstract public function save();
    abstract public function delete();
}
