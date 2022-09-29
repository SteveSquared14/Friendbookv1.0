<?php
require_once('Models/UserSession.php');
require_once('Models/UserDataSet.php');
if(isset($_SESSION['login'])){
    //If session is already set, do nothing and contine with rest of controller
}
else{
    //If session isn't set, then start a new session
    $session = new UserSession();
}
$view = new stdClass();
$userDataSet = new UserDataSet();

$view->loggedInUserID = $_SESSION['uid'];
$view->friendships = $userDataSet->retrieveAllFriendships($_SESSION['uid']);
$fullNameArray = $userDataSet->retrieveOneUser($_SESSION['uid']);

//If the log out button is pressed, call the method to destroy the session
//and then go back to the index page. A.k.a "logging the user out"
if(isset($_POST['logOut'])){
    $session->logout();
    require_once('index.php');
}
else {
    //Captcha not answered correctly, so send the user back to the log-in/register view
    require_once('Views/landingPageUser.phtml');
}