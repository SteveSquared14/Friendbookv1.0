<?php
require_once('Models/FriendRequest.php');
require_once('Models/UserSession.php');
require_once('Models/UserDataSet.php');
$view = new stdClass();
$session = new UserSession();
$userDataSet = new UserDataSet();
$view->requestedFriends = $userDataSet->retrieveAllFriendRequests($_SESSION['uid']);
$fullNameArray = $userDataSet->retrieveOneUser($_SESSION['uid']);
//$view->loggedInUserID = $userDataSet->retrieveUserID($_SESSION['login']);

//check to see if user has pressed accept friend button
if (isset($_POST['blockUserBtn'])){
    $blockUser = new FriendRequest();
    $blockUser->blockUser($_POST['hiddenUserID']);
    require_once('landingPageUser.php');
}
//check to see if user has pressed add friend button
else if(isset($_POST['addFriend'])){
    $newFriendship = new FriendRequest();
    $newFriendship->storeFriendRequest($_POST['hiddenUserID']);
    require_once('landingPageUser.php');
}
//check to see if user has pressed reject friend button
else if(isset($_POST['rejectFriendBtn'])){
    $rejectFriendship = new FriendRequest();
    $rejectFriendship->rejectFriendRequest($_POST['hiddenFriendshipID']);
    require_once('landingPageUser.php');
}
//check to see if user has pressed block user button
elseif(isset($_POST['acceptFriendBtn'])){
$acceptFriendship = new FriendRequest();
$acceptFriendship->acceptFriendRequest($_POST['hiddenFriendshipID']);
require_once('landingPageUser.php');
}
//else no buttons pressed, so just load the view normally and don't do anything extra
else {
    require_once('Views/friendRequests.phtml');
}