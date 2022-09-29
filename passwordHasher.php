<?php
/*
 * Only used this model once to download 1000 images for half the users in my database.
 * Kept in as proof, but never used again or in the live system
 */
require_once ('Models/UserDataSet.php');
print_r('Generating...<br><br>');

$searchFor = "Head";
$path = "";
$userDataSet = new UserDataSet();
//get all the images from the DB that end in .jpg
$unhashedPasswordsFromDB = $userDataSet->passwordHasher();