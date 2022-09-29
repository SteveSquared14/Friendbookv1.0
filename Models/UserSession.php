<?php
require_once('UserDataSet.php');
Class UserSession
{
    protected $username, $loggedIn, $userID;

    public function __construct()
    {
        //Start the session
        session_start();

        //assign default values to the class variables
        $this->username = "No user";
        $this->loggedIn = false;
        $this->userID = "0";

        //if the session is set, assign the class variables new values
        if (isset($_SESSION["login"])) {
            $this->username = $_SESSION["login"];
            //$this->userID = $_SESSION["uid"];
            $this->loggedIn = true;
        }
    }

    public function checkValidLogIn($newUsername, $newPassword)
    {
        //Return value, initially set to false so noone can log in
        $verification = false;

        //make a new data set and then check if the username and password entered by the user exist in the database
        //if they do, it returns a result which is an array of size 1
        $validUsers = new UserDataSet();
        $usernameCheck = $validUsers->checkUsername($newUsername);
        $passwordCheck = $validUsers->checkPassword($newUsername, $newPassword);
        //if the size of the variable $validUsersDataSet is bigger than 0 the user must already exist in the database
        //so assign the username and password they entered to the session created and then
        //change the value of the verification variable to true as the session has been set
        if (($usernameCheck == true) && ($passwordCheck == true)) {
            //log the user in
            $userDataSet = new UserDataSet();
            $_SESSION['login'] = $newUsername;
            $_SESSION['uid'] = $userDataSet->retrieveUserID($newUsername);
            $this->loggedIn = true;
            $this->username = $newUsername;
            $this->userID = $validUsers->retrieveUserID($newUsername);
            $verification = true;
        }
        else{
            $verification = false;
        }
        return $verification;
    }

    //Log the user out, unset all session values and then destroy the session
    public function logout()
    {
        unset($_SESSION['login']);
        unset($_SESSION['uid']);
        $this->loggedIn = false;
        $this->username = "No user";
        $this->userID = "0";
        session_destroy();
    }

    //Returns true is the user is logged in, false otherwise
    public function isLoggedIn()
    {
        return $this->loggedIn;
    }

    //Returns the username of the session user
    public function userName(){
        return $this->username;
    }
}