<?php
require_once('Models/UserDataSet.php');
require_once('Models/UserSession.php');
if(isset($_SESSION['login'])){
    //If session is already set, do nothing and contine with rest of controller
}
else{
    //If session isn't set, then start a new session
    $session = new UserSession();
}
$view = new stdClass();
$userDataSet = new UserDataSet();
$view->profileResults = $userDataSet->loadUserProfile($_SESSION['uid']);
$fullNameArray = $userDataSet->retrieveOneUser($_SESSION['uid']);
$currentUserObject = new UserData($fullNameArray);
$currentPasswordInDB = $currentUserObject->getPassword();

$updateDetailsResponse = "";
if(isset($_POST['submitNewDetails'])){
    if(md5($_POST['currentPassword']) === $currentPasswordInDB){
        $newPassword = md5($_POST['newPassword']);
        $userDataSet->changeUserDetails($_SESSION['uid'], $_POST['newUserName'], $_POST['newFirstName'], $_POST['newLastName'], $_POST['newEmail'], $newPassword);
        $updateDetailsResponse = 'Details Updated!';
        require_once('Views/profilePage.phtml');
    }
    else{
        $updateDetailsResponse = 'Please enter the correct current password to change details!';
        require_once('Views/profilePage.phml');
    }
}
require_once('Views/profilePage.phtml');