<?php
require_once ('Models/Database.php');
Class Register{
    //Variables needed to create a new user record. Matches what is in the DB
    var $userID, $username, $firstName, $lastName, $email, $password, $longitude, $latitude, $profileImage;

    //Constructor to generate set the values of all the class variables
    public function __construct($newFirstName, $newLastName, $newEmail, $newPassword, $newUsername){
        //initiate the instance of the DB and get the connection
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();

        $this->firstName = $newFirstName;
        $this->lastName = $newLastName;
        $this->password = md5($newPassword);
        $this->email = $newEmail;
        $this->username = $newUsername;
        //Once the username has been created, append it to the end of a robohash link to make a default profile picture
        $this->profileImage = "https://robohash.org/" . $this->username;
        //longitude, latitude and profile image set to default values for the University of Salford
        $this->longitude = "53.4872";
        $this->latitude = "2.2737";

        //Call to a private function within the class to generate a user id and username
        $this->generateUserID();
    }

    //Makes the new user id by adding 1 to the largest id currently in the User DB table
    //Could be removed by making the UserID column of the Users DB auto-increment, but I wrote weeks before I learned about
    //the auto-increment feature so I stuck with it
    private function generateUserID(){
        //First get the largest ID number in the DB, then add 1 to it, then set it as this new users ID
        $idQuery = "SELECT MAX(id) from Users";
        $idStatement = $this->_dbHandle->prepare($idQuery);
        $idStatement->execute();
        $maxUserIDArray = $idStatement->fetch();
        $maxID = $maxUserIDArray[0];

        //Add 1 to the current largest ID so that it follows on and is still unique
        $this->userID = (int)$maxID + 1;
    }

    //stores a new user in the database once they have registered
    public function storeNewUser(){
        //SQL statement to inset the new user record into the DB using the newly updated variables
        //depending on what the user entered into the form
        $createUserSQL = "INSERT INTO Users (id, username, firstName, lastName, email, password, longitude, latitude, profileImage) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $statement = $this->_dbHandle->prepare($createUserSQL);

        $statement->bindParam(1, $this->userID);
        $statement->bindParam(2, $this->username);
        $statement->bindParam(3, $this->firstName);
        $statement->bindParam(4, $this->lastName);
        $statement->bindParam(5, $this->email);
        $statement->bindParam(6, $this->password);
        $statement->bindParam(7, $this->longitude);
        $statement->bindParam(8, $this->latitude);
        $statement->bindParam(9, $this->profileImage);

        //Execute the SQL statement to insert the new row into the DB and register the user
        $statement->execute();
    }
}
