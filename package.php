<?php

/**
 * This is the package.xml generator for Braintree Payments API
 *
 * PHP version 5
 *
 * LICENSE:
 *
 * Copyright 2015 silverorange
 *
 * Copyright (c) 2014 Braintree, a division of PayPal, Inc.
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category  Services
 * @package   Braintree
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2015 silverorange
 * @license   https://github.com/braintree/braintree_php/blob/master/LICENSE MIT
 */

require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$api_version     = '3.5.0';
$api_state       = 'stable';

$release_version = '3.5.0';
$release_state   = 'alpha';
$release_notes   = 'Mirror official release.';

$description =
	"The Braintree PHP library provides integration access to the " .
	"Braintree Gateway.\n" .
	"\n".
	"This package contains the latest version of the SDK modified " .
	"slightly for installation as a PEAR package. Modifications are:" .
	" - reduce required PHP version - the tests require PHP 5.4 but " .
	"   the SDK itself runs fine on PHP 5.3.\n" .
	" - adjust the installation directories to match PSR-0.\n";

$package = new PEAR_PackageFileManager2();

$package->setOptions(
	array(
		'filelistgenerator'       => 'file',
		'simpleoutput'            => true,
		'baseinstalldir'          => '/',
		'packagedirectory'        => './',
		'dir_roles'               => array(
			'lib'                 => 'php',
			'tests'               => 'test',
		),
		'exceptions'              => array(
			'README.md'           => 'doc',
			'CHANGELOG.md'        => 'doc',
			'LICENSE'             => 'doc',
			'phpunit.xml.dist'    => 'test',
		),
		'ignore'                  => array(
			'package.php',
			'ci.sh',
			'Rakefile',
			'composer.json',
			'*.tgz',
		),
	)
);

$package->setPackage('Braintree');
$package->setSummary(
	'The Braintree PHP library provides integration access to the '.
	'Braintree Gateway.'
);
$package->setDescription($description);
$package->setChannel('pear.silverorange.com');
$package->setPackageType('php');
$package->setLicense(
	'MIT License',
 	'https://github.com/braintree/braintree_php/blob/master/LICENSE'
);

$package->setNotes($release_notes);
$package->setReleaseVersion($release_version);
$package->setReleaseStability($release_state);
$package->setAPIVersion($api_version);
$package->setAPIStability($api_state);

$package->addMaintainer(
	'lead',
	'gauthierm',
	'Mike Gauthier',
	'mike@silverorange.com'
);

$package->setPhpDep('5.3.0');
$package->addExtensionDep('required', 'curl');
$package->addExtensionDep('required', 'dom');
$package->addExtensionDep('required', 'hash');
$package->addExtensionDep('required', 'openssl');
$package->addExtensionDep('required', 'xmlwriter');

$package->setPearInstallerDep('1.4.0');
$package->generateContents();
$package->addRelease();

function rewriteInstallPath($package, $current_dir) {
	$dir = dir(__DIR__ . '/' . $current_dir);
	while (false != ($entry = $dir->read())) {
		if ($entry === '.' || $entry === '..') {
			continue;
		}

		if (is_dir(__DIR__ . '/' . $current_dir . '/' . $entry)) {
			rewriteInstallPath($package, $current_dir . '/' . $entry);
		} else {
			$from = $current_dir . '/' . $entry;

			// strip leading 'lib' off all installed files
			$rewrite_path = explode('/', $current_dir);
			array_shift($rewrite_path);
			$rewrite_path[] = $entry;

			// install SSL cert in the Braintree dir
			if ($rewrite_path[0] === 'ssl') {
				array_unshift($rewrite_path, 'Braintree');
			}

			$to = implode('/', $rewrite_path);
			$package->addInstallAs($from, $to);
		}
	}
}

rewriteInstallPath($package, 'lib');

if (   isset($_GET['make'])
	|| (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')
) {
	$package->writePackageFile();
} else {
	$package->debugPackageFile();
}

?>
