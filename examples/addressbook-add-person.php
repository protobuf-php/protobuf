#!/usr/bin/env php
<?php

$loader = require_once __DIR__ . '/../vendor/autoload.php';

$loader->add('Tutorial\AddressBookProtos', __DIR__ . '/src');

use Tutorial\AddressBookProtos\Person;
use Tutorial\AddressBookProtos\AddressBook;

if ( ! class_exists('\Tutorial\AddressBookProtos\Person')) {

    fwrite(STDERR,
        'You need to generate the php classes using the following command:' . PHP_EOL .
        './vendor/bin/protobuf --include-descriptors -i ./examples/ -o ./examples/src/ ./examples/addressbook.proto' . PHP_EOL
    );

    exit(1);
}

if ( ! isset($argv[1])) {
    echo "Usage: ./examples/addressbook-add-person.php ADDRESS_BOOK_FILE" . PHP_EOL;
    exit(1);
}

// Read the existing address book or create a new one.
$addressBook = is_file($argv[1])
    ? new AddressBook(file_get_contents($argv[1]))
    : new AddressBook();

$person = new Person();
$id     = intval(readline("Enter person ID: "));
$name   = trim(readline("Enter person name: "));
$email  = trim(readline("Enter email address (blank for none): "));

$person->setId($id);
$person->setName($name);

if ( ! empty($email)) {
    $person->setEmail($email);
}

while (true) {
    $number = trim(readline("Enter a phone number (or leave blank to finish):"));

    if (empty($number)) {
        break;
    }

    $phone  = new Person\PhoneNumber();
    $type   = trim(readline("Is this a mobile, home, or work phone? "));

    switch (strtolower($type)) {
        case 'mobile':
            $phone->setType(Person\PhoneType::MOBILE());
            break;
        case 'work':
            $phone->setType(Person\PhoneType::WORK());
            break;
        case 'home':
            $phone->setType(Person\PhoneType::HOME());
            break;
        default:
            echo "Unknown phone type. Using default." . PHP_EOL;
    }

    $phone->setNumber($number);
    $person->addPhone($phone);
}

// Add a person.
$addressBook->addPerson($person);

// Print current address book
echo $addressBook;

// Write the new address book back to disk.
file_put_contents($argv[1], $addressBook->toStream());