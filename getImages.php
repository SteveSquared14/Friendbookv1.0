<?php
/*
 * Only used this model once to download 1000 images for half the users in my database.
 * Kept in as proof, but never used again or in the live system
 */
require_once ('Models/UserDataSet.php');
print_r('downloading...<br>');

$searchFor = "Head";
$path = "";
$userDataSet = new UserDataSet();
//get all the images from the DB that end in .jpg
$imagesFromDB = $userDataSet->getJpgImages();
echo sizeof($imagesFromDB);
echo '<br>';
//assign them to an array for downloading
for($j=0; $j<sizeof($imagesFromDB); $j++){
    $images[$j] = $imagesFromDB[$j];
}

//download each persons image and store it in the Images folder
for($i=0; $i<=sizeof($imagesFromDB);$i++) {
    $url = 'https://loremflickr.com/240/160/' . $searchFor;
    print_r($imagesFromDB[$i] . " downloading " . $url);
    print_r('<br>');
    print_r('<br>');
    file_put_contents("Images/" . "$imagesFromDB[$i]", file_get_contents($url));
}