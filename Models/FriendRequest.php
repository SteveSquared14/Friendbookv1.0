<?php
require_once ('Models/Database.php');
require_once ('Models/UserDataSet.php');
class FriendRequest{
    protected $_dbHandle, $_dbInstance;
    var $friendshipID;

    public function __construct() {
        //set up the DB
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
        $this->generateFriendshipID();
    }

    //Generates a new friendship ID by adding 1 to the current largest friendshipID
    //The Friendships DB table is set to auto-increment, but this function is needed for the call to
    //the checkDuplicateFriendsip() function
    private function generateFriendshipID(){
        //First get the largest ID number in the DB, then add 1 to it, then set it as this new users ID
        $idQuery = "SELECT MAX(friendshipID) from Friendships";
        $idStatement = $this->_dbHandle->prepare($idQuery);
        $idStatement->execute();
        $maxUserIDArray = $idStatement->fetch();
        $maxID = $maxUserIDArray[0];

        //Add 1 to the current largest ID so that it follows on and is still unique
        $this->friendshipID = (int)$maxID + 1;
    }

    //creates and inserts a new friendship in the Friendships table
    public function storeFriendRequest($y){
        $friend1Username = $_SESSION['login'];
        $userDataSet = new UserDataSet();
        $friend1ID = $userDataSet->retrieveUserID($friend1Username);
        $status = 1;
        if($userDataSet->checkDuplicateFriendship($_SESSION['login'], $this->friendshipID) == true){
            echo 'Warning, you are already friends with this user!';
        }
        else{
            $storeFriendRequestStatement = "INSERT INTO Friendships (friendshipID, friend1ID, friend2ID, status) VALUES (?,?,?,?)";
            $storeFriendRequest = $this->_dbHandle->prepare($storeFriendRequestStatement);

            $storeFriendRequest->bindParam(1, $this->friendshipID);
            $storeFriendRequest->bindParam(2, $friend1ID);
            $storeFriendRequest->bindParam(3, $y);
            $storeFriendRequest->bindParam(4, $status);
            $storeFriendRequest->execute();
        }
    }

    //Makes a new friendship with blocked status id (4)
    public function blockUser($y){
        $friend1ID = $_SESSION['uid'];
        //$userDataSet = new UserDataSet();
        $status = 4;
        $storeFriendRequestStatement = "INSERT INTO Friendships (friendShipID, friend1ID, friend2ID, status) VALUES ('$this->friendshipID','$friend1ID','$y','$status')";
        $storeFriendRequest = $this->_dbHandle->prepare($storeFriendRequestStatement);

        /*
        $storeFriendRequest->bindParam(1, $this->friendshipID);
        $storeFriendRequest->bindParam(2, $friend1ID);
        $storeFriendRequest->bindParam(3, $y);
        $storeFriendRequest->bindParam(4, $status);
        */
        //var_dump($storeFriendRequest);
        $storeFriendRequest->execute();
    }

    //Updates the status id of the friendship from 1 to 2 (rejected to accepted)
    public function acceptFriendRequest($y){
        $status = 2;
        $sql = "UPDATE Friendships SET status=? WHERE friendShipID =?";
        $sqlStatement = $this->_dbHandle->prepare($sql);

        $sqlStatement->bindParam(1, $status);
        $sqlStatement->bindParam(2, $y);
        $sqlStatement->execute();
    }


    //removes the friendship from the DB as the user gets requested does not want to be friends
    public function rejectFriendRequest($y){
        $sql = "DELETE FROM Friendships WHERE friendShipID=?";
        $sqlStatement = $this->_dbHandle->prepare($sql);

        $sqlStatement->bindParam(1, $y);
        $sqlStatement->execute();
    }
}