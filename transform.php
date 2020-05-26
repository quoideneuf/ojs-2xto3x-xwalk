#!/usr/bin/env php
<?php
use PHPUnit\Framework\ExpectationFailedException as ExpectationFailedException;
class TestShouldFailException extends Exception { }

$opts = getopt('', ['xml:', 'out:', 'xsl:', 'secref:', 'article-defaults:', 'test']);

if (key_exists('test', $opts)) {
	require __DIR__ . '/tests/XmlOutputTest.php';
}

if (!key_exists('xml', $opts)) {
	usageAndExit();
}

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

$xsldoc = new DOMDocument();
$xsldoc->load($opts['xsl']);

$xsl = new XSLTProcessor();
$xsl->importStyleSheet($xsldoc);

if (is_dir($opts['xml']) && is_dir($opts['out'])) {
	$dh = opendir($opts['xml']);
	while ($file = readdir($dh)) {
		echo $file . "\n";
		if ($file === '.') continue;
		if ($file === '..') continue;
		transform($opts['xml'] . '/' . $file, $opts['out'] . '/' . $file);
	}
	closedir($dh);
} else {
	transform($opts['xml'], $opts['out']);
}

function transform($source, $target = null) {
	global $opts;
	global $xsl;
	$xmldoc = new DOMDocument();
	$xmldoc->load($source);
	if (key_exists('article-defaults', $opts)) {
		$defaults = file_get_contents($opts['article-defaults']);
		foreach (explode("\n", $defaults) as $default) {
			$default = explode("=", trim($default));
			if (sizeof($default) == 2) {
				$xsl->setParameter('', array_shift($default), array_shift($default));
			}
		}
	}

	if (key_exists('secref', $opts)) {
		$xsl->setParameter('', 'section_ref', $opts['secref']);
	}

	if ($target) {
		file_put_contents($target, $xsl->transformToXML($xmldoc));
	} else {
		echo $xsl->transformToXML($xmldoc);
	}
}

# Test transformed XML...
if (key_exists('xsl', $opts) and key_exists('test', $opts)) {
	$test_should_pass = new XmlOutputTest($opts['out']);
	$test_should_pass->testXml();
}



function usageAndExit() {
	echo "USAGE: transform.php --xml /path/to/input.xml --out /path/to/output.xml [ --xsl transformer.xsl ] [ --test ]";
	exit(0);
}

?>
