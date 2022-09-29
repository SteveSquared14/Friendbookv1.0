<?php
require_once('Models/UserDataSet.php');
require_once('Models/UserSession.php');
require_once('Models/UserData.php');
$session = new UserSession();
$userDataSet = new UserDataSet();

if(isset($_POST['imageUploadBtn'])){
    //find the file that was uploaded in the form
    $file = $_FILES['fileToUpload'];

    //assign each value of the FILE array to a variable
    $fileName = $_FILES['fileToUpload']['name'];
    $fileTmpName = $_FILES['fileToUpload']['tmp_name'];
    $fileSize = $_FILES['fileToUpload']['size'];
    $fileError = $_FILES['fileToUpload']['error'];
    $fileType = $_FILES['fileToUpload']['type'];

    //Explode the array of the filename to reconstruct the actual file name
    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    //Array of allowed file types on the site
    $allowed = array('jpg');
    //Conduct checks and output appropriate error messages as required
    $imageUploadResponse = "";
    if(in_array($fileActualExt, $allowed)){
        if($fileError === 0){
            if($fileSize < 1000000){
                $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                $fileDestination = 'Images/' . $fileNameNew;

                //upload the file to the destination folder for the site
                move_uploaded_file($fileTmpName, $fileDestination);
                //change the file name in the DB for the user
                $userDataSet->imageUploadPathSet($fileNameNew, $_SESSION['uid']);
                //Loop the user back to the profile page so they don't notice anything "changing"
                $imageUploadResponse = "Image successfully uploaded";
                require_once('profilePage.php');
            }else{
                $imageUploadResponse = ' Your file is too big!';
                require_once('profilePage.php');
            }
        }else{
            $imageUploadResponse = 'There was an error uploading your file!';
            require_once('profilePage.php');
        }
    }else{
        $imageUploadResponse = 'You cannot upload files of this type!';
        require_once('profilePage.php');
    }
}