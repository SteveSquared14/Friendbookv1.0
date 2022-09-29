<?php
require_once('Models/UserSession.php');
require_once('Models/Pagination.php');
require_once ('Models/UserDataSet.php');
$session = new UserSession();
$view = new stdClass();
$pagination = new Pagination();
$view = new stdClass();
$userDataSet = new UserDataSet();

//Get all users from the database in a paginated fashion
$view->allPaginatedUsers = $userDataSet->retrieveAllUsers($pagination->getPageFirstResults(), $pagination->getNoOfRecordsPerPage());

//used for displaying a welcome message to the user in the view
$fullNameArray = $userDataSet->retrieveOneUser($_SESSION['uid']);

$view->allUsers = (int)$pagination->getTotalNumberOfRecords();
$noOfRecordsPerPage = $pagination->getNoOfRecordsPerPage();
$view->numberOfPages = $pagination->getTotalPages();
$view->pageFirstResults = $pagination->getPageFirstResults();
$view->pageNumber = $pagination->getPage();

//if log out button pressed, log them out using the function below
if(isset($_POST['logOut'])){
    $session->logout();
    require_once('index.php');
}
else {
    //if not pressed, then require the view below
    require_once('Views/landingPageGeneral.phtml');
}
