<?php
$view = new stdClass();
require_once('Models/UserDataSet.php');
require_once('Models/Register.php');

$responseMessage = "";
//Checks to see if the submit button on the form was pressed
if(isset($_POST['register'])) {
    $password = $_POST['password'];
    if(strlen($password) >= 8) {
        if (preg_match('/[A-Z]/', $password)) {
            if (preg_match('/\W/', $password)) {
                if (preg_match('/\d/', $password)) {
                    //Make a new instance of the register class, passing it the necessary parameters from the form the user completed
                    $registerNewUser = new Register($_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['password'], $_POST['username']);
                    //Call to the function which actually "registers" the users. Aka, inserts there info into the Users table in the DB
                    $registerNewUser->storeNewUser();

                    //Now check if the storeNewUser() function was a success and the user is registered, let them know
                    $emailSearch = new UserDataSet();
                    $emailToCheck = $emailSearch->retrieveEmail($_POST['email']);
                    if($emailToCheck == $_POST['email']) {
                        $responseMessage = "Congrats, you have registered!";
                        require_once('index.php');
                    } else { //If the storeNewUser() function was not successful, let the user know
                        echo "Error: Sorry, we could not register you at this time.";
                        require_once('index.php');
                    }
                } else {
                    $responseMessage = "Error: Password must contain at least one number between 0-9!";
                    require_once('index.php');
                }
            } else {
                $responseMessage = "Error: Password must contain at least one special character!";
                require_once('index.php');
            }
        }
        else{
            $responseMessage = "Error: Password must contain an uppercase letter!";
            require_once('index.php');
        }
    }else {
        $responseMessage = "Error: Password must contain 8 or more characters!";
        require_once('index.php');
    }
}