<?php

set_time_limit(120);

$connection = mysql_connect('localhost', 'root', '123456');
mysql_select_db('cloudcart');

$countries = array("Bulgaria", "Germany", "France", "Russia", "Turkey");
$firstNames = array("Ivan", "Petar", "Tihomir", "Georgi", "Dimitar", "Lachezar", "Samuil", "Cvetan", "Stefan");
$lastNames = array("Ivanov", "Petrov", "Stefanov", "Georgiev", "Dimitrov", "Petkov", "Krumov", "Cvetanov");
$mailProviders = array("gmail.com", "gmail.bg", "abv.bg", "dir.bg", "mail.bg", "yahoo.com", "hotmail.com", "live.co.uk");

for ($i = 0; $i < 1000; $i++) {
    $firstName = randomFirstName();
    $lastName = randomLastName();
    $mail = randomMail($firstName, $lastName);
    $username = randomUsername($firstName, $lastName);
    $date = randomDate();
    $country = randomCountry();
    $password = randomPassword();

    mysql_query("INSERT INTO `users`(`username`, `password`, `country`, `name`, `lastname`, `email`, `date`)
                VALUES('{$username}', '{$password}', '{$country}', '{$firstName}', '{$lastName}', '{$mail}', '{$date}')");
}

function randomDate() {
    $timestamp = mt_rand(1388530800, 1419980400);
    return date("Y-m-d", $timestamp);
}

function randomCountry() {
    global $countries;
    return $countries[array_rand($countries)];
}

function randomFirstName() {
    global $firstNames;
    return $firstNames[array_rand($firstNames)];
}

function randomLastName() {
    global $lastNames;
    return $lastNames[array_rand($lastNames)];
}

function randomMail($firstName, $lastName) {
    global $mailProviders;
    $provider = $mailProviders[array_rand($mailProviders)];

    switch (mt_rand(0, 2)) {
        case 0:
            return "{$firstName}.{$lastName}@{$provider}";
        case 1:
            return "{$lastName}.{$firstName}@{$provider}";
        default:
            return substr($firstName, 0, 1) . ".{$lastName}@{$provider}";
    }
}

function randomUsername($firstName, $lastName) {
    return "{$firstName}.{$lastName}_" . substr(time(), mt_rand(0, 9), mt_rand(1, 2));
}

function randomPassword() {
    return substr(md5(uniqid() . time()), mt_rand(5, 16), mt_rand(8, 16));
}
