<?php
//load libraries
require 'vendor/autoload.php';

//turn off WSDL cache
ini_set("soap.wsdl_cache_enabled","0");

//load models
require_once('models.php');

function addContact($contact) {
    global $database;
    $database->insert('users', [
        'name' => $contact->name,
        'email' => $contact->email,
        'phone' => $contact->phone,
        'address' => $contact->address,
    ]);
    return true;
}

function listContacts() {
    global $database;
    $data = $database->select('users', [
        'id',
        'name',
        'email',
        'phone',
        'address'
    ]);
    $contacts = array();
    foreach ($data as $contact_arr) {
        $contacts[] = new Contact($contact_arr);
    }
    return json_encode($contacts);
}

$database = new Medoo\Medoo([
    'database_type' => 'mysql',
    'database_name' => 'phonebook',
    'server' => 'localhost',
    'username' => 'root',
    'password' => ''
]);

$server = new SoapServer("phonebook.wsdl",[
    'classmap'=>[
        'contact'=>'Contact',
    ]
]);

try {
    $server->addFunction('addContact');
    $server->addFunction('listContacts');
    $server->handle();
} catch (SoapFault $exc) {
    return $exc->getTraceAsString();
}