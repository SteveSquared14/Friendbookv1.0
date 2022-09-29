<?php
$view = new stdClass();
require_once('Models/UserDataSet.php');
require_once('Models/UserSession.php');
$userDataSet = new UserDataSet();
//if the log in button is pressed, continue with the subsequent validation checks
$errorMessage = "";
if(isset($_POST['submit'])) {
    //if all the input fields have entries, continue with validation checks
    if(isset($_POST['logIn']) && isset($_POST['password']) && isset($_POST['userEntryCaptcha'])) {
        //works out what the value of the capture should be
        $correctCaptchaAnswer = $_POST['hiddenNumber1'] + $_POST['hiddenNumber2'];
        //checks if the user has got the captcha security question right
        if ($_POST['userEntryCaptcha'] == $correctCaptchaAnswer) {
            //if it gets to this stage, all validation has been done, so start a session
            //then check to see if what the user has entered matches a record in the DB and require the relevant page depending on the outcome
            $session = new UserSession();
            $logInOutcome = $session->checkValidLogIn($_POST['logIn'], $_POST['password']);
            if ($logInOutcome == true) {
                require_once('landingPageUser.php');
            }else {
                $errorMessage = "Error: Incorrect username or password!";
                $session->logout();
                require_once('index.php');
            }
        }
        else{
            $errorMessage =  "Error: Incorrect Captcha answer!";
            require_once('index.php');
        }
    }
    else{
        $errorMessage =  "Error: Please complete all fields!";
        require_once('index.php');
    }
}