<?php
	/**
	 * This file is part of TwistPHP.
	 *
	 * TwistPHP is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation, either version 3 of the License, or
	 * (at your option) any later version.
	 *
	 * TwistPHP is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with TwistPHP.  If not, see <http://www.gnu.org/licenses/>.
	 *
	 * @author     Shadow Technologies Ltd. <contact@shadow-technologies.co.uk>
	 * @license    https://www.gnu.org/licenses/gpl.html LGPL License
	 * @link       http://twistphp.com/
	 *
	 */

	namespace TwistPHP\Packages;
	use TwistPHP\ModuleBase;

	/**
	 * Easy session management allowing the use of a user and site array of data. All stored using the PHP session.
	 * Also extends the template package to allow the use of session vars in templates.
	 */
	class Session extends ModuleBase{

		protected $intSessionLife = 86400;
		protected $blStarted = false;
		protected $strHandler = 'native';
		protected $resHandler = null;

		public function __construct(){

			$this->strHandler = \Twist::framework() -> setting('SESSION_HANDLER');
			switch($this->strHandler){

				case'file';
					require_once sprintf('%s/libraries/Session/File.lib.php',DIR_FRAMEWORK_PACKAGES);
					$this->resHandler = new SessionFile();
					break;

				case'memcache';
					require_once sprintf('%s/libraries/Session/Memcache.lib.php',DIR_FRAMEWORK_PACKAGES);
					$this->resHandler = new SessionMemcache();
					break;

				case'mysql';
					require_once sprintf('%s/libraries/Session/Mysql.lib.php',DIR_FRAMEWORK_PACKAGES);
					$this->resHandler = new SessionMysql();
					break;

				case'native';
				default:
					//Do nothing, uses the default handler
					break;
			}

			$this->start();
		}

		/**
		 * Create the guest session for the user, this will then be used when the user becomes real
		 * @static
		 * @return void
		 */
		public function start(){

			if($this->blStarted == false){
				session_start();
				$_COOKIE['PHPSESSID'] = $this->getSessionID();
				$this->blStarted = true;
				//setcookie('PHPSESSID', $this->getSessionID(), (\Twist::DateTime()->time()+$this->intSessionLife), '/', $_SERVER["HTTP_HOST"], isset($_SERVER["HTTPS"]), true);
				//setcookie('PHPSESSID', $this->getSessionID(), (\Twist::DateTime()->time()+$this->intSessionLife), '/');
			}
		}

		/**
		 * Get the currently assigned session ID
		 * @return string
		 */
		public function getSessionID(){
			return session_id();
		}

		/**
		 * Set and get the twist session data, passing only a key will return the data stored against that key. Pass in a value as well will set and return the result. Null is returned upon error.
		 * @param $strKey
		 * @param null $mxdValue
		 * @return null
		 */
		public function data($strKey,$mxdValue = null){

			if(!is_null($mxdValue)){
				$_SESSION['twist-session'][$strKey] = $mxdValue;
			}

			return (array_key_exists($strKey,$_SESSION['twist-session'])) ? $_SESSION['twist-session'][$strKey] : null;
		}

		/**
		 * Remove a single session item or clear the whole session by leaving the key field null
		 * @param null $strKey
		 */
		public function remove($strKey = null){

			if(!is_null($strKey) && array_key_exists($strKey,$_SESSION['twist-session'])){
				unset($_SESSION['twist-session'][$strKey]);
			}elseif(is_null($strKey)){
				$_SESSION['twist-session'] = array();
			}
		}

		public function templateExtension($strReference){

			$strData = '';

			if(strstr($strReference,'/')){
				$mxdTempData = $this->framework()->tools()->arrayParse($strReference,$_SESSION['twist-session']);
				$strData = (is_array($mxdTempData)) ? print_r($mxdTempData,true) : $mxdTempData;
			}elseif(array_key_exists($strReference,$_SESSION['twist-session'])){
				$strData = (is_array($_SESSION['twist-session'][$strReference])) ? print_r($_SESSION['twist-session'][$strReference],true) : $_SESSION['twist-session'][$strReference];
			}

			return $strData;
		}
	}