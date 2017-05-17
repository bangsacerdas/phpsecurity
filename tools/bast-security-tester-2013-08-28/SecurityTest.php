<?php

/**
 * BAST - Basic security tester in PHP.
 *
 * Copyright (c) 2005-2011 Artur Graniszewski (aargoth@boo.pl)
 * All rights reserved.
 *
 * @category   Library
 * @copyright  Copyright (c) 2005-2011 Artur Graniszewski (aargoth@boo.pl)
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 * @version    1.12.09.28
 */

/**
 * Screen class.
 */
class Screen
{
    const STRONG = 1;
    const REGULAR = 0;

    /**
     * Writes the line of text.
     *
     * @param string $message Message.
     * @param int $format Format.
     * @return void
     */
    public static function writeLine($message, $format = self::REGULAR) {
        if(self::STRONG & $format) {
            $message = "<strong>$message</strong>";
        }
        echo $message."\n";
    }

    /**
     * Writes the text.
     *
     * @param string $message Message.
     * @param int $format Format.
     * @return void
     */
    public static function write($message, $format = self::REGULAR) {
        if(self::STRONG & $format) {
            $message = "<strong>$message</strong>";
        }
        echo $message;
    }
}

/**
 * Main test (runs all other tests)
 */
class MainTest
{
    /**
     * List of loaded extensions.
     *
     * @var string[]
     */
    protected $loadedExtensions = array();

    /**
     * Version of this class.
     *
     * @return string
     */
    public function getVersion() {
        return '1.13.09.28 (2013-09-28)';
    }

    /**
     * Runs the tests
     *
     * @return MainTest
     */
    public function __construct() {
        Screen::writeLine('<pre>');
        Screen::write('<h1>Security Report</h1>');
        Screen::write('<h3>Report date: ' . gmdate('M d Y H:i:s') .'</h1>');
        Screen::write('<h3>Test version: ' . $this->getVersion() .' ' . md5(file_get_contents(__FILE__)) . '</h1>');
        Screen::writeLine('<strong>Detected software:</strong>');
        if(function_exists('gethostname')) {
            Screen::writeLine(' * Host name: ' . gethostname());
        }
        Screen::writeLine(' * OS Type: ' . SystemTest::getSystemType());
        Screen::writeLine(' * HTTPD Server: ' . SystemTest::getServerVersion());
        Screen::writeLine(' * PHP version: ' . phpversion());
        Screen::writeLine('');

        Screen::writeLine('<strong>Installed software:</strong>');
        $this->loadedExtensions = array_flip(get_loaded_extensions());

        $this->loadedExtensions['security'] = true;
        $this->loadedExtensions['Apache'] = true;
        ksort($this->loadedExtensions);

        foreach($this->loadedExtensions as $extensionName => $trash) {

            $className = ucfirst($extensionName).'Test';
            if(!class_exists($className)) {
                $test = new GenericTest($extensionName);
            } else {
                $test = new $className($extensionName);
            }

            if(!$test->isPresent()) {
                continue;
            }
            Screen::write(" * ".$extensionName);
            Screen::write(' version: '.$test->getVersion());

            if($test->isVulnerable()) {
                Screen::writeLine(' - found ' . (count($test->getVulnerabilities())). ' vulnerabilities: ');
                $list = array();
                foreach($test->getVulnerabilities() as $vulnerability) {
                    $cve = '';
                    $bug = is_array($vulnerability) ? $vulnerability[0] : $vulnerability;
                    if(is_array($vulnerability)) {
                        if(!empty($vulnerability[2])) {
                            $bug = '[<a href="' . $vulnerability[2] . '" target="_blank">Link</a>] ' . $vulnerability[0];
                        } else {
                            $bug = $vulnerability[0];
                        }

                        if(!empty($vulnerability[1])) {
                            $cve = ' (<a href="http://cve.mitre.org/cgi-bin/cvename.cgi?name=' . $vulnerability[1] . '">' . $vulnerability[1] . '</a>)';
                        }
                    } else {
                        $bug = $vulnerability;
                    }
                    Screen::writeLine('<font color="#775555"> - ' . $bug . $cve . '</font>');
                }

            } else {
                Screen::write(' - no vulnerability detected');
            }

            Screen::writeLine("");
        }

        Screen::writeLine('</pre>');
    }
}

/**
 * Generict test extended by others.
 */
class GenericTest
{
    /**
     * List of detected vulnerabilities.
     *
     * @var mixed[]
     */
    protected $foundVulnerabilities = array();

    /**
     * Extension name.
     *
     * @var string
     */
    protected $extensionName = "";

    /**
     * Extensions versions and their name mappings.
     *
     * @var string[]
     */
    protected $extensionsVersions = array(
        'dbg' => 'NuSphere Debugger',
        'curl' => 'cURL Information',
        'gd' => 'GD Version',
        'iconv' => 'iconv library version',
        'libxml' => 'libXML Compiled Version',
        'pcre' => 'PCRE Library Version',
        'pgsql' => 'PostgreSQL(libpq) Version',
        'xml' => 'libxml2 Version',
    );

    /**
     * Runs the test.
     *
     * @param string $extensionName Extension name.
     * @return GenericTest
     */
    public function __construct($extensionName) {
        $this->extensionName = $extensionName;
        $this->checkVersions();
    }

    /**
     * Detects vulnerabilities.
     *
     * @return void
     */
    protected function checkVersions() {

    }

    /**
     * Checks if this module is present.
     *
     * @return bool
     */
    public function isPresent() {
        return true;
    }

    /**
     * Checks if this module is vulnerable.
     *
     * @return bool
     */
    public function isVulnerable() {
        return isset($this->foundVulnerabilities[0]);
    }

    /**
     * Returns full module info.
     *
     * @param string $extensionName Extension name.
     * @param string $settingName Setting name.
     * @return string
     */
    protected function getFullInfo($extensionName = null, $settingName = null) {
        static $info_arr = array();
        if(count($info_arr) == 0) {
            ob_start();
            phpinfo();

            $info_lines = explode("\n", strip_tags(ob_get_clean(), "<tr><td><h2>"));
            $cat = "General";
            foreach($info_lines as $line) {
                // new cat?
                preg_match("~<h2>(.*)</h2>~", $line, $title) ? $cat = $title[1] : null;
                if(preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val)) {
                    $info_arr[$cat][trim($val[1])] = trim($val[2]);
                }
                else if(preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val)) {
                    $info_arr[$cat][trim($val[1])] = array("local" => trim($val[2]), "master" => trim($val[3]));
                }
            }
        }

        if(!$extensionName) {
            return $info_arr;
        }
        if(isset($info_arr[$extensionName])) {
            if($settingName) {
                if(isset($info_arr[$extensionName][$settingName])) {
                    return $info_arr[$extensionName][$settingName];
                } else {
                    return;
                }
            }
            return $info_arr[$extensionName];
        }
        return;
    }

    /**
     * Returns module version.
     *
     * @return string
     */
    public function getVersion($extensionName = null) {
        $version = phpversion($extensionName ? $extensionName : $this->extensionName);
        if(!$version) {
            if(isset($this->extensionsVersions[$this->extensionName])) {
                $info = $this->getFullInfo($this->extensionName, $this->extensionsVersions[$this->extensionName]);
            } else {
                $info = $this->getFullInfo($this->extensionName, 'Version');
            }
            if($info) {
                $version = $info;
            }
        }

        return $version;
    }

    /**
     * Retuns a list of detected vulnerabilities.
     *
     * @return mixed[]
     */
    public function getVulnerabilities() {
        return $this->foundVulnerabilities;
    }
}

/**
 * Basic security test.
 */
class SecurityTest extends GenericTest
{
    /**
     * List of dangerous PHP functions.
     *
     * @var string[]
     */
    protected $badFunctions = array(
        'dl',
        'exec',
        'shell_exec',
        'system',
        'passthru',
        'popen',
        'symlink',
        'link',
        'set_time_limit',
        'proc_open',
        'proc_close',
        'posix_getpwuid',
        'openlog',
        'proc_nice',
        'pcntl_exec'
    );

    /**
     * Checks the PHP settings
     *
     * @return void
     */
    protected function checkVersions() {
        if(ini_get('open_basedir')) {
            $this->foundVulnerabilities[] = 'open_basedir enabled but is insecure by design';
        } else {
            if(isset($_SERVER['GATEWAY_INTERFACE']) && preg_match('~CGI~', $_SERVER['GATEWAY_INTERFACE'])) {
                // ok
            } else {
                $this->foundVulnerabilities[] = 'open_basedir disabled and PHP is not running as a CGI process';
            }
        }

        if(ini_get('safe_mode') && version_compare(phpversion(), '<', '5.3.0')) {
            $this->foundVulnerabilities[] = 'safe_mode enabled but is insecure by design';
        } else {
            if(isset($_SERVER['GATEWAY_INTERFACE']) && preg_match('~CGI~', $_SERVER['GATEWAY_INTERFACE'])) {
                // ok
            } else if (version_compare(phpversion(), '<', '5.3.0')){
                $this->foundVulnerabilities[] = 'safe_mode disabled and PHP is not running as a CGI process';
            } else {
                $this->foundVulnerabilities[] = 'safe_mode (deprecated) is disabled and PHP is not running as a CGI process';
            }
        }

        if($functions = ini_get('disable_functions')) {
        } else {
            $functions = '';
        }
        $functions = explode(',', $functions);
        array_walk($functions, 'trim');
        $badFunctions = array_diff($this->badFunctions, $functions);
        foreach($badFunctions as $index => $value) {
            if(!function_exists($value)) {
                unset($badFunctions[$index]);
            }
        }
        if(!empty($badFunctions)) {
            $this->foundVulnerabilities[] = 'Unsafe functions available: ' . implode(', ', $badFunctions);
        }

    }
}

/**
 * PHP test.
 */
class CoreTest extends GenericTest
{
    /**
     * List of known PHP vulnerabilities.
     *
     * @var mixed[]
     */
    protected $vulnerabilityTypes = array(
        1 => array('Remote code execution', 'CVE-2012-0830'),
        2 => 'Denial of Service (hash collisions)',
        3 => 'crypt() returns only the salt for MD5',
        4 => array('PHP hangs on numeric value 2.2250738585072011e-308', 'CVE-2010-4645', 'http://bugs.php.net/53632'),
        5 => array('Regression in open_basedir handling', null, 'http://bugs.php.net/53516'),
        6 => array('Old crypt_blowfish version', 'CVE-2011-2483', 'http://php.net/security/crypt_blowfish'),
        8 => array('Possible double free in imap extension', 'CVE-2010-4150'),
        9 => array('NULL pointer dereference in ZipArchive::getArchiveComment', 'CVE-2010-3709'),
        10=> array('Possible flaw in open_basedir', 'CVE-2010-3436'),
        11=> array('Format string vulnerability in the phar extension', 'CVE-2010-2950'),
        12=> array('Segfault in filter_var with FILTER_VALIDATE_EMAIL with large amount of data', 'CVE-2010-2950'),
        13=> array('var_export() data disclosure if a fatal error occurs', 'CVE-2010-2531'),
        19=> array('NULL pointer dereference when processing invalid XML-RPC requests', 'CVE-2010-0397', 'http://bugs.php.net/51288'),
        20=> array('SplObjectStorage unserialization problems', 'CVE-2010-2225'),
        21=> array('Possible interruption array leak in strchr(), strstr(), substr(), chunk_split(), strtok(), addcslashes(), str_repeat(), trim()', 'CVE-2010-2484'),
        26=> array('crypt() ignores sha512 prefix', null, 'http://bugs.php.net/50334'),
        27=> array('Denial of service - SoapClient does not honor max_redirects', null, 'http://bugs.php.net/48590'),
        32=> array('safe_mode_include_dir fails', null, 'http://bugs.php.net/50063'),
        35=> array('FILTER_SANITIZE_EMAIL allows disallowed characters', null, 'http://bugs.php.net/49470'),
        36=> array('"disable_functions" php.ini option does not work on Zend extensions', null, 'http://bugs.php.net/49065'),
        37=> array('proc_open() can bypass safe_mode_protected_env_vars restrictions', null, 'http://bugs.php.net/49026'),
        38=> array('Insufficient input string validation of htmlspecialchars()', null, 'http://bugs.php.net/49785'),
        39=> array('safe_mode bypass with exec()/system()/passthru() on Windows', null, 'http://bugs.php.net/45997'),
        40=> array('Arbitrary memory access in the imageRotate function', 'CVE-2008-5498'),
        41=> array('Broken broke magic_quotes_gpc', null, 'http://bugs.php.net/42718'),
        42=> array('Denial of Service / possible arbitrary code execution in PCRE', 'CVE-2008-2371'),
        45=> array('Denial of Service / possible arbitrary code execution in GD2', 'CVE-2008-3658'),
        46=> array('Possible buffer overflows inside memnstr()', 'CVE-2008-3659'),
        47=> array('safe_mode bypass with posix_access()', 'CVE-2008-2665'),
        48=> array('Multiple directory traversal vulnerabilities with ftok()/chdir()', 'CVE-2008-2666'),
        49=> array('Buffer overflows in IMAP toolkit rfc822.c legacy routine', 'CVE-2008-2829'),
        52=> array('Arbitrary code execution via a crafted URI', 'CVE-2008-0599'),
        57=> array('Automatic session id insertion adds sessions id to non-local forms', null, 'http://bugs.php.net/42869'),
        58=> array('Values set with php_admin_* in httpd.conf can be overwritten with ini_set()', null, 'http://bugs.php.net/41561'),
        60 => array('Ini parser crashes when using ${xxxx} ini variables', null, 'http://bugs.php.net/61650'),
        61 => array('call_user_func_array with more than 16333 arguments leaks / crashes', null, 'http://bugs.php.net/61273'),
        62 => array('Missing checks around malloc() calls', null, 'https://bugs.php.net/bug.php?id=61461'),
        63 => array('PHP-CGI query string parameter vulnerability', 'CVE-2012-1823', 'http://bugs.php.net/61910'),
        64 => array('Buffer Overflow in apache_request_headers', 'CVE-2012-2329', 'http://bugs.php.net/61807'),
        65 => array('The crypt_des function in FreeBSD before 9.0-RELEASE-p2 does not process the complete cleartext password if this password contains a 0x80 character', 'CVE-2012-2143', null),
        66 => array('Invalid memory access when incrementally assigning to a member of a null object', null, 'http://bugs.php.net/62005'),
        67 => array('Object recursion not detected for classes that implement JsonSerializable', '', 'http://bugs.php.net/61978'),
        68 => array('A parsing bug in the prepared statements can lead to access violations', null, 'http://bugs.php.net/61755'),
        69 => array('Integer overflow in the phar_parse_tarfile function', 'CVE-2012-2386', 'http://bugs.php.net/61065'),
        70 => array('Potential overflow in _php_stream_scandir', 'CVE-2012-2688', null),
        71 => array('ReflectionMethod random corrupt memory on high concurrent', null, 'http://bugs.php.net/62432'),
        72 => array('Crypt SHA256/512 Segfaults With Malformed Salt', null, 'http://bugs.php.net/62443'),
        73 => array('Invalid phar stream path causes crash', null, 'http://bugs.php.net/62227'),
        74 => array('open_basedir bypass in SQLite', 'CVE-2012-3365', null),
        75 => array('Erealloc in iconv.c unsafe', null, 'http://bugs.php.net/55042'),
        76 => array('unset($array[$float]) causes a crash', null, 'http://bugs.php.net/62653'),
        77 => array('Only one directive is loaded from "Per Directory Values" Windows registry', null, 'http://bugs.php.net/62955'),
        78 => array('Unserialize invalid DateTime causes crash', null, 'http://bugs.php.net/62852'),
        79 => array('Segfault or broken object references on unserialize()', null, 'http://bugs.php.net/62836'),
        80 => array('Open_basedir bypassed by soap.wsdl_cache_dir', 'CVE-2013-1635', null),
        81 => array('SOAP parser allows remote attackers to read arbitrary files via a SOAP WSDL file', 'CVE-2013-1643', null),
        82 => array('Heap based buffer overflow in quoted_printable_encode', 'CVE-2013-2110', null),
        83 => array('Possible XSS on "Registered stream filters" info', null, 'http://bugs.php.net/62964'),
        84 => array('CLI server not responsive when responding with 422 http status code', null, 'http://bugs.php.net/65066'),

        0 => 'THIS VERSION IS OBSOLETE AND NEEDS TO BE UPGRADED (It reached the End of Life phase)',
        7 => 'Paths with NULL in them (foo\0bar.txt) are considered valid',
        14 => 'Possible buffer overflows in mysqlnd_list_fields, mysqlnd_change_user',
        15 => 'Possible buffer overflows when handling error packets in mysqlnd',
        16 => 'Possible information leak because of interruption of XOR operator',
        17 => 'Possible dechunking filter buffer overflow',
        18 => 'Possible arbitrary memory access inside sqlite extension',
        22 => 'Weak LCG entropy',
        23 => 'Broken safe_mode validation inside tempnam() when the directory path does not end with a /',
        24 => 'Possible open_basedir/safe_mode bypass in the session extension',
        25 => 'Missing host validation for HTTP urls inside FILTER_VALIDATE_URL',
        28 => 'Possible DOS via temporary file exhaustion',
        29 => 'Missing sanity checks around exif processing',
        30 => 'safe_mode bypass in tempnam()',
        31 => 'open_basedir bypass in posix_mkfifo()',
        33 => 'Broken certificate validation inside php_openssl_apply_verification_policy',
        34 => 'Possible bad caching of symlinked directories in the realpath cache on Windows',
        43 => 'Missing initialization of BG(page_uid) and BG(page_gid)',
        44 => 'Incorrect php_value order for Apache configuration',
        50 => 'Possible stack buffer overflow in FastCGI SAPI',
        51 => 'Vulnerability to incomplete multibyte chars inside escapeshellcmd()',
        53 => 'safe_mode bypass in cURL',
        54 => 'htmlentities()/htmlspecialchars() accept partial multibyte sequences',
        55 => 'Possible buffer overflows inside glibc implementations of the fnmatch(), setlocale() and glob()',
        56 => '"mail.force_extra_parameters" php.ini directive can be modifiable in .htaccess',
        59 => 'Regression in glob() when enforcing safe_mode/open_basedir checks on paths containing "*"',
    );

    /**
     * List of vulnerable PHP versions.
     *
     * @var mixed[]
     */
    protected $vulnerableVersions = array(
        '5.5.1' => array(84),
        '5.4.17' => array(83, 84),
        '5.4.16' => array(83, 84),
        '5.4.15' => array(82, 83, 84),
        '5.4.14' => array(82, 83, 84),
        '5.4.13' => array(82, 83, 84),
        '5.4.12' => array(80, 81, 82, 83, 84),
        '5.4.11' => array(80, 81, 82, 83, 84),
        '5.4.10' => array(80, 81, 82, 83, 84),
        '5.4.9' => array(80, 81, 82, 83, 84),
        '5.4.8' => array(80, 81, 82, 83, 84),
        '5.4.7' => array(80, 81, 82, 83, 84),
        '5.4.6' => array(77, 78, 79, 80, 81, 82, 83, 84),
        '5.4.5' => array(77, 76, 78, 79, 80, 81, 82, 83, 84),
        '5.4.4' => array(70, 71, 72, 73, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84),
        '5.4.3' => array(60, 65, 66, 67, 68, 69, 70, 71, 72, 73, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84),
        '5.4.2' => array(60, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84),
        '5.4.1' => array(60, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84),
        '5.4.0' => array(60, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84),
        '5.3.2[0-2]' => array(0, 80, 81, 82),
        '5.3.1[7-9]' => array(0, 80, 81, 82),
        '5.3.16' => array(0, 77, 78, 80, 81, 82),
        '5.3.15' => array(0, 77, 78, 80, 81, 82),
        '5.3.14' => array(0, 70, 71, 72, 73, 74, 77, 78, 80, 81, 82),
        '5.3.13' => array(0, 65, 66, 69, 70, 71, 72, 73, 74, 77, 78, 80, 81, 82),
        '5.3.12' => array(0, 63, 65, 66, 69, 70, 71, 72, 73, 74, 77, 78, 80, 81, 82),
        '5.3.11' => array(0, 63, 65, 66, 69, 70, 71, 72, 73, 74, 77, 78, 80, 81, 82),
        '5.3.10' => array(0, 60, 61, 63, 65, 66, 69, 70, 71, 72, 73, 74, 77, 78, 80, 81, 82),
        '5.3.9' => array(0, 1, 60, 61, 63, 65, 66, 69, 70, 71, 72, 73, 74, 77, 78, 80, 81, 82),
        '5.3.8' => array(0, 2, 60, 61, 63, 65, 66, 69, 70, 71, 72, 73, 74, 77, 78, 80, 81, 82),
        '5.3.7' => array(0, 2, 3, 60, 61, 63, 65, 66, 69, 70, 71, 72, 73, 74, 77, 78, 80, 81, 82),
        '5.3.4' => array(0, 2, 4, 6, 60, 61, 63, 65, 66, 69, 70, 71, 72, 73, 74, 77, 78, 80, 81, 82),
        '5.3.3' => array(0, 2, 4, 6, 8, 9, 10, 11, 12, 60, 61, 63, 65, 66, 69, 70, 71, 72, 73, 74, 77, 78, 80, 81, 82),
        '5.3.2' => array(0, 2, 4, 6, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 60, 61, 63, 65, 66, 69, 70, 71, 72, 73, 74, 77, 78, 80, 81, 82),
        '5.3.1' => array(0, 2, 4, 6, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 22, 23, 24, 25, 26, 27, 60, 61, 63, 65, 66, 69, 70, 71, 72, 73, 74, 77, 78, 80, 81, 82),
        '5.3.0' => array(0, 2, 4, 6, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 60, 61, 63, 65, 66, 69, 70, 71, 72, 73, 74, 77, 78, 80, 81, 82),
        '5.2.16' => array(0, 2, 6, 60, 61, 63, 65, 66),
        '5.2.15' => array(0, 2, 4, 5, 6, 60, 61, 63, 65, 66),
        '5.2.14' => array(0, 2, 4, 6, 8, 9, 12, 60, 61, 63, 65, 66),
        '5.2.13' => array(0, 2, 4, 6, 8, 9, 12, 13, 18, 19, 20, 60, 61, 63, 65, 66),
        '5.2.12' => array(0, 2, 4, 6, 8, 9, 12, 13, 18, 19, 20, 22, 23, 24, 25, 60, 61, 63, 65, 66),
        '5.2.11' => array(0, 2, 4, 6, 8, 9, 12, 13, 18, 19, 20, 22, 23, 24, 25, 28, 30, 31, 38, 60, 61, 63, 65, 66),
        '5.2.10' => array(0, 2, 4, 6, 8, 9, 12, 13, 18, 19, 20, 22, 23, 24, 25, 28, 29, 30, 31, 33, 35, 37, 38, 60, 61, 63, 65, 66),
        '5.2.9' => array(0, 2, 4, 6, 8, 9, 12, 13, 18, 19, 20, 22, 23, 24, 25, 28, 29, 30, 31, 33, 35, 37, 38, 39, 60, 61, 63, 65, 66),
        '5.2.8' => array(0, 2, 4, 6, 8, 9, 12, 13, 18, 19, 20, 22, 23, 24, 25, 28, 29, 30, 31, 33, 35, 37, 38, 39, 40, 60, 61, 63, 65, 66),
        '5.2.7' => array(0, 2, 4, 6, 8, 9, 12, 13, 18, 19, 20, 22, 23, 24, 25, 28, 29, 30, 31, 33, 35, 37, 38, 39, 40, 41, 60, 61, 63, 65, 66),
        '5.2.6' => array(0, 2, 4, 6, 8, 9, 12, 13, 18, 19, 20, 22, 23, 24, 25, 28, 29, 30, 31, 33, 35, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 60, 61, 63, 65, 66),
        '5.2.5' => array(0, 2, 4, 6, 8, 9, 12, 13, 18, 19, 20, 22, 23, 24, 25, 28, 29, 30, 31, 33, 35, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 60, 61, 63, 65, 66),
        '5.[0-2].' => array(0, 2, 4, 6, 8, 9, 12, 13, 18, 19, 20, 22, 23, 24, 25, 28, 29, 30, 31, 33, 35, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 63, 65, 66),
    ); // 5.3.4

    /**
     * Detects vulnerabilities.
     *
     * @return void
     */
    protected function checkVersions() {
        $version = $this->getVersion();
        foreach($this->vulnerableVersions as $vulnerableVersion => $vulnerabilities) {
            if(preg_match('~'.str_replace('.', '\.', $vulnerableVersion).'~', $version)) {
                foreach($vulnerabilities as $vulnerability) {
                    $this->foundVulnerabilities[] = $this->vulnerabilityTypes[$vulnerability];
                }
                return;
            }
        }
    }

}

/**
 * Checks for debugger.
 */
class DbgTest extends GenericTest
{
    /**
     * List of known vulnerabilities.
     *
     * @var string[]
     */
    protected $foundVulnerabilities = array('Debugger detected - possibility of sensitive data disclosure');

    /**
     * Detects vulnerabilities.
     *
     * @return void
     */
    protected function checkVersions() {
        return;
    }
}

/**
 * Checks for debugger.
 */
class Xdebug extends DbgTest
{

}

/**
 * Apache HTTP test.
 */
class ApacheTest extends GenericTest
{
    /**
     * List of known vulnerabilities.
     *
     * @var mixed[]
     */
    protected $vulnerabilityTypes = array(
        0 => array('1.[0-3].[0-9]+',
            null, 'high', 'THIS VERSION IS OBSOLETE AND NEEDS TO BE UPGRADED (It reached the End of Life phase)', null, null),

        1 => array('1.3.[0-9]+',
            'mod_proxy', 'moderate', 'mod_proxy reverse proxy exposure', 'CVE-2011-3368', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        2 => array('1.3.(41|39|37|36|35|34|33|32|31|29|28|27|26|24|22|20|19|17|14|12|11|9|6|4|3|2)',
            'mod_proxy', 'moderate', 'mod_proxy overflow on 64-bit systems', 'CVE-2010-0010', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        3 => array('1.3.(39|37|36|35|34|33|32|31|29|28|27|26|24|22|20|19|17|14|12|11|9|6|4|3|2)',
            'mod_status', 'moderate', 'mod_status XSS', 'CVE-2007-6388', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        4 => array('1.3.(39|37|36|35|34|33|32|31|29|28|27|26|24|22|20|19|17|14|12|11|9|6|4|3|2)',
            'mod_imap', 'moderate', 'mod_imap XSS', 'CVE-2007-5000', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        5 => array('1.3.(37|36|35|34|33|32|31|29|28|27|26|24|22|20|19|17|14|12|11|9|6|4|3|2)',
            'mod_status', 'moderate', 'mod_status XSS', 'CVE-2006-5752', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        6 => array('1.3.(37|36|35|34|33|32|31|29|28|27|26|24|22|20|19|17|14|12|11|9|6|4|3|2|1|0)',
            null, 'moderate', 'Signals to arbitrary processes', 'CVE-2007-3304', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        7 => array('1.3.(36|35|34|33|32|31|29|28)',
            'mod_rewrite', 'important', 'mod_rewrite off-by-one error', 'CVE-2006-3747', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        8 => array('1.3.(34|33|32|31|29|28|27|26|24|22|20|19|17|14|12|11|9|6|4|3)',
            null, 'moderate', 'Expect header Cross-Site Scripting', 'CVE-2006-3918', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        9 => array('1.3.(34|33|32|31|29|28|27|26|24|22|20|19|17|14|12|11|9|6|4|3|2|1|0)',
            'mod_imap', 'moderate', 'mod_imap Referer Cross-Site Scripting', 'CVE-2005-3352', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        10 => array('1.3.(32|31|29|28|27|26|24|22|20|19|17|14|12|11|9|6|4|3|2|1|0)',
            'mod_include', 'moderate', 'mod_include overflow', 'CVE-2004-0940', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        11 => array('1.3.(31|29|28|27|26)',
            'mod_proxy', 'moderate', 'mod_proxy buffer overflow', 'CVE-2004-0492', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        12 => array('1.3.(29|28|27|26|24|22|20|19|17|14|12|11|9|6|4|3|2|1|0)',
            null, 'important', 'Listening socket starvation', 'CVE-2004-0174', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        13 => array('1.3.(29|28|27|26|24|22|20|19|17|14|12|11|9|6|4|3|2|1|0)',
            null, 'important', 'Allow/Deny parsing on big-endian 64-bit platforms', 'CVE-2003-0993', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        14 => array('1.3.(29|28|27|26|24|22|20|19|17|14|12|11|9|6|4|3|2|1|0)',
            null, 'low', 'Error log escape filtering', 'CVE-2003-0020', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        15 => array('1.3.(29|28|27|26|24|22|20|19|17|14|12|11|9|6|4|3|2|1|0)',
            'mod_digest', 'low', 'mod_digest nonce checking', 'CVE-2003-0987', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        16 => array('1.3.(28|27|26|24|22|20|19|17|14|12|11|9|6|4|3|2|1|0)',
            null, 'low', 'Local configuration regular expression overflow', 'CVE-2003-0542', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        17 => array('1.3.(27|26|24|22|20|19|17|14|12|11|9|6|4|3|2|1|0)',
            array(null, 'SystemTest::isWindows() || SystemTest::isOS2()'), 'important', 'RotateLogs DoS', 'CVE-2003-0460', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        18 => array('1.3.(26|24|22|20|19|17|14|12|11|9|6|4|3|2|1|0)',
            null, 'important', 'Buffer overflows in ab utility', 'CVE-2002-0843', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        19 => array('1.3.(26|24|22|20|19|17|14|12|11|9|6|4|3|2|1|0)',
            null, 'important', 'Shared memory permissions lead to local privilege escalation', 'CVE-2002-0839', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        20 => array('1.3.(26|24|22|20|19|17|14|12|11|9|6|4|3|2|1|0)',
            null, 'low', 'Error page XSS using wildcard DNS', 'CVE-2002-0840', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        21 => array('1.3.(24|22|20|19|17|14|12|11|9|6|4|3|2|1|0)',
            null, 'critical', 'Apache Chunked encoding vulnerability', 'CVE-2002-0392', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        22 => array('1.3.(24|22|20|19|17|14|12|11|9|6|4|3|2|1|0)',
            null, 'low', 'Filtered escape sequences', 'CVE-2003-0083', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        23 => array('1.3.(22|20|19|17|14|12|11|9|6|4|3|2|1|0)',
            array(null, 'SystemTest::isWindows()'), 'critical', 'Win32 Apache Remote command execution', 'CVE-2002-0061', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        24 => array('1.3.20',
            null, 'important', 'Requests can cause directory listing to be displayed', 'CVE-2001-0729', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        25 => array('1.3.(20|19|17|14|12|11|9|6|4|3|2|1|0)',
            null, 'important', 'Multiviews can cause a directory listing to be displayed', 'CVE-2001-0731', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        26 => array('1.3.(20|19|17|14|12|11|9|6|4|3|2|1|0)',
            null, 'moderate', 'split-logfile can cause arbitrary log files to be written to', 'CVE-2001-0730', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        27 => array('1.3.(19|17|14|12|11|9|6|4|3|2|1|0)',
            array(null, 'SystemTest::isWindows() || SystemTest::isOS2()'), 'important', 'Denial of service attack on Win32 and OS2', 'CVE-2001-1342', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        28 => array('1.3.(17|14|12|11)',
            null, 'important', 'Requests can cause directory listing to be displayed', 'CVE-2001-0925', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        29 => array('1.3.(12|11|9|6|4|3|2|1|0)',
            'mod_rewrite', 'important', 'Rewrite rules that include references allow access to any file', 'CVE-2000-0913', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        30 => array('1.3.(12|11|9)',
            'mod_vhost_alias', 'important', 'Mass virtual hosting can display CGI source', 'CVE-2000-1204', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        31 => array('1.3.(12|11|9|6|4|3|2|1|0)',
            array(null, 'SystemTest::isWindows()'), 'moderate', 'Requests can cause directory listing to be displayed on NT', 'CVE-2000-0505', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        32 => array('1.3.(11|9|6|4|3|2|1|0)',
            null, 'important', 'Cross-site scripting can reveal private session information', 'CVE-2000-1205', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        33 => array('1.3.(9|6|4|3|2|1|0)',
            null, 'moderate', 'Mass virtual hosting security issue', 'CVE-2000-1206', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        34 => array('1.3.(3|2|1|0)',
            array(null, 'SystemTest::isWindows()'), 'important', 'Denial of service attack on Win32', null, 'http://httpd.apache.org/security/vulnerabilities_13.html'),
        35 => array('1.3.(1|0)',
            null, 'important', 'Multiple header Denial of Service vulnerability', 'CVE-1999-1199', 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        36 => array('1.3.(1|0)',
            null, 'important', 'Denial of service attacks', null, 'http://httpd.apache.org/security/vulnerabilities_13.html'),

        // ------------------------

        37 => array('2.0.64, 2.0.63, 2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'important', 'Range header remote DoS', 'CVE-2011-3192', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        38 => array('2.0.64, 2.0.63, 2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_proxy', 'moderate', 'mod_proxy reverse proxy exposure', 'CVE-2011-3368', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        39 => array('2.0.64, 2.0.63, 2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_proxy', 'moderate', 'apr_fnmatch flaw leads to mod_autoindex remote DoS', 'CVE-2011-0419', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        40 => array('2.0.64, 2.0.63, 2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_isapi', 'moderate', 'mod_isapi module unload flaw', 'CVE-2010-0425', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        41 => array('2.0.63, 2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37',
            'mod_isapi', 'important', 'mod_isapi module unload flaw', 'CVE-2010-0425', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        42 => array('2.0.63, 2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'low', 'expat DoS', 'CVE-2009-3720', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        43 => array('2.0.63, 2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'low', 'expat DoS', 'CVE-2009-3560', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        44 => array('2.0.63, 2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'low', 'apr_brigade_split_line DoS', 'CVE-2010-1623', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        45 => array('2.0.63, 2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'low', 'mod_dav DoS', 'CVE-2010-1452', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        46 => array('2.0.63, 2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'low', 'APR apr_palloc heap overflow', 'CVE-2009-2412', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        47 => array('2.0.63, 2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_deflate', 'low', 'mod_deflate DoS', 'CVE-2009-1891', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        48 => array('2.0.63, 2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_proxy_ftp', 'low', 'mod_proxy_ftp FTP command injection', 'CVE-2009-3095', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        49 => array('2.0.63, 2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_proxy_ftp', 'low', 'mod_proxy_ftp DoS', 'CVE-2009-3094', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        50 => array('2.0.63, 2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_headers', 'low', 'Subrequest handling of request headers (mod_headers)', 'CVE-2010-0434', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        51 => array('2.0.63, 2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_proxy_ftp', 'low', 'mod_proxy_ftp globbing XSS', 'CVE-2008-2939', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        52 => array('2.0.63, 2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_proxy_http', 'moderate', 'mod_proxy_http DoS', 'CVE-2008-2364', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        53 => array('2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_proxy_ftp', 'low', 'mod_proxy_ftp UTF-7 XSS', 'CVE-2008-0005', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        54 => array('2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_status', 'moderate', 'mod_status XSS', 'CVE-2007-6388', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        55 => array('2.0.61, 2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_imap', 'moderate', 'mod_imap XSS', 'CVE-2007-5000', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        56 => array('2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_proxy', 'moderate', 'mod_proxy crash', 'CVE-2007-3847', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        57 => array('2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_status', 'moderate', 'mod_status cross-site scripting', 'CVE-2006-5752', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        58 => array('2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'moderate', 'Signals to arbitrary processes', 'CVE-2007-3304', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        59 => array('2.0.59, 2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_cache', 'moderate', 'mod_cache proxy DoS', 'CVE-2007-1863', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        60 => array('2.0.58, 2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46',
            'mod_rewrite', 'important', 'mod_rewrite off-by-one error', 'CVE-2006-3747', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        61 => array('2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_ssl', 'low', 'mod_ssl access control DoS', 'CVE-2005-3357', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        62 => array('2.0.55, 2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_imap', 'moderate', 'mod_imap Referer Cross-Site Scripting', 'CVE-2005-3352', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        63 => array('2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_ssl', 'important', 'SSLVerifyClient bypass', 'CVE-2005-2700', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        64 => array('2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'low', 'Worker MPM memory leak', 'CVE-2005-2970', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        65 => array('2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'low', 'PCRE overflow', 'CVE-2005-2491', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        66 => array('2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_ssl', 'low', 'Malicious CRL off-by-one', 'CVE-2005-1268', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        67 => array('2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'moderate', 'Byterange filter DoS', 'CVE-2005-2728', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        68 => array('2.0.54, 2.0.53, 2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'moderate', 'HTTP Request Spoofing', 'CVE-2005-2088', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        69 => array('2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'important', 'Memory consumption DoS', 'CVE-2004-0942', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        70 => array('2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_disk_cache', 'low', 'mod_disk_cache stores sensitive headers', 'CVE-2004-1834', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        71 => array('2.0.52, 2.0.51, 2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_ssl', 'moderate', 'SSLCipherSuite bypass', 'CVE-2004-0885', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        72 => array('2.0.51',
            null, 'important', 'Basic authentication bypass', 'CVE-2004-0811', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        73 => array('2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'critical', 'IPv6 URI parsing heap overflow', 'CVE-2004-0786', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        74 => array('2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'important', 'SSL connection infinite loop', 'CVE-2004-0748', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        75 => array('2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'low', 'Environment variable expansion flaw', 'CVE-2004-0747', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        76 => array('2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44',
            'mod_ssl', 'low', 'Malicious SSL proxy can cause crash', 'CVE-2004-0751', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        77 => array('2.0.50, 2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_dav', 'low', 'WebDAV remote crash', 'CVE-2004-0809', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        78 => array('2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'important', 'Header parsing memory leak', 'CVE-2004-0493', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        79 => array('2.0.49, 2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_ssl', 'low', 'FakeBasicAuth overflow', 'CVE-2004-0488', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        80 => array('2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'important', 'Listening socket starvation', 'CVE-2004-0174', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        81 => array('2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_ssl', 'important', 'mod_ssl memory leak', 'CVE-2004-0113', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        82 => array('2.0.48, 2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'low', 'Error log escape filtering', 'CVE-2003-0020', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        83 => array('2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_rewrite', 'low', 'Local configuration regular expression overflow', 'CVE-2003-0542', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        84 => array('2.0.47, 2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_cgid', 'moderate', 'CGI output information leak', 'CVE-2003-0789', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        85 => array('2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'important', 'Remote DoS with multiple Listen directives', 'CVE-2003-0253', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        86 => array('2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_ssl', 'important', 'mod_ssl renegotiation issue', 'CVE-2003-0192', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        87 => array('2.0.46, 2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'moderate', 'Remote DoS via IPv6 ftp proxy', 'CVE-2003-0254', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        88 => array('2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37',
            null, 'critical', 'APR remote crash', 'CVE-2003-0245', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        89 => array('2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40',
            null, 'important', 'Basic Authentication DoS', 'CVE-2003-0189', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        90 => array('2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            array(null, 'SystemTest::isOS2()'), 'important', 'OS2 device name DoS', 'CVE-2003-0134', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        91 => array('2.0.45, 2.0.44, 2.0.43, 2.0.42, 2.0.40',
            null, 'low', 'Filtered escape sequences', 'CVE-2003-0083', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        92 => array('2.0.44, 2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'important', 'Line feed memory leak DoS', 'CVE-2003-0132', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        93 => array('2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'critical', 'MS-DOS device name filtering', 'CVE-2003-0016', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        94 => array('2.0.43, 2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'important', 'Apache can serve unexpected files', 'CVE-2003-0017', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        95 => array('2.0.42, 2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'low', 'Error page XSS using wildcard DNS', 'CVE-2002-0840', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        96 => array('2.0.42',
            null, 'moderate', 'CGI scripts source revealed using WebDAV', 'CVE-2002-1156', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        97 => array('2.0.40, 2.0.39, 2.0.37, 2.0.36, 2.0.35',
            'mod_dav', 'moderate', 'mod_dav crash', 'CVE-2002-1593', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        98 => array('2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'important', 'Path vulnerability', 'CVE-2002-0661', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        99 => array('2.0.39, 2.0.37, 2.0.36, 2.0.35',
            null, 'low', 'Path revealing exposures', 'CVE-2002-0654', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        100 => array('2.0.37, 2.0.36, 2.0.35',
            null, 'critical', 'Apache Chunked encoding vulnerability', 'CVE-2002-0392', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        101 => array('2.0.35',
            null, 'low', 'Warning messages could be displayed to users', 'CVE-2002-1592', 'http://httpd.apache.org/security/vulnerabilities_20.html'),

        102 => array('2.2.24, 2.2.23, 2.2.22, 2.2.21, 2.2.20, 2.2.19, 2.2.18, 2.2.17, 2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_rewrite', 'low', 'mod_rewrite log escape filtering', 'CVE-2013-1862', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        103 => array('2.2.24, 2.2.23, 2.2.22, 2.2.21, 2.2.20, 2.2.19, 2.2.18, 2.2.17, 2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_dav', 'moderate', 'mod_dav crash', 'CVE-2013-1896', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        104 => array('2.2.23, 2.2.22, 2.2.21, 2.2.20, 2.2.19, 2.2.18, 2.2.17, 2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            null, 'moderate', 'XSS in mod_proxy_balancer', 'CVE-2012-4558', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        105 => array('2.2.23, 2.2.22, 2.2.21, 2.2.20, 2.2.19, 2.2.18, 2.2.17, 2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            null, 'low', 'XSS due to unescaped hostnames', 'CVE-2012-3499', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        106 => array('2.2.22, 2.2.21, 2.2.20, 2.2.19, 2.2.18, 2.2.17, 2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_negotiation', 'low', 'XSS in mod_negotiation when untrusted uploads are supported', 'CVE-2012-2687', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        107 => array('2.2.22, 2.2.21, 2.2.20, 2.2.19, 2.2.18, 2.2.17, 2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            null, 'low', 'Insecure LD_LIBRARY_PATH handling', 'CVE-2012-0883', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        108 => array('2.2.21, 2.2.20, 2.2.19, 2.2.18, 2.2.17, 2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_setenvif', 'low', 'mod_setenvif .htaccess privilege escalation', 'CVE-2011-3607', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        109 => array('2.2.21, 2.2.20, 2.2.19, 2.2.18, 2.2.17, 2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_log_config', 'low', 'mod_log_config crash', 'CVE-2012-0021', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        110 => array('2.2.21, 2.2.20, 2.2.19, 2.2.18, 2.2.17, 2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            null, 'low', 'Scoreboard parent DoS', 'CVE-2012-0031', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        111 => array('2.2.21, 2.2.20, 2.2.19, 2.2.18, 2.2.17, 2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_proxy', 'moderate', 'mod_proxy reverse proxy exposure', 'CVE-2011-4317', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        112 => array('2.2.21, 2.2.20, 2.2.19, 2.2.18, 2.2.17, 2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            null, 'moderate', 'Error responses can expose cookies', 'CVE-2012-0053', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        113 => array('2.2.21, 2.2.20, 2.2.19, 2.2.18, 2.2.17, 2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_proxy', 'moderate', 'mod_proxy reverse proxy exposure', 'CVE-2011-3368', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        114 => array('2.2.20, 2.2.19, 2.2.18, 2.2.17, 2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12',
            'mod_proxy_ajp', 'moderate', 'mod_proxy_ajp remote DoS', 'CVE-2011-3348', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        115 => array('2.2.19, 2.2.18, 2.2.17, 2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            null, 'important', 'Range header remote DoS', 'CVE-2011-3192', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        116 => array('2.2.18, 2.2.17, 2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            null, 'moderate', 'apr_fnmatch flaw leads to mod_autoindex remote DoS', 'CVE-2011-0419', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        117 => array('2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            null, 'low', 'expat DoS', 'CVE-2009-3720', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        118 => array('2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            null, 'low', 'expat DoS', 'CVE-2009-3560', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        119 => array('2.2.16, 2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            null, 'low', 'apr_brigade_split_line DoS', 'CVE-2010-1623', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        120 => array('2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9',
            'mod_proxy_http', 'important', 'mod_proxy_http timeout detection flaw', 'CVE-2010-2068', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        121 => array('2.2.15, 2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_dav', 'low', 'mod_cache and mod_dav DoS', 'CVE-2010-1452', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        122 => array('2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_isapi', 'important', 'mod_isapi module unload flaw', 'CVE-2010-0425', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        123 => array('2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_headers', 'low', 'Subrequest handling of request headers', 'CVE-2010-0434', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        124 => array('2.2.14, 2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_proxy_ajp', 'moderate', 'mod_proxy_ajp DoS', 'CVE-2010-0408', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        125 => array('2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_proxy_ftp', 'low', 'mod_proxy_ftp DoS', 'CVE-2009-3094', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        126 => array('2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_proxy_ftp', 'low', 'mod_proxy_ftp FTP command injection', 'CVE-2009-3095', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        127 => array('2.2.13, 2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            null /*array(null, 'SystemTest::isSolaris()')*/, 'moderate', 'Solaris pollset DoS', 'CVE-2009-2699', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        128 => array('2.2.12, 2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            null, 'low', 'APR apr_palloc heap overflow', 'CVE-2009-2412', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        129 => array('2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_proxy', 'important', 'mod_proxy reverse proxy DoS', 'CVE-2009-1890', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        130 => array('2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_proxy_ajp', 'important', 'mod_proxy_ajp information disclosure', 'CVE-2009-1191', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        131 => array('2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_deflate', 'low', 'mod_deflate DoS', 'CVE-2009-1891', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        132 => array('2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            null, 'low', 'AllowOverride Options handling bypass', 'CVE-2009-1195', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        133 => array('2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_negotiation', 'low', 'CRLF injection in mod_negotiation when untrusted uploads are supported', 'CVE-2008-0456', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        134 => array('2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            null, 'moderate',  'APR-util off-by-one overflow', 'CVE-2009-1956', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        135 => array('2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            null, 'moderate',  'APR-util XML DoS', 'CVE-2009-1955', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        136 => array('2.2.11, 2.2.10, 2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            null, 'moderate',  'APR-util heap underwrite', 'CVE-2009-0023', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        137 => array('2.2.9',
            'mod_proxy_http', 'important',  'Timeout detection flaw (mod_proxy_http)', 'CVE-2010-2791', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        138 => array('2.2.9, 2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_proxy_ftp', 'low',  'mod_proxy_ftp globbing XSS', 'CVE-2008-2939', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        139 => array('2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_proxy_balancer', 'low',  'mod_proxy_balancer CSRF', 'CVE-2007-6420', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        140 => array('2.2.8, 2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_proxy_http', 'moderate',  'mod_proxy_http DoS', 'CVE-2008-2364', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        141 => array('2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_proxy_ftp', 'low',  'mod_proxy_ftp UTF-7 XSS', 'CVE-2008-0005', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        142 => array('2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_proxy_balancer', 'low',  'mod_proxy_balancer DoS', 'CVE-2007-6422', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        143 => array('2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_proxy_balancer', 'low',  'mod_proxy_balancer XSS', 'CVE-2007-6421', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        144 => array('2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_status', 'low',  'mod_status XSS', 'CVE-2007-6388', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        145 => array('2.2.6, 2.2.5, 2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_imagemap', 'low',  'mod_imagemap XSS', 'CVE-2007-5000', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        146 => array('2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_proxy', 'moderate',  'mod_proxy crash', 'CVE-2007-3847', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        147 => array('2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_status', 'moderate',  'mod_status cross-site scripting', 'CVE-2006-5752', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        148 => array('2.2.4, 2.2.3, 2.2.2, 2.2.0',
            null, 'moderate',  'Signals to arbitrary processes', 'CVE-2007-3304', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        149 => array('2.2.4',
            'mod_cache', 'moderate',  'mod_cache information leak', 'CVE-2007-1862', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        150 => array('2.2.4, 2.2.3, 2.2.2, 2.2.0',
            'mod_cache', 'moderate',  'mod_cache proxy DoS', 'CVE-2007-1863', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        151 => array('2.2.2, 2.2.0',
            'mod_rewrite', 'important',  'mod_rewrite off-by-one error', 'CVE-2006-3747', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        152 => array('2.2.0',
            'mod_ssl', 'low',  'mod_ssl access control DoS', 'CVE-2005-3357', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        153 => array('2.2.0',
            'mod_imap', 'moderate',  'mod_imap Referer Cross-Site Scripting', 'CVE-2005-3352', 'http://httpd.apache.org/security/vulnerabilities_22.html'),

        154 => array('2.4.1',
            null, 'low',  'Insecure LD_LIBRARY_PATH handling', 'CVE-2012-0883', 'http://httpd.apache.org/security/vulnerabilities_24.html'),

        155 => array('2.4.2, 2.4.1',
            'mod_proxy_http', 'important',  'Response mixup when using mod_proxy_ajp or mod_proxy_http', 'CVE-2012-3502', 'http://httpd.apache.org/security/vulnerabilities_24.html'),

        156 => array('2.4.2, 2.4.1',
            'mod_proxy_ajp', 'important',  'Response mixup when using mod_proxy_ajp or mod_proxy_http', 'CVE-2012-3502', 'http://httpd.apache.org/security/vulnerabilities_24.html'),

        157 => array('2.4.2, 2.4.1',
            'mod_negotiation', 'low',  'XSS in mod_negotiation when untrusted uploads are supported', 'CVE-2012-2687', 'http://httpd.apache.org/security/vulnerabilities_24.html'),

        158 => array('2.4.3, 2.4.2, 2.4.1',
            null, 'low',  'XSS due to unescaped hostnames', 'CVE-2012-3499', 'http://httpd.apache.org/security/vulnerabilities_24.html'),

        159 => array('2.4.3, 2.4.2, 2.4.1',
            'mod_proxy_balancer', 'moderate',  'XSS in mod_proxy_balancer', 'CVE-2012-4558', 'http://httpd.apache.org/security/vulnerabilities_24.html'),

        160 => array('2.4.4, 2.4.3, 2.4.2, 2.4.1',
            'mod_dav', 'moderate',  'mod_dav crash', 'CVE-2013-1896', 'http://httpd.apache.org/security/vulnerabilities_24.html'),

        161 => array('2.4.4, 2.4.3, 2.4.2, 2.4.1',
            'mod_session_dbd', 'moderate',  'mod_session_dbd session fixation flaw', 'CVE-2013-2249', 'http://httpd.apache.org/security/vulnerabilities_24.html'),

    );

    /**
     * Checks if Apache HTTPD is present.
     *
     * @return bool
     */
    public function isPresent() {
        return !is_null($this->getVersion());
    }

    /**
     * Detects Apache HTTPD version.
     *
     * @return string
     */
    public function getVersion($extensionName = null) {
        $version = SystemTest::getServerVersion();
        if(!preg_match('~^Apache/~', $version)) {
            return;
        }

        return SystemTest::extractVersion($version);
    }

    /**
     * Detects Apache HTTPD modules.
     *
     * @return string[]
     */
    public function getModules() {
        if(function_exists('apache_get_modules')) {
            $modules = apache_get_modules();
            return $modules;
        }
    }

    /**
     * Detects vulnerabilities.
     *
     * @return void
     */
    protected function checkVersions() {
        $version = $this->getVersion();
        if(!$version) {
            return;
        }

        $modules = $this->getModules();

        foreach($this->vulnerabilityTypes as $index => $vulnerabilities) {
            if(strpos($vulnerabilities[0], ',')) {
                $vulnerableVersions = explode(',', str_replace(' ', '', $vulnerabilities[0]));
                if(in_array($version, $vulnerableVersions)) {
                    if(!$vulnerabilities[1] || !$modules ||
                        (!is_array($vulnerabilities[1]) && in_array($vulnerabilities[1], $modules)) ||
                        (is_array($vulnerabilities[1]) && in_array($vulnerabilities[1][0], $modules))
                    ) {
                        $this->foundVulnerabilities[] = array_slice($vulnerabilities, 3);
                    }
                }
            } else {
                if(preg_match('~^'.str_replace('.', '\.', $vulnerabilities[0]).'$~', $version)) {
                    if(!$vulnerabilities[1] || !$modules ||
                        (!is_array($vulnerabilities[1]) && in_array($vulnerabilities[1], $modules)) ||
                        (is_array($vulnerabilities[1]) && in_array($vulnerabilities[1][0], $modules))
                    ) {
                        $this->foundVulnerabilities[] = array_slice($vulnerabilities, 3);
                    }
                }
            }
        }
    }
}

/**
 * System test.
 */
class SystemTest extends GenericTest
{
    /**
     * Extracts the version numbers.
     *
     * @param string $version Version string.
     * @return string Extracted version number.
     */
    public static function extractVersion($version) {
        if(preg_match('~([0-9]+\.[0-9]+(\.[0-9]+)*)~', $version, $matches)) {
            return $matches[1];
        }
    }

    /**
     * Detects HTTP server version.
     *
     * @return string.
     */
    public static function getServerVersion() {
        if(function_exists('apache_get_version')) {
            $version = apache_get_version();
        } else if(isset($_SERVER['SERVER_SOFTWARE'])) {
            $version = $_SERVER['SERVER_SOFTWARE'];
        } else {
            return;
        }

        return $version;
    }

    /**
     * Detects the Operating System type.
     *
     * @return string
     */
    public static function getSystemType() {
        if(function_exists('php_uname')) {
            $result = php_uname('s r');
            return $result;
        }

        if(function_exists('posix_uname')) {
            $result = posix_uname();
            return $result['sysname'] . '/' . $result['release'];
        }

        if(isset($_SERVER['OS'])) {
            return str_replace('_', ' ', $_SERVER['OS']);
        }

        return str_replace('_', ' ', PHP_OS);
    }
}

/**
 * Xml test
 */
class XmlTest extends GenericTest
{
    protected $vulnerabilityTypes = array(
        1 => array('Heap corruption in xml parser', 'CVE-2013-4113', 'http://bugs.php.net/65236'),
    );

    /**
     * List of vulnerable versions.
     *
     * @var mixed[]
     */
    protected $vulnerableVersions = array(
        '5.3.[0-9]' => array(1),
        '5.3.1[0-9]' => array(1),
        '5.3.2[0-6]' => array(1),
        '5.4.[0-9]' => array(1),
        '5.4.1[0-7]' => array(1),
        //'5.4.[0-9]' => array(1),
        //'5.4.1[0-5]' => array(1),
    );

    /**
     * Detects vulnerabilities.
     *
     * @return void
     */
    protected function checkVersions() {
        $version = $this->getVersion('Core');
        foreach($this->vulnerableVersions as $vulnerableVersion => $vulnerabilities) {
            if(preg_match('~'.str_replace('.', '\.', $vulnerableVersion).'~', $version)) {
                foreach($vulnerabilities as $vulnerability) {
                    $this->foundVulnerabilities[] = $this->vulnerabilityTypes[$vulnerability];
                }
                return;
            }
        }
    }
}

/**
 * Ftp test
 */
class FtpTest extends GenericTest
{
    protected $vulnerabilityTypes = array(
        1 => array('FTPs memory leak with SSL', null, 'http://bugs.php.net/65228'),
    );

    /**
     * List of vulnerable versions.
     *
     * @var mixed[]
     */
    protected $vulnerableVersions = array(
        '5.4.[0-9]' => array(1),
        '5.4.1[0-7]' => array(1),
    );

    /**
     * Detects vulnerabilities.
     *
     * @return void
     */
    protected function checkVersions() {
        $version = $this->getVersion();
        foreach($this->vulnerableVersions as $vulnerableVersion => $vulnerabilities) {
            if(preg_match('~'.str_replace('.', '\.', $vulnerableVersion).'~', $version)) {
                foreach($vulnerabilities as $vulnerability) {
                    $this->foundVulnerabilities[] = $this->vulnerabilityTypes[$vulnerability];
                }
                return;
            }
        }
    }
}

/**
 * Gmp test
 */
class GmpTest extends GenericTest
{
    protected $vulnerabilityTypes = array(
        1 => array('Memory leak in gmp_cmp second parameter', null, 'http://bugs.php.net/65227'),
    );

    /**
     * List of vulnerable versions.
     *
     * @var mixed[]
     */
    protected $vulnerableVersions = array(
        '5.4.[0-9]' => array(1),
        '5.4.1[0-7]' => array(1),
    );

    /**
     * Detects vulnerabilities.
     *
     * @return void
     */
    protected function checkVersions() {
        $version = $this->getVersion();
        foreach($this->vulnerableVersions as $vulnerableVersion => $vulnerabilities) {
            if(preg_match('~'.str_replace('.', '\.', $vulnerableVersion).'~', $version)) {
                foreach($vulnerabilities as $vulnerability) {
                    $this->foundVulnerabilities[] = $this->vulnerabilityTypes[$vulnerability];
                }
                return;
            }
        }
    }
}

/**
 * Openssl test
 */
class OpensslTest extends GenericTest
{
    protected $vulnerabilityTypes = array(
        1 => array('Fixed handling null bytes in subjectAltName', 'CVE-2013-4248', null),
        2 => array('UMR bug in fix for CVE-2013-4248', 'CVE-2013-4248', null),
    );

    /**
     * List of vulnerable versions.
     *
     * @var mixed[]
     */
    protected $vulnerableVersions = array(
        '5.4.[0-9]' => array(1),
        '5.4.1[0-7]' => array(1),
        '5.4.18' => array(2),
        '5.5.[0-2]' => array(1),
        '5.5.2' => array(2),
    );

    /**
     * Detects vulnerabilities.
     *
     * @return void
     */
    protected function checkVersions() {
        $version = $this->getVersion();
        foreach($this->vulnerableVersions as $vulnerableVersion => $vulnerabilities) {
            if(preg_match('~'.str_replace('.', '\.', $vulnerableVersion).'~', $version)) {
                foreach($vulnerabilities as $vulnerability) {
                    $this->foundVulnerabilities[] = $this->vulnerabilityTypes[$vulnerability];
                }
                return;
            }
        }
    }
}

/**
 * Session test
 */
class SessionTest extends GenericTest
{
    protected $vulnerabilityTypes = array(
        1 => array('Session fixation vulnerability in the Sessions subsystem', 'CVE-2011-4718', null),
    );

    /**
     * List of vulnerable versions.
     *
     * @var mixed[]
     */
    protected $vulnerableVersions = array(
        '5.3.[0-9]+' => array(1),
        '5.4.[0-9]+' => array(1),
        '5.5.[0-2]' => array(1),
    );

    /**
     * Detects vulnerabilities.
     *
     * @return void
     */
    protected function checkVersions() {
        $version = $this->getVersion('Core');
        foreach($this->vulnerableVersions as $vulnerableVersion => $vulnerabilities) {
            if(preg_match('~'.str_replace('.', '\.', $vulnerableVersion).'~', $version)) {
                foreach($vulnerabilities as $vulnerability) {
                    $this->foundVulnerabilities[] = $this->vulnerabilityTypes[$vulnerability];
                }
                return;
            }
        }
    }
}

/**
 * Postgre SQL test
 */
class Pdo_pgsqlTest extends GenericTest
{
    protected $vulnerabilityTypes = array(
        1 => array('Buffer overflow in _pdo_pgsql_error', null, 'http://bugs.php.net/64949'),
    );

    /**
     * List of vulnerable versions.
     *
     * @var mixed[]
     */
    protected $vulnerableVersions = array(
        '5.3.[0-9]' => array(1),
        '5.3.1[0-9]' => array(1),
        '5.3.2[0-6]' => array(1),
        '5.4.[0-9]' => array(1),
        '5.4.1[0-5]' => array(1),
    );

    /**
     * Detects vulnerabilities.
     *
     * @return void
     */
    protected function checkVersions() {
        $version = $this->getVersion();
        foreach($this->vulnerableVersions as $vulnerableVersion => $vulnerabilities) {
            if(preg_match('~'.str_replace('.', '\.', $vulnerableVersion).'~', $version)) {
                foreach($vulnerabilities as $vulnerability) {
                    $this->foundVulnerabilities[] = $this->vulnerabilityTypes[$vulnerability];
                }
                return;
            }
        }
    }
}

/**
 * Calendar Test
 */
class CalendarTest extends GenericTest
{
    protected $vulnerabilityTypes = array(
        1 => array('Integer overflow in SndToJewish', null, 'http://bugs.php.net/64895'),
    );

    /**
     * List of vulnerable versions.
     *
     * @var mixed[]
     */
    protected $vulnerableVersions = array(
        '5.3.[0-9]' => array(1),
        '5.3.1[0-9]' => array(1),
        '5.3.2[0-5]' => array(1),
        '5.4.[0-9]' => array(1),
        '5.4.1[0-5]' => array(1),
    );

    /**
     * Detects vulnerabilities.
     *
     * @return void
     */
    protected function checkVersions() {
        $version = $this->getVersion('core');
        foreach($this->vulnerableVersions as $vulnerableVersion => $vulnerabilities) {
            if(preg_match('~'.str_replace('.', '\.', $vulnerableVersion).'~', $version)) {
                foreach($vulnerabilities as $vulnerability) {
                    $this->foundVulnerabilities[] = $this->vulnerabilityTypes[$vulnerability];
                }
                return;
            }
        }
    }
}

/**
 * Zlib test.
 */
class ZlibTest extends GenericTest
{
    /**
     * List of known vulnerabilities.
     *
     * @var string[]
     */
    protected $vulnerabilityTypes = array(
        1 => 'Buffer Overflow',
        2 => 'Denial of Service',
    );

    /**
     * List of vulnerable versions.
     *
     * @var mixed[]
     */
    protected $vulnerableVersions = array(
        '1.2.[1-2]' => array(1, 2),
        '1.1.[0-3]' => array(2),
        '1.1$' => array(2)
    );

    /**
     * Detects vulnerabilities.
     *
     * @return void
     */
    protected function checkVersions() {
        $version = $this->getVersion();
        foreach($this->vulnerableVersions as $vulnerableVersion => $vulnerabilities) {
            if(preg_match('~'.str_replace('.', '\.', $vulnerableVersion).'~', $version)) {
                foreach($vulnerabilities as $vulnerability) {
                    $this->foundVulnerabilities[] = $this->vulnerabilityTypes[$vulnerability];
                }
                return;
            }
        }
    }

}

/**
 * Curl test.
 */
class CurlTest extends GenericTest
{
    /**
     * List of known vulnerabilities.
     *
     * @var string[]
     */
    protected $vulnerabilityTypes = array(
        1 => 'curl SSL CBC IV vulnerability',
        2 => 'curl URL sanitization vulnerability',
        3 => 'libcurl inapropriate GSSAPI delegation',
        4 => 'curl local file overwrite',
        5 => 'libcurl data callback extensive length',
        6 => 'libcurl embedded zero in cert name',
        7 => 'libcurl Arbitrary File Access',
        8 => 'libcurl GnuTLS insufficient cert verification',
        9 => 'libcurl TFTP Packet Buffer Overflow',
        10=> 'libcurl URL Buffer Overflow',
        11=> 'libcurl NTLM Buffer Overflow',
        12=> 'Kerberos Authentication Buffer Overflow',
        13=> 'NTLM Authentication Buffer Overflow',
        14=> 'Proxy Authentication Header Information Leakage',
        15=> 'FTP Server Response Buffer Overflow',
    );

    /**
     * List of vulnerable versions.
     *
     * @var mixed[]
     */
    protected $vulnerableVersions = array(
        '7.23.[0-1]' => array(1, 2),
        '7.22.0' => array(1, 2),
        '7.21.7' => array(1, 2),
        '7.21.[2-6]' => array(1, 2, 3),
        '7.2[0-1].[0-1]' => array(1, 2, 3, 4),
        '7.19.[6-7]' => array(1, 3, 5),
        '7.19.[4-5]' => array(1, 3, 5, 6),
        '7.19.[0-3]' => array(1, 3, 5, 6, 7),
        '7.18.[0-2]' => array(1, 3, 5, 6, 7),
        '7.17.[0-1]' => array(1, 3, 5, 6, 7),
        '7.16.4' => array(1, 3, 5, 6, 7),
        '7.16.[0-3]' => array(1, 3, 5, 6, 7, 8),
        '7.15.[3-5]' => array(1, 3, 5, 6, 7, 8),
        '7.15.[1-2]' => array(1, 3, 5, 6, 7, 8, 9),
        '7.15.0' => array(1, 3, 5, 6, 7, 8, 9, 10),
        '7.14.[0-1]' => array(1, 3, 5, 6, 7, 8, 10, 11),
        '7.14.[0-1]' => array(1, 3, 5, 6, 7, 8, 10, 11),
        '7.13.[1-2]' => array(1, 3, 5, 6, 7, 10, 11),
        '7.13.0' => array(1, 3, 5, 6, 7, 10, 11, 12, 13),
        '7.12.[0-3]' => array(1, 3, 5, 6, 7, 10, 11, 12, 13),
        '7.11.2' => array(1, 3, 5, 6, 7, 10, 11, 12, 13),
        '7.11.[0-1]' => array(1, 3, 5, 6, 7, 11, 12, 13),
        '7.10.[7-8]' => array(1, 3, 5, 6, 7, 11, 12, 13),
        '7.10.6' => array(1, 3, 5, 6, 7, 11, 12, 13, 14),
        '7.10.5' => array(5, 6, 7, 12, 14),
        '7.10.[0-4]' => array(6, 7, 12, 14),
        '7.10$' => array(6, 7, 12, 14),
        '7.[5-9]' => array(6, 7, 12, 14),
        '7.4.[1-2]' => array(6, 7, 12, 14),
        '7.4.0' => array(6, 7, 12, 14, 15),
        '7.4$' => array(6, 7, 12, 14, 15),
        '7.3.0' => array(7, 12, 14, 15),
        '7.3$' => array(7, 12, 14, 15),
        '7.[1-2].' => array(7, 14, 15),
        '7.[1-2]$' => array(7, 14, 15),
        '6.[0-9].[0-9]' => array(7, 15),
        '6.[0-9]$' => array(7, 15),

    );

    /**
     * Detects vulnerabilities.
     *
     * @return void
     */
    protected function checkVersions() {
        $version = $this->getVersion();
        foreach($this->vulnerableVersions as $vulnerableVersion => $vulnerabilities) {
            if(preg_match('~'.str_replace('.', '\.', $vulnerableVersion).'~', $version)) {
                foreach($vulnerabilities as $vulnerability) {
                    $this->foundVulnerabilities[] = $this->vulnerabilityTypes[$vulnerability];
                }
                return;
            }
        }
    }
}
