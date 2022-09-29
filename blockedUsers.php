<?php
require_once('Models/UserSession.php');
require_once('Models/UserDataSet.php');
if(isset($_SESSION['login'])){
    //If session is already set, do nothing and continue with rest of controller
}
else{
    //If session isn't set, then start a new session
    $session = new UserSession();
}
$view = new stdClass();

$userDataSet = new UserDataSet();
$view->blockedUsers = $userDataSet->retrieveAllBlockedUsers($_SESSION['uid']);
$fullNameArray = $userDataSet->retrieveOneUser($_SESSION['uid']);

//If the log-out button is pressed, call the method to destroy the session
//and then go back to the index page. A.k.a "logging the user out"
if(isset($_POST['logOut'])){
    $session->logout();
    require_once('index.php');
}
else {
    //If the logout button hasn't been pressed then the user must be logged in
    //so load their landing page
    require_once('Views/blockedUsers.phtml');
}