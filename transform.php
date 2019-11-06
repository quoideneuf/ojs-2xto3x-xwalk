#!/usr/bin/env php
<?php
use PHPUnit\Framework\ExpectationFailedException as ExpectationFailedException;

class TestShouldFailException extends Exception { }

$opts = getopt('', ['xml:', 'out:', 'xsl:', 'test']);

if (key_exists('test', $opts)) {
	require __DIR__ . '/tests/XmlOutputTest.php';
}

if (!key_exists('xml', $opts) or !key_exists('out', $opts)) {
	usageAndExit();
}

# Transform if asked to
if (key_exists('xsl', $opts)) {
	$xsldoc = new DOMDocument();
	$xsldoc->load($opts['xsl']);

	$xmldoc = new DOMDocument();
	$xmldoc->load($opts['xml']);

	$xsl = new XSLTProcessor();
	$xsl->importStyleSheet($xsldoc);
	file_put_contents($opts['out'], $xsl->transformToXML($xmldoc));
}

# Test transformed XML...
if (key_exists('xsl', $opts) and key_exists('test', $opts)) {
	$test_should_pass = new XmlOutputTest($opts['out']);
	$test_should_pass->testXml();
}

# ..or sanity test the tests
if (!key_exists('xsl', $opts) and key_exists('test', $opts)) {
	try {
		$test_should_fail = new XmlOutputTest($opts['xml']);
		$test_should_fail->testXml();
		throw new TestShouldFailException();
	} catch (ExpectationFailedException $e) {
		echo "Caught ExpectationFailedException\n";
	}
	$test_should_pass = new XmlOutputTest($opts['out']);
	$test_should_pass->testXml();
	echo "ok";
	exit();
}


function usageAndExit() {
	echo "USAGE: transform.php --xml /path/to/input.xml --out /path/to/output.xml [ --xsl transformer.xsl ] [ --test ]";
	exit(0);
}

?>
