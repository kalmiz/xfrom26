<?php
// scripts/load.sqlite.php
 
/**
* Script for creating and loading database
*/
 
// Initialize the application path and autoloading
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH . '/../library',
    get_include_path(),
)));
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();
/**
 * Convert a comma separated file into an associated array.
 * The first row should contain the array keys.
 * 
 * Example:
 * 
 * @param string $filename Path to the CSV file
 * @param string $delimiter The separator used in the file
 * @return array
 * @link http://gist.github.com/385876
 * @author Jay Williams <http://myd3.com/>
 * @copyright Copyright (c) 2010, Jay Williams
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
function csv_to_array($filename='', $delimiter=',')
{
	if(!file_exists($filename) || !is_readable($filename))
		return FALSE;
	
	$header = NULL;
	$data = array();
	if (($handle = fopen($filename, 'r')) !== FALSE)
	{
		while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
		{
			if(!$header)
				$header = $row;
			else
				$data[] = array_combine($header, $row);
		}
		fclose($handle);
	}
	return $data;
} 

// Define some CLI options
$getopt = new Zend_Console_Getopt(array(
    'withdata|w' => 'Load database with sample data',
	'filelist|f-s' => 'List of data file to load',
    'env|e-s'    => 'Application environment for which to create database (defaults to development)',
    'help|h'     => 'Help -- usage message',
));
try {
    $getopt->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    // Bad options passed: report usage
    echo $e->getUsageMessage();
    return false;
}
 
// If help requested, report usage message
if ($getopt->getOption('h')) {
    echo $getopt->getUsageMessage();
    return true;
}
 
// Initialize values based on presence or absence of CLI options
$withData = $getopt->getOption('w');
$env      = $getopt->getOption('e');
$files    = $getopt->getOption('f');
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (null === $env) ? 'development' : $env);
 
// Initialize Zend_Application
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
 
// Initialize and retrieve DB resource
$bootstrap = $application->getBootstrap();
$bootstrap->bootstrap('db');
$dbAdapter = $bootstrap->getResource('db');
 
// let the user know whats going on (we are actually creating a
// database here)
//if ('testing' != APPLICATION_ENV) {
    //echo 'Writing Database Xfrom26 in (control-c to cancel): ' . PHP_EOL;
    //for ($x = 5; $x > 0; $x--) {
        //echo $x . "\r"; sleep(1);
    //}
//}
 
// this block executes the actual statements that were loaded from
// the schema file.
try {
    $schemaSql = file_get_contents(dirname(__FILE__) . '/../data/db/xfrom26.sql');
    // use the connection directly to load sql in batches
    $dbAdapter->getConnection()->exec($schemaSql);
 
    if ('testing' != APPLICATION_ENV) {
        echo PHP_EOL;
        echo 'Database Created';
        echo PHP_EOL;
    }
 
    if ($withData) {
		$mapper = new Xfrom26_Model_WordMapper();
		$data = array();
		$imported = 0;
		if ($files) {
			foreach (explode(",", $files) as $file) {
				$data = array_merge($data, csv_to_array(realpath($file)));
			}
		}
		foreach ($data as $row) {
			$row['word'] = trim(str_replace( "?", "", $row['word']));
			$row['unit'] = (int)str_replace(array(" ", "intro", "unit"), "", strtolower($row['unit']));
			$row['isDouble'] = 0;
			$row['pos'] = trim(array_shift(explode(",", str_replace( "?", "", $row['pos']))));
			$row['pos'] = trim(array_shift(explode("/", str_replace( "?", "", $row['pos']))));
            if ((strpos($row['word'], '(') === false) && (strpos($row['word'], '…') === false) && sizeof(explode(" ", $row['word'])) < 2) {
                $exists = $mapper->findUnique(new Xfrom26_Model_Word(), $row['word'], $row['pos'], $row['profile']);
                if (!$exists->getId()) {
                    $mapper->save(new Xfrom26_Model_Word($row));
                    $imported += 1;
                }
            }
		}
        if ('testing' != APPLICATION_ENV) {
            echo "Data Loaded. Imported $imported from " . count($data) . " lines.";
            echo PHP_EOL;
        }
    }
 
} catch (Exception $e) {
    echo 'AN ERROR HAS OCCURED:' . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
	var_dump($row);
    return false;
}
 
// generally speaking, this script will be run from the command line
return true;

