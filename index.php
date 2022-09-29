<?php
require_once('Models/UserSession.php');
require_once ('Models/UserDataSet.php');
$userDataSet = new UserDataSet();
$session = new UserSession();
$view = new stdClass();

//This bit is for Captcha and security when logging in
$view->number1 = rand(1,25);
$view->number2 = rand(1,25);

//if session exists, call the landingPage controller for the logged in user
if(isset($_SESSION['login'])){
    require_once('landingPageUser.php');
}
//if not then require the index.phtml (log-in/register page) again
else{
    require_once('Views/index.phtml');
}