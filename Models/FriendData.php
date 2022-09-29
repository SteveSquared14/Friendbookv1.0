<?php

class FriendData extends UserData {
    protected $userID, $username, $firstName, $lastName, $email, $password, $longitude, $latitude, $profileImage;
    protected $friendshipID, $friend1, $friend2, $friendshipStatus;

    public function __construct($dbRow){
        $this->userID = $dbRow['id'];
        $this->username = $dbRow['username'];
        $this->firstName = $dbRow['firstName'];
        $this->lastName = $dbRow['lastName'];
        $this->email = $dbRow['email'];
        $this->password = $dbRow['password'];
        $this->longitude= $dbRow['longitude'];
        $this->latitude= $dbRow['latitude'];
        $this->profileImage = $dbRow['profileImage'];
        $this->friendshipID = $dbRow['friendShipID'];
        $this->friend1 = $dbRow['friend1ID'];
        $this->friend2 = $dbRow['friend2ID'];
        $this->friendshipStatus = $dbRow['status'];
    }

    //Accessor methods for each of the private fields go below here//
    //Get the friendshipID
    public function getFriendshipID(){
        return $this->friendshipID;
    }

    //Accessor methods for each of the private fields go below here//
    //Get friend 1's ID
    public function getFriend1ID(){
        return $this->friend1;
    }

    //Accessor methods for each of the private fields go below here//
    //Get friend 2's ID
    public function getFriend2ID(){
        return $this->friend2;
    }

    //Accessor methods for each of the private fields go below here//
    //Get the status ID for the friendship
    public function getFriendshipStatusID(){
        return $this->friendshipStatus;
    }
}