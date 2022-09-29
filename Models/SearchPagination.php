<?php
require_once ('Models/Database.php');
/*
 * Used a separate pagination model for the paginating the search results because variables need to be set dynamically
 * at run-time/search-time, instead of generating based on fixed values like the pagination model for displaying all users
 */
class SearchPagination{
    var $noOfRecordsPerPage;
    var $totalPages;
    var $pageFirstResults;
    var $page;
    var $totalNumberOfRecords;

    var $userSearchTerm;
    var $searchCriteria;
    var $searchFilter;

    public function __construct($newTotalNumberOfRecords)
    {
        //connect to the database
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();

        //define number of results per page
        $this->noOfRecordsPerPage = 15;

        //number of results in the database
        $this->totalNumberOfRecords = $newTotalNumberOfRecords;

        //determine number of total pages available
        $this->totalPages = ceil($this->totalNumberOfRecords / $this->noOfRecordsPerPage);

        //determine which page number visitor is currently on
        if (!isset($_GET['page'])) {
            $this->page = 1;
        } else {
            $this->page = $_GET['page'];
        }
        $this->pageFirstResults = ($this->page - 1) * $this->noOfRecordsPerPage;
    }

    //Return the number of records per page
    public function getNoOfRecordsPerPage(){
        return $this->noOfRecordsPerPage;
    }

    //return the total number of pages
    public function getTotalPages(){
        return $this->totalPages;
    }

    //returns the page of first results
    public function getPageFirstResults(){
        return $this->pageFirstResults;
    }

    //returns the page number of the current page
    public function getPage(){
        return $this->page;
    }

    //Returns the term the user searched
    public function getUserSearchTerm(){
        return $this->userSearchTerm;
    }

    //returns the criteria the user selected to search by
    public function getSearchCriteria(){
        return $this->searchCriteria;
    }

    //returns the filter the user selected to order the results by
    public function getSearchFilter(){
        return $this->searchFilter;
    }
}