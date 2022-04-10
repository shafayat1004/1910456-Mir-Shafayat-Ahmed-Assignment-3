<?php

class User {
    public $firstName = "";
    public $lastName = "";
    public $birthdate = "";
}

$user = new User();
$user->firstName = "Shafayat";
$user->lastName = "Ahmed";

json_encode($user);
$user->birthdate = new DateTime();
echo json_encode($user);


?>