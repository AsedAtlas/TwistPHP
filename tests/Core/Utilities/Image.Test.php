<?php

	class Image extends \PHPUnit_Framework_TestCase{

		public static $resImage = null;
		public static $strImageCode = '';

		public function testCreate(){

			self::$resImage = \Twist::Image()->create(16,16, '#000000');
			$strNewImageCode = self::$resImage->outputBase64();

			$this->assertTrue(!empty($strNewImageCode));
			$this->assertContains('base64', $strNewImageCode);
			$this->assertNotEquals($strNewImageCode, self::$strImageCode);

			self::$strImageCode = $strNewImageCode;
		}

		public function testDetectOrientation(){

			$strOrientation = self::$resImage->detectOrientation();
			$this->assertEquals($strOrientation, 'square');

			self::$strImageCode = self::$resImage->outputBase64();
		}

		public function testAspectRatio(){

			$intAspectRatio = self::$resImage->aspectRatio();
			$this->assertEquals($intAspectRatio, 1);

			self::$strImageCode = self::$resImage->outputBase64();
		}

		public function testNormalizeColor(){

			$arrColour = self::$resImage->normalizeColor('#FF0000');
			$this->assertTrue(is_array($arrColour));

			//set the alpha too high
			$arrColour['a'] = 129;
			$arrColour = self::$resImage->normalizeColor($arrColour);

			//Check is has been lowered
			$this->assertEquals($arrColour['a'], 127);

			self::$strImageCode = self::$resImage->outputBase64();
		}

		public function testFill(){

			self::$resImage->fill('#FF0000');
			$strNewImageCode = self::$resImage->outputBase64();

			$this->assertTrue(!empty($strNewImageCode));
			$this->assertContains('base64', $strNewImageCode);
			$this->assertNotEquals($strNewImageCode, self::$strImageCode);

			self::$strImageCode = $strNewImageCode;
		}

		public function testLine(){

			self::$resImage->line(0,0,16,16);
			$strNewImageCode = self::$resImage->outputBase64();

			$this->assertTrue(!empty($strNewImageCode));
			$this->assertContains('base64', $strNewImageCode);
			$this->assertNotEquals($strNewImageCode, self::$strImageCode);

			self::$strImageCode = $strNewImageCode;
		}

		public function testRectangle(){

			self::$resImage->rectangle(4,4,12,8);
			$strNewImageCode = self::$resImage->outputBase64();

			$this->assertTrue(!empty($strNewImageCode));
			$this->assertContains('base64', $strNewImageCode);
			$this->assertNotEquals($strNewImageCode, self::$strImageCode);

			self::$strImageCode = $strNewImageCode;
		}

		public function testString(){

			self::$resImage->string(5,5,'T');
			$strNewImageCode = self::$resImage->outputBase64();

			$this->assertTrue(!empty($strNewImageCode));
			$this->assertContains('base64', $strNewImageCode);
			$this->assertNotEquals($strNewImageCode, self::$strImageCode);

			self::$strImageCode = $strNewImageCode;
		}

		public function testFlip(){

			self::$resImage->flip('vertical');
			$strNewImageCode = self::$resImage->outputBase64();

			$this->assertTrue(!empty($strNewImageCode));
			$this->assertContains('base64', $strNewImageCode);
			$this->assertNotEquals($strNewImageCode, self::$strImageCode);

			self::$strImageCode = $strNewImageCode;
		}

		public function testOpacity(){

			self::$resImage->opacity(0.5);
			$strNewImageCode = self::$resImage->outputBase64();

			$this->assertTrue(!empty($strNewImageCode));
			$this->assertContains('base64', $strNewImageCode);
			$this->assertNotEquals($strNewImageCode, self::$strImageCode);

			self::$strImageCode = $strNewImageCode;
		}

		public function testRotate(){

			self::$resImage->rotate(90);
			$strNewImageCode = self::$resImage->outputBase64();

			$this->assertTrue(!empty($strNewImageCode));
			$this->assertContains('base64', $strNewImageCode);
			$this->assertNotEquals($strNewImageCode, self::$strImageCode);

			self::$strImageCode = $strNewImageCode;
		}

		public function testCrop(){

			self::$resImage->crop(1,1,15,15);
			$strNewImageCode = self::$resImage->outputBase64();

			$this->assertTrue(!empty($strNewImageCode));
			$this->assertContains('base64', $strNewImageCode);
			$this->assertNotEquals($strNewImageCode, self::$strImageCode);

			$arrInfo = self::$resImage->currentInfo();
			$this->assertEquals($arrInfo['width'], 14);
			$this->assertEquals($arrInfo['height'], 14);

			self::$strImageCode = $strNewImageCode;
		}

	}