<?php
require_once('Models/UserDataSet.php');
require_once('Models/UserSession.php');
require_once('Models/SearchPagination.php');
$session = new UserSession();
$view = new stdClass();
$searchResults = new UserDataSet();
if(isset($_POST['userSearchTerm'])){
    if($_POST['searchCriteria'] == "Search By:" || $_POST['searchFilter'] == "Sort By:"){
        if($_POST['searchCriteria'] == "Search By:"){
            $_POST['searchCriteria'] = "First Name";
        }
        if($_POST['searchFilter'] == "Sort By:"){
            $_POST['searchFilter'] = "UserID";
        }
    }
    $view->searchResults = $searchResults->searchDatabase($_POST['userSearchTerm'], $_POST['searchCriteria'], $_POST['searchFilter']);
}

$view->userSearchTerm = $_POST['userSearchTerm'];
$view->searchCriteria = $_POST['searchCriteria'];
$view->searchFilter = $_POST['searchFilter'];

//Use a default value as a parameter for this pagination object, as at this point in the controller
//all we want is access to the accessor methods below the declaration in order to generate
//the relevant LIMIT/OFFSET search parameter to be used later
$pagination = new SearchPagination(1250);

//Use the accessor methods to generate the LIMIT/OFFSET parameter to
//be used in the paginated DB search (below)
$firstPageResults = $pagination->getPageFirstResults();
$noOfRecordsPerPage = $pagination->getNoOfRecordsPerPage();
$paginationParam = $firstPageResults . ", " . $noOfRecordsPerPage;

if(isset($_POST['userSearchTerm'])){
    if($_POST['searchCriteria'] == "Search By:" || $_POST['searchFilter'] == "Sort By:"){
        if($_POST['searchCriteria'] == "Search By:"){
            $_POST['searchCriteria'] = "First Name";
        }
        if($_POST['searchFilter'] == "Sort By:"){
            $_POST['searchFilter'] = "UserID";
        }
    }
    $view->paginatedSearchResults = $searchResults->paginatedSearchDatabase($_POST['userSearchTerm'], $_POST['searchCriteria'], $_POST['searchFilter'], $paginationParam);
}
//Now make another SearchPagination object, passing it the dynamic size depending on how many results the previous search returned
$pagination2 = new SearchPagination(sizeof($view->searchResults));
$firstPageResults = $pagination2->getPageFirstResults();
$noOfRecordsPerPage = $pagination2->getNoOfRecordsPerPage();
$paginationParam = $firstPageResults . ", " . $noOfRecordsPerPage;
$view->numberOfPages = $pagination2->getTotalPages();
$view->pageNumber = $pagination2->getPage();
require_once('Views/search.phtml');