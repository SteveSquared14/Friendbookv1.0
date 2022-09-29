<?php
require_once ('Models/Database.php');

class Pagination{
    var $noOfRecordsPerPage;
    var $totalPages;
    var $pageFirstResults;
    var $page;
    var $totalNumberOfRecords;

    public function __construct()
    {
        //connect to the database
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();

        //define number of results per page
        $this->noOfRecordsPerPage = 50;

        //find the number of results in the database
        $totalNumberOfRecordsSQL = "SELECT COUNT(*) FROM Users";
        $statement = $this->_dbHandle->prepare($totalNumberOfRecordsSQL);
        $statement->execute();
        $totalNumberOfRecords = $statement->fetchColumn(0);
        $this->totalNumberOfRecords = $totalNumberOfRecords;

        //determine number of total pages available
        $this->totalPages = ceil($totalNumberOfRecords / $this->noOfRecordsPerPage);

        //determine which page number visitor is currently on
        if (!isset($_GET['page'])) {
            $this->page = 1;
        } else {
            $this->page = $_GET['page'];
        }
        $this->pageFirstResults = ($this->page - 1) * $this->noOfRecordsPerPage;
    }

    //return the number of records per page
    public function getNoOfRecordsPerPage(){
        return $this->noOfRecordsPerPage;
    }

    //returns the number of pages
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

    //return the total number of records in the DB
    public function getTotalNumberOfRecords(){
        return $this->totalNumberOfRecords;
    }
}
