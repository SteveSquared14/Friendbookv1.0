<?php
/*
 * This model was only used once to encrypt all the existing passwords in the database using md5 hash
 * I've left it here to show my php ability and that I can manipulate my DB using a custom PHP script
 */
class PasswordHasher{
    protected $id, $unhashedPassword;

    public function __construct($dbRow){
        $this->id = $dbRow['id'];
        $this->unhashedPassword = $dbRow['password'];
    }

    //Accessor methods for each of the private fields go below here//
    //Get the friendshipID
    public function getID(){
        return $this->id;
    }

    //Accessor methods for each of the private fields go below here//
    //Get the friendshipID
    public function getPassword(){
        return $this->unhashedPassword;
    }
}