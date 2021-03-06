<?php

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__).'/../../phpunit-support.php';

class ICS extends PHPUnitSupport{

	public function testCalendar(){

		$resCalendar = \Twist::ICS()->createCalendar();

		$this->assertTrue(is_resource($resCalendar) || is_object($resCalendar));
	}

	public function testEvent(){

		$resEvent = \Twist::ICS()->createEvent();

		$this->assertTrue(is_resource($resEvent) || is_object($resEvent));
	}

	public function testLoad(){

		$resCalendarICS = \Twist::ICS()->loadFile(TWIST_PUBLIC_ROOT.'/app/calendar.ics');

		$this->assertTrue(is_resource($resCalendarICS) || is_object($resCalendarICS));
		$this->_assertStringContainsString('BEGIN:VCALENDAR',$resCalendarICS->getRaw());

		$resEventICS = \Twist::ICS()->loadFile(TWIST_PUBLIC_ROOT.'/app/event.ics');

		$this->assertTrue(is_resource($resEventICS) || is_object($resEventICS));
		$this->_assertStringContainsString('BEGIN:VEVENT',$resEventICS->getRaw());
	}
}