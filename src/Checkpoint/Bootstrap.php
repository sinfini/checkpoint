<?php
/**
 * This file is part of the php-sqlbox utility.
 *
 * (c) Sankar suda <sankar.suda@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Checkpoint;

/**
 * @author sankar <sankar.suda@gmail.com>
 */

date_default_timezone_set ('Asia/Calcutta');

defined('DS') or define('DS',DIRECTORY_SEPARATOR);
defined('ABSPATH') or define('ABSPATH',realpath(dirname(__FILE__).DS).DS);
defined('STORAGE') or define('STORAGE',ABSPATH.'storage/');
defined('LOG') or define('LOG',STORAGE.'log/');
defined('TMP') or define('TMP',STORAGE.'tmp/');
defined('CACHE') or define('CACHE',STORAGE.'cache/');
//defined('BIN') or define('BIN',realpath(ABSPATH.'../../').DS);
defined('BIN') or define('BIN',ABSPATH);


$errors = E_ALL^E_NOTICE^E_STRICT;
error_reporting($errors);

//ini_set('display_errors','Off');
ini_set('log_errors', 'On');
ini_set('error_log', LOG.'error.log');

set_time_limit(0);

class Bootstrap
{

    private static function acquire($file)
    {
        if (is_file($file)) {
            return include($file);
        }
    }

    public static function run()
    {
        if (!($loader = self::acquire(__DIR__ . '/../../vendor/autoload.php'))) {
            echo 'You must set up project\'s dependencies first by running the following commands:' . PHP_EOL;
            echo "    curl -s https://getcomposer.org/installer | php\n";
            echo "    php composer.phar install\n";
            exit(1);
        }
        return $loader;
    }
}

return Bootstrap::run();