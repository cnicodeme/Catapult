<?php
/**
 * @name Locale
 * Manage I18n from the client, through Gettext
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

class Locale extends \Catapult\Core\Middleware {
    private static $lang = null;

    public function __construct() {
        \Catapult\Core\EventDispatcher::on('process_request', array($this, 'onProcessRequest'));
    }

    public function onProcessRequest() {
        if(isset($_SESSION['locale'])) {
            self::$lang = $_SESSION['locale'];
        } else {
            self::$lang = $this->getLanguageFromBrowser();
        }
    }

    // Thanks to https://www.drupal.org/node/221712
    private function getLanguageFromBrowser() {
        // Specified by the user via the browser's Accept Language setting
        // Samples: "hu, en-us;q=0.66, en;q=0.33", "hu,en-us;q=0.5"
        // MSIE 7: "en-nz,en-us;q=0.7,sv-se;q=0.3"
        $browser_langs = array();
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            preg_match_all('"(((\S\S)-?(\S\S)?)(;q=([0-9.]+))?)\s*(,\s*|$)"',strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']),$browser_accept);
            for ($i = 0; $i < count($browser_accept); $i++) {
                // The language part is either a code or a code with a quality.
                // We cannot do anything with a * code, so it is skipped.
                // If the quality is missing, it is assumed to be 1 according to the RFC.
                if(!empty($browser_accept[2][$i])) $browser_langs[$browser_accept[2][$i]] = ($browser_accept[6][$i]? (float) $browser_accept[6][$i] : 1.0);
                if(!empty($browser_accept[3][$i]) && empty($browser_langs[$browser_accept[3][$i]])) $browser_langs[$browser_accept[3][$i]] = ($browser_accept[6][$i]? (float) $browser_accept[6][$i]-0.01 : 0.99);
            }
        }
        // Order the codes by quality
        arsort($browser_langs);

        $languages = \Catapult\Core\Config::getArray('languages');
        if (!is_null($languages)) {
            $languages = array_fill_keys($languages, true);
            foreach ($browser_langs as $langcode => $q) {
                if (isset($languages[$langcode])) return $langcode;
            }
        }

        return \Catapult\Core\Config::get('default_language', 'en'); // Default
    }

    public function getLang() {
        return self::$lang;
    }

    public static function setLang($lang) {
        $_SESSION['locale'] = $lang;
        self::$lang = $lang;
    }
}
