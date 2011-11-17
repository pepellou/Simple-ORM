<?php

require_once dirname(__FILE__).'/../config.php';

class StringUtilsTest extends PHPUnit_Framework_TestCase {

	protected function setUp(
	) {
	}

	public function startsWithProvider(
	) {
		return array(
			array("",    "",    true),
			array("asd", "",    true),
			array("asd", "a",   true),
			array("as",  "a",   true),
			array("a",   "a",   true),
			array("asd", "as",  true),
			array("asd", "asd", true),
			array("",    "d",   false),
			array("ssd", "a",   false),
			array("as",  "s",   false),
			array("f",   "a",   false),
			array("fsd", "fv",  false),
			array("asd", "asf", false)
		);
	}

	/**
	 * @dataProvider startsWithProvider
	 */
	public function test_startsWith(
		$what,
		$with,
		$expected
	) {
		$this->assertEquals($expected, StringUtils::startsWith($what, $with));
	}
	
	public function endsWithProvider(
	) {
		return array(
			array("",    "",    true),
			array("dsa", "",    true),
			array("dsa", "a",   true),
			array("sa",  "a",   true),
			array("a",   "a",   true),
			array("dsa", "sa",  true),
			array("dsa", "dsa", true),
			array("",    "d",   false),
			array("dss", "a",   false),
			array("sa",  "s",   false),
			array("f",   "a",   false),
			array("dsf", "vf",  false),
			array("dsa", "fsa", false)
		);
	}

	/**
	 * @dataProvider endsWithProvider
	 */
	public function test_endsWith(
		$what,
		$with,
		$expected
	) {
		$this->assertEquals($expected, StringUtils::endsWith($what, $with));
	}
	
}

?>
