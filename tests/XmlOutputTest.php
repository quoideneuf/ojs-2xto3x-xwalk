<?php

require __DIR__ . '/../vendor/autoload.php';

class XmlOutputTest extends PHPUnit\Framework\TestCase
{
	function __construct($xmlFile) {
		$this->xmlFile = $xmlFile;
		$this->xml = simplexml_load_file($this->xmlFile);
		$this->xml->registerXPathNamespace("pkp", "http://pkp.sfu.ca");
        parent::__construct();
    }

	function assertXPathEquals($value, $xpath) {
		$_value = implode($this->xml->xpath($xpath));
		echo "Checking XPath: " . $xpath . "\n";
		$this->assertEquals(
			$value,
			$_value
		);
	}

	public function testXml() {
		$this->assertXPathEquals(
			"A Review of Information Systems and Corporate Memory: design for staff turn-over",
			"//pkp:title[@locale='en_US']"
		);

		$this->assertXPathEquals(
			"https://creativecommons.org/licenses/by-nc-nd/4.0",
			"//pkp:licenseUrl"
		);

		$this->assertXPathEquals(
			"Brian Vemer",
			"//pkp:copyrightHolder[@locale='en_US']"
		);

		$this->assertXPathEquals(
			"2018",
			"//pkp:copyrightYear"
		);

		$this->assertXPathEquals(
			"information technology",
			"//pkp:keywords[@locale='en_US']/pkp:keyword[1]"

		);

		$this->assertXPathEquals(
			"knowledge preservation",
			"//pkp:keywords[@locale='en_US']/pkp:keyword[2]",
		);

		// author/@include_in_browse author/@user_group_ref <-- can't populate these
		// attributes with 2.x export
		// givenname/@locale <-- can this be inferred from ancestor <article> tag?
		$this->assertXPathEquals(
			"Brian",
			"//pkp:authors/pkp:author[@primary_contact='true']/pkp:givenname"
		);

		$this->assertXPathEquals(
			"Vemer",
			"//pkp:authors/pkp:author[@primary_contact='true']/pkp:familyname"
		);

		$this->assertXPathEquals(
			"University of Oslo",
			"//pkp:authors/pkp:author[@primary_contact='true']/pkp:affiliation"
		);

		$this->assertXPathEquals(
			"NO",
			"//pkp:authors/pkp:author[@primary_contact='true']/pkp:country"
		);

		$this->assertXPathEquals(
			"bvemer@mailinator.com",
			"//pkp:authors/pkp:author[@primary_contact='true']/pkp:email"
		);

	}
}
