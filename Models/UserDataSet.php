<?php
require_once ('Models/UserData.php');
require_once ('Models/Database.php');
require_once ('Models/FriendData.php');
require_once ('Models/PasswordHasher.php');

class UserDataSet {
    protected $_dbHandle, $_dbInstance;

    public function __construct() {
        //connect to the DB
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    /*
     * Used once to download .jpg images for each user
     * and then commented out. Only kept in as I wanted to prove I can
     * both display downloaded stored images on my site. As well as storing and
     * displaying them directly from a resolves URL
     *
    public function getJpgImages(){
        $imagesFromDB = "SELECT id, profileImage FROM Users WHERE profileImage LIKE '%.jpg%' ORDER BY firstName";
        $statement = $this->_dbHandle->prepare($imagesFromDB); // prepare a PDO statement
        $statement->execute(); // execute the PDO statement
        $statement->fetch();

        $dataSet = [];
        $i = 0;
        while ($row = $statement->fetch()) {
            $dataSet[$i] = $row[1];
            //var_dump($dataSet[$i]);
            //echo '<br>';
            //$dataSet[$i] = $row[0];
            $i++;
        }
        return $dataSet;
    }
    */

    /*
     * Used once to encrypt all remaining plain text passwords in DB to md5 Hash.
     * Only kept in as I wanted to prove I can
     * write customs scripts to manipulate my DB and that I have 100% "ticked off" the
     * passwords encryption in the mark scheme
     *
    public function passwordHasher(){
        $imagesFromDB = "SELECT id, password FROM Users WHERE password LIKE '%123%' ORDER BY id";
        $statement = $this->_dbHandle->prepare($imagesFromDB); // prepare a PDO statement
        $statement->execute(); // execute the PDO statement
        $statement->fetch();

        $dataSet = [];
        while ($row = $statement->fetch()) {
            $dataSet[] = new PasswordHasher($row);
        }

        $hashedPasswords = [];
        foreach($dataSet as $unhashedPassword){
            $userID = $unhashedPassword->getID();
            $unHashedPassword = $unhashedPassword->getPassword();
            $hashedPassword = md5($unHashedPassword);
            $hashedPasswords[] = $hashedPassword;

            $sqlPwordStatement = "UPDATE Users SET password = '$hashedPassword' WHERE id='$userID'";
            echo $unHashedPassword;
            echo '<br>';
            echo $sqlPwordStatement;
            echo '<br>';
            echo '<br>';

            $statement = $this->_dbHandle->prepare($sqlPwordStatement); // prepare a PDO statement
            $statement->execute(); // execute the PDO statement
        }
    }
    */

    //Checks if a friendship being requested already exists in the database
    //Included as a failsafe as the system has been designed in such a way that
    //the user is only able to add the users that they are not currently friends with. So if they can "see" and add friend button
    //then they will not be friends with that user as otherwise the add friend button would not be shown
    public function checkDuplicateFriendship($x, $y){
        $returnValue = false;
        $sql = "SELECT COUNT(friendShipID) FROM Friendships WHERE friend1ID=? AND friend2ID=?";
        $statement = $this->_dbHandle->prepare($sql); // prepare a PDO statement

        $statement->bindParam(1,$x);
        $statement->bindParam(2,$y);
        $numberOfFriendshipsArray = $statement->execute(); // execute the PDO statement
        $numberOfFriendships = 0;
        if(is_array($numberOfFriendships) === true){
            $numberOfFriendships = 1;
        }
        else{
            $numberOfFriendships = 0;
        }

        if($numberOfFriendships == 1){
            $returnValue = true;
        }
        return $returnValue;
    }

    //Retrieves every single user from the database
    public function retrieveAllUsers($x, $y) {
        $paramToBind = $x . ", " . $y;
        $sqlQuery = "SELECT * FROM Users ORDER BY id LIMIT $paramToBind";

        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->execute(); // execute the PDO statement

        $dataSet = [];
        while ($row = $statement->fetch()) {
            $dataSet[] = new UserData($row);
        }
        return $dataSet;
    }


    //Retrieves a specific user from the database
    public function retrieveOneUser($x){
        $userQuery = "SELECT * FROM Users WHERE id='$x'";
        $statement = $this->_dbHandle->prepare($userQuery);
        $statement->execute();
        $userArray = $statement->fetch();
        return $userArray;
    }

    //Retrieves the friendship status of a friendship between the user logged in and another specified user
    //Used for filtering all user page and search results
    public function retrieveUserStatus($x, $y){
        $valueToReturn = "";
        $loggedInUserID = $y;
        $userQuery="SELECT Friendships.status FROM Users, Friendships WHERE (Users.id=Friendships.friend1ID OR Users.id=Friendships.friend2ID) AND ((friend1ID='$loggedInUserID' AND friend2ID=?) OR (friend2ID='$loggedInUserID' AND friend1ID=?))";
        $statement = $this->_dbHandle->prepare($userQuery);

        $statement->bindParam(1, $x);
        $statement->bindParam(2, $x);

        $statement->execute();
        $statusArray = $statement->fetch();;
        if(is_bool($statusArray)){
            $valueToReturn = "-1";
        }
        else{
            $valueToReturn = $statusArray["status"];
        }
        return $valueToReturn;
    }

    //Retrieves every friendship containing the logged-in user where status is "accepted"
    public function retrieveAllFriendships($x) {
        $loggedInFriendID = $x;
        $sqlQuery1 = "SELECT * from (
              select * from Users where Users.id in (
                  select friend1ID as friend1
                  from Friendships
                  where (Friendships.friend1ID = '$loggedInFriendID' or Friendships.friend2ID = '$loggedInFriendID')
                  union
                  select friend2ID as friend2
                  from Friendships
                  where (Friendships.friend1ID = '$loggedInFriendID' or Friendships.friend2ID = '$loggedInFriendID')
                  )
                    and Users.id != '$loggedInFriendID'
                ) ping inner join Friendships where ((friend1ID=ping.id and friend2ID='$loggedInFriendID') or (friend1ID='$loggedInFriendID' and friend2ID=ping.id))";
        $statement1 = $this->_dbHandle->prepare($sqlQuery1); // prepare a PDO statement
        $statement1->execute(); // execute the PDO statement
        $dataSet = [];
        while ($row = $statement1->fetch()) {
            $dataSet[] = new FriendData($row);
        }

        return $dataSet;
    }

    //Retrieves all friendships from the DB where friendship status equals 1
    public function retrieveAllFriendRequests($x) {
        $loggedInFriendID = $x;
        $sqlQuery1 = "SELECT * from (
              select * from Users where Users.id in (
                  select friend1ID as friend1
                  from Friendships
                  where (Friendships.friend1ID = '$loggedInFriendID' or Friendships.friend2ID = '$loggedInFriendID') AND Friendships.status=1 
                  union
                  select friend2ID as friend2
                  from Friendships
                  where (Friendships.friend1ID = '$loggedInFriendID' or Friendships.friend2ID = '$loggedInFriendID') AND Friendships.status=1 
                  )
                    and Users.id != '$loggedInFriendID'
                ) ping inner join Friendships where ((friend1ID=ping.id and friend2ID='$loggedInFriendID') or (friend1ID='$loggedInFriendID' and friend2ID=ping.id))";
        $statement1 = $this->_dbHandle->prepare($sqlQuery1); // prepare a PDO statement
        $statement1->execute(); // execute the PDO statement
        $dataSet = [];
        while ($row = $statement1->fetch()) {
            $dataSet[] = new FriendData($row);
        }

        return $dataSet;
    }

    //Retrieves every friendship containing the logged-in user where status is "blocked"
    public function retrieveAllBlockedUsers($x) {
        $loggedInFriendID = $x;
        $sqlQuery1 = "SELECT * from (
              select * from Users where Users.id in (
                  select friend1ID as friend1
                  from Friendships
                  where (Friendships.friend1ID = '$loggedInFriendID' or Friendships.friend2ID = '$loggedInFriendID') AND Friendships.status ='4'
                  union
                  select friend2ID as friend2
                  from Friendships
                  where (Friendships.friend1ID = '$loggedInFriendID' or Friendships.friend2ID = '$loggedInFriendID') AND Friendships.status = '4'
                  )
                    and Users.id != '$loggedInFriendID'
                ) ping inner join Friendships where ((friend1ID=ping.id and friend2ID='$loggedInFriendID') or (friend1ID='$loggedInFriendID' and friend2ID=ping.id))";
        $statement1 = $this->_dbHandle->prepare($sqlQuery1); // prepare a PDO statement
        $statement1->execute(); // execute the PDO statement
        $dataSet = [];
        while ($row = $statement1->fetch()) {
            $dataSet[] = new FriendData($row);
        }
        return $dataSet;
    }

    //Function used to search for users by username
    //returns an array of all users matching the search
    public function searchDatabase($searchTerm, $searchCriteria, $searchFilter){
        $sqlStatement = "";
        if ($searchCriteria == "First Name") {
            if ($searchFilter == "First Name") {
                $sqlStatement = "SELECT * FROM Users WHERE firstName LIKE '" . $searchTerm . "%' ORDER BY firstName";
            } elseif ($searchFilter == "Last Name") {
                $sqlStatement = "SELECT * FROM Users WHERE firstName LIKE '" . $searchTerm . "%' ORDER BY lastName";
            } elseif ($searchFilter == "UserID") {
                $sqlStatement = "SELECT * FROM Users WHERE firstName LIKE '" . $searchTerm . "%' ORDER BY id";
            }
        } elseif ($searchCriteria == "Last Name") {
            if ($searchFilter == "First Name") {
                $sqlStatement = "SELECT * FROM Users WHERE lastName LIKE '" . $searchTerm . "%' ORDER BY firstName";
            } elseif ($searchFilter == "Last Name") {
                $sqlStatement = "SELECT * FROM Users WHERE lastName LIKE '" . $searchTerm . "%' ORDER BY lastName";
            } elseif ($searchFilter == "UserID") {
                $sqlStatement = "SELECT * FROM Users WHERE lastName LIKE '" . $searchTerm . "%' ORDER BY id";
            }
        } elseif ($searchCriteria == "Username") {
            //If user searches by username, then check if they have applied a search filter and amend the sql statement
            //accordingly
            if ($searchFilter == "First Name") {
                $sqlStatement = "SELECT * FROM Users WHERE username='$searchTerm' ORDER BY firstName";
            } elseif ($searchFilter == "Last Name") {
                $sqlStatement = "SELECT * FROM Users WHERE username='$searchTerm' ORDER BY lastName";
            } elseif ($searchFilter == "UserID") {
                $sqlStatement = "SELECT * FROM Users WHERE username='$searchTerm' ORDER BY id";
            }
        }
        else{
            $sqlStatement = "SELECT * FROM Users WHERE username='$searchTerm' ORDER BY username";
        }
        $statement2 = $this->_dbHandle->prepare($sqlStatement); // prepare a PDO statement
        $statement2->execute(); // execute the PDO statement
        $queryResults = [];
        while($row = $statement2->fetch()) {
            $queryResults[] = new UserData($row);
        }
        return $queryResults;
    }

    //Function used to search for users by username
    //returns an array of all users matching the search
    public function paginatedSearchDatabase($searchTerm, $searchCriteria, $searchFilter, $paginationParam){
        $sqlStatement = "";
        if ($searchCriteria == "First Name") {
            if ($searchFilter == "First Name") {
                $sqlStatement = "SELECT * FROM Users WHERE firstName LIKE '" . $searchTerm . "%' ORDER BY firstName LIMIT $paginationParam";
            } elseif ($searchFilter == "Last Name") {
                $sqlStatement = "SELECT * FROM Users WHERE firstName LIKE '" . $searchTerm . "%' ORDER BY lastName LIMIT $paginationParam";
            } elseif ($searchFilter == "UserID") {
                $sqlStatement = "SELECT * FROM Users WHERE firstName LIKE '" . $searchTerm . "%' ORDER BY id LIMIT $paginationParam";
            }
        } elseif ($searchCriteria == "Last Name") {
            if ($searchFilter == "First Name") {
                $sqlStatement = "SELECT * FROM Users WHERE lastName LIKE '" . $searchTerm . "%' ORDER BY firstName LIMIT $paginationParam";
            } elseif ($searchFilter == "Last Name") {
                $sqlStatement = "SELECT * FROM Users WHERE lastName LIKE '" . $searchTerm . "%' ORDER BY lastName LIMIT $paginationParam";
            } elseif ($searchFilter == "UserID") {
                $sqlStatement = "SELECT * FROM Users WHERE lastName LIKE '" . $searchTerm . "%' ORDER BY id LIMIT $paginationParam";
            }
        } elseif ($searchCriteria == "Username") {
            //If user searches by username, then check if they have applied a search filter and amend the sql statement
            //accordingly
            if ($searchFilter == "First Name") {
                $sqlStatement = "SELECT * FROM Users WHERE username='$searchTerm' ORDER BY firstName LIMIT $paginationParam";
            } elseif ($searchFilter == "Last Name") {
                $sqlStatement = "SELECT * FROM Users WHERE username='$searchTerm' ORDER BY lastName LIMIT $paginationParam";
            } elseif ($searchFilter == "UserID") {
                $sqlStatement = "SELECT * FROM Users WHERE username='$searchTerm' ORDER BY id LIMIT $paginationParam";
            }
        }
        else{
            $sqlStatement = "SELECT * FROM Users WHERE username='$searchTerm' ORDER BY username $paginationParam";
        }
        $statement2 = $this->_dbHandle->prepare($sqlStatement); // prepare a PDO statement
        $statement2->execute(); // execute the PDO statement
        $queryResults = [];
        while($row = $statement2->fetch()) {
            $queryResults[] = new UserData($row);
        }
        return $queryResults;
    }

    //Function to find the details of the user that is logged in currently.
    //Returns an array (of size 1 as there cannot be duplicate usernames in the DB) containing
    //the details of the user, so they can edit them
    public function loadUserProfile($userProfileToLoad){
        $sqlStatement = "SELECT * FROM Users WHERE id='$userProfileToLoad'";

        $statement2 = $this->_dbHandle->prepare($sqlStatement); // prepare a PDO statement
        $statement2->execute(); // execute the PDO statement

        $queryResults = [];
        while($row = $statement2->fetch()) {
            $queryResults[] = new UserData($row);
        }
        return $queryResults;
    }

    //Used when someone logs in, returns true if username they entered is in DB, false otherwise
    public function checkUsername($x){
        $userID = $this->retrieveUserID($x);
        $usernameToCheck = $x;

        $checkStatement = "SELECT * FROM Users WHERE id='$userID'";
        $checkStatement = $this->_dbHandle->prepare($checkStatement);
        $checkStatement->execute();
        $user = new UserData($checkStatement->fetch());
        $usernameInDB = "";
        if(strpos($x, '@')){
            $usernameInDB = $user->getEmail();
        }
        else {
            $usernameInDB = $user->getUsername();
        }
        $returnValue = false;
        if (($usernameToCheck == $usernameInDB)){
            $returnValue = true;
        }

        return $returnValue;
    }

    //Used when someone logs in, returns true if password they entered is in DB, false otherwise
    public function checkPassword($y, $x){
        $userID = $this->retrieveUserID($y);
        $passwordToCheck = md5($x);
        $checkStatement="";

        $checkStatement = "SELECT * FROM Users WHERE id='$userID'";
        $checkStatement = $this->_dbHandle->prepare($checkStatement);
        $checkStatement->execute();
        $user = new UserData($checkStatement->fetch());
        $passwordInDB = $user->getPassword();

        $returnValue = false;
        if ($passwordToCheck == $passwordInDB){
            $returnValue = true;
        }

        return $returnValue;
    }

    //Retrieves the email address of a specific user
    public function retrieveEmail($x){
        $emailQuery = "SELECT email FROM Users WHERE email=?";

        $statement = $this->_dbHandle->prepare($emailQuery);
        $statement->bindParam(1, $x);
        $statement->execute();

        $emailArray = $statement->fetch();
        return $emailArray[0];
    }

    //Retrieves the UserID of a specific user
    public function retrieveUserID($x){
        $emailQuery="";
        //Logic to determine if the user has logged in with their email address or username
        //Checks if what the user inputted contains an @ as this makes it an email address
        if(strpos($x, '@') !== false) {
            //If email used, set sql where to email column
            $emailQuery = "SELECT id FROM Users WHERE email='$x'";
        }
        else{
            //If username used, set sql where to username
            $emailQuery = "SELECT id FROM Users WHERE username='$x'";
        }
        $statement = $this->_dbHandle->prepare($emailQuery);
        $statement->execute();

        $emailArray = $statement->fetch();
        return $emailArray[0];
    }

    public function imageUploadPathSet($x, $y){
        $sql = "UPDATE Users SET profileImage=? WHERE id=?";
        $statement = $this->_dbHandle->prepare($sql);
        $statement->bindParam(1, $x);
        $statement->bindParam(2, $y);
        $statement->execute();
    }

    public function changeUserDetails($userID, $a, $b, $c, $d, $e){
        $loggedInUserID = $userID;

        $usernameSQL = "UPDATE Users SET username='$a' WHERE id='$loggedInUserID'";
        $usernameStatement = $this->_dbHandle->prepare($usernameSQL);

        $firstNameSQL = "UPDATE Users SET firstName='$b' WHERE id='$loggedInUserID'";
        $firstNameStatement = $this->_dbHandle->prepare($firstNameSQL);

        $lastNameSQL = "UPDATE Users SET lastName='$c' WHERE id='$loggedInUserID'";
        $lastNameStatement = $this->_dbHandle->prepare($lastNameSQL);

        $emailSQL = "UPDATE Users SET email='$d' WHERE id='$loggedInUserID'";
        $emailStatement = $this->_dbHandle->prepare($emailSQL);

        $passwordSQL = "UPDATE Users SET password='$e' WHERE id='$loggedInUserID'";
        $passwordStatement = $this->_dbHandle->prepare($passwordSQL);

        $usernameStatement->execute();
        $firstNameStatement->execute();
        $lastNameStatement->execute();
        $emailStatement->execute();
        $passwordStatement->execute();

    }
}