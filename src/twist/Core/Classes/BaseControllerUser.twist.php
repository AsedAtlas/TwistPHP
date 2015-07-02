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
 * @link       https://twistphp.com
 *
 */

namespace Twist\Core\Classes;
use \Twist\Core\Models\User\Auth;

class BaseControllerUser extends BaseController{

    protected $resUser = null;
    protected $strEntryPageURI = null;

    public function __construct(){

        $this->resUser = \Twist::User();

        $this -> _aliasURI( 'change-password', 'changePassword' );
        $this -> _aliasURI( 'forgotten-password', 'forgottenPassword' );
        $this -> _aliasURI( 'verify-account', 'verifyAccount' );
        $this -> _aliasURI( 'device-manager', 'deviceManager' );
    }

    public function _entryPage($strEntryPageURI = null){
        $this->strEntryPageURI = $strEntryPageURI;
    }

    public function login(){

        if(array_key_exists('logout',$_GET)){
            $this->logout();
        }

        return $this->resUser->viewExtension('login_form');
    }

    public function authenticate(){

        $arrResult = Auth::login($_POST['email'],$_POST['password'],(array_key_exists('remember',$_POST) && $_POST['remember'] == '1'));

        if($arrResult['issue'] != ''){

            \Twist::Session()->data('site-login_error_message',$arrResult['message']);

            //A login issue has occurred, redirect to the relevant page
            switch($arrResult['issue']){

                case 'temporary':
                    \Twist::redirect('./change-password');
                    break;

                case 'verify':
                    \Twist::redirect('./verify-account');
                    break;

                case 'disabled':
                case 'password':
                case 'email':
                    \Twist::redirect('./login');
                    break;
            }

        }elseif($arrResult['status']){
            //Login complete
            \Twist::redirect(is_null($this->strEntryPageURI) ? './' : './'.$this->strEntryPageURI);
        }else{
            //You are not logged in
            \Twist::redirect('./login');
        }
    }

    public function logout(){
        Auth::logout();
        \Twist::redirect('./login');
    }

    public function forgottenPassword(){
        return $this->resUser->viewExtension('forgotten_password_form');
    }

    public function postForgottenPassword(){

        //Process the forgotten password request
        if(array_key_exists('forgotten_email',$_POST) && $_POST['forgotten_email'] != ''){
            $arrUserData = $this->resUser->getByEmail($_POST['forgotten_email']);

            //Now if the email exists send out the reset password email.
            if(is_array($arrUserData) && count($arrUserData) > 0){

                $resUser = $this->resUser->get($arrUserData['id']);
                $resUser->resetPassword(true);
                $resUser->commit();

                \Twist::Session()->data('site-login_message','A temporary password has been emailed to you');
                \Twist::redirect('./');
            }
        }
    }

    public function changePassword(){
        return $this->resUser->viewExtension('change_password_form');
    }

    public function postChangePassword(){

        if(array_key_exists('password',$_POST) && array_key_exists('confirm_password',$_POST)){

            if($this->resUser->loggedIn()){

                if($_POST['password'] === $_POST['confirm_password']){

                    if(\Twist::Session()->data('user-temp_password') === '0'){

                        if(array_key_exists('current_password',$_POST)){

                            $strNewPassword = $_POST['password'];

                            //Change the users password and re-log them in (Only for none-temp password users)
                            $this->resUser->changePassword(\Twist::Session()->data('user-id'),$strNewPassword,$_POST['current_password'],false);

                            //Remove the two posted password vars
                            unset($_POST['password']);
                            unset($_POST['current_password']);

                            Auth::login(\Twist::Session()->data('user-email'),$strNewPassword);
                            \Twist::redirect('./');
                        }
                    }else{

                        $strNewPassword = $_POST['password'];

                        //Change the users password and re-log them in
                        $this->resUser->updatePassword(\Twist::Session()->data('user-id'),$strNewPassword);

                        //Remove the posted password and reset the session var
                        unset($_POST['password']);
                        \Twist::Session()->data('user-temp_password','0');

                        Auth::login(\Twist::Session()->data('user-email'),$strNewPassword);
                        \Twist::redirect('./');
                    }

                }else{
                    \Twist::Session()->data('site-error_message','The passwords you entered do not match');
                    \Twist::redirect('./change-password');
                }
            }
        }
    }

    public function verifyAccount(){
        return $this->resUser->viewExtension('account_verification');
    }

    public function postVerifyAccount(){

        //Resend a new verification code
        if(array_key_exists('verification_email',$_POST) && $_POST['verification_email'] != ''){
            $arrUserData = $this->resUser->getByEmail($_POST['verification_email']);

            //Now if the email exists send out the reset password email.
            if(is_array($arrUserData) && count($arrUserData) > 0){

                $resUser = $this->resUser->get($arrUserData['id']);
                $resUser->requireVerification();
                $resUser->commit();
            }
        }

        if(array_key_exists('verify',$_GET) && array_key_exists('verify',$_GET) && $_GET['verify'] != ''){
            $this->resUser->verifyEmail($_GET['verify']);
        }
    }

    public function deviceManager(){
        return $this->resUser->viewExtension('devices_form');
    }

    public function postDeviceManager(){

        if(array_key_exists('save-device',$_GET) && array_key_exists('device-name',$_GET)){
            Auth::SessionHandler()->editDevice($this->resUser->currentID(),$_GET['save-device'],$_GET['device-name']);
        }

        if(array_key_exists('forget-device',$_GET)) {
            Auth::SessionHandler()->forgetDevice($this->resUser->currentID(), $_GET['forget-device']);
        }
    }

    public function register(){
        return $this->resUser->viewExtension('registration_form');
    }

    public function postRegister(){

        //Process the register user request
        if(array_key_exists('register',$_POST) && $_POST['register'] != ''){

            $resUser = $this->resUser->create();
            $resUser->email($_POST['email']);
            $resUser->firstname($_POST['firstname']);
            $resUser->surname($_POST['lastname']);
            $resUser->level(10);

            $blContinue = true;

            if(\Twist::framework()->setting('USER_REGISTER_PASSWORD')){

                if($_POST['password'] === $_POST['confirm_password']){
                    $arrResponse = $resUser->password($_POST['password']);

                    if(!$arrResponse['status']){
                        \Twist::Session()->data('site-register_error_message',$arrResponse['message']);
                        $blContinue = false;
                    }
                }else{
                    \Twist::Session()->data('site-register_error_message','Your password and confirm password do not match');
                    $blContinue = false;
                }
            }else{
                $resUser->resetPassword();
            }

            //If the password configuration has passed all checks then continue
            if($blContinue){
                $intUserID = $resUser->commit();

                if($intUserID > 0){

                    //AUTO_LOGIN
                    if(\Twist::framework()->setting('USER_REGISTER_PASSWORD') && !\Twist::framework()->setting('USER_EMAIL_VERIFICATION') && \Twist::framework()->setting('USER_AUTO_AUTHENTICATE')){

                        //@todo redirect - test or work out best way of doing this
                        //$this->resUser->afterLoginRedirect(); --- set the value that this function uses, authenticate will do the redirect

                        //Authenticate the user (log them in)
                        $this->resUser->authenticate($_POST['email'],$_POST['password']);

                        //@todo redirect - test or work out best way of doing this
                        $this->resUser->afterLoginRedirect();
                    }else{
                        \Twist::Session()->data('site-register_message','Thank you for your registration, your password has been emailed to you');
                        unset( $_POST['email'] );
                        unset( $_POST['firstname'] );
                        unset( $_POST['lastname'] );
                        unset( $_POST['register'] );
                    }
                }else{
                    \Twist::Session()->data('site-register_error_message','Failed to register user');
                }
            }
        }
    }
}