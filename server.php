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

function listContacts($id = 0) {
    global $database;
    if ($id != 0 ) {
        return json_encode($database->select('users', '*', [
            'id' => $id
        ])[0]);
    } else {
        $data = $database->select('users', '*');
        $contacts = array();
        foreach ($data as $contact_arr) {
            $contacts[] = new Contact($contact_arr);
        }
        return json_encode($contacts);
    }
}

function editContact($contact) {
    global $database;
    $database->update('users', [
        'name' => $contact->name,
        'email' => $contact->email,
        'phone' => $contact->phone,
        'address' => $contact->address,
    ], [
        'id' => $contact->id
    ]);
    return true;
}

function searchContacts($input) {
    global $database;
    $data = $database->select('users', '*', [
        'name[~]' => $input
    ]);
    $contacts = array();
    foreach ($data as $contact_arr) {
        $contacts[] = new Contact($contact_arr);
    }
    return json_encode($contacts);
}

function deleteContact($id) {
    global $database;
    $database->delete('users', [
        'id' => $id
    ]);
    return true;
}

$database = new Medoo\Medoo([
    'database_type' => 'mysql',
    'database_name' => 'phonebook',
    'server' => 'localhost',
    'username' => 'root',
    'password' => ''
]);

$server = new SoapServer("phonebook.wsdl",[
    'classmap' => [
        'contact' => 'Contact',
    ]
]);

try {
    $server->addFunction('addContact');
    $server->addFunction('deleteContact');
    $server->addFunction('listContacts');
    $server->addFunction('searchContacts');
    $server->addFunction('editContact');
    $server->handle();
} catch (SoapFault $exc) {
    return $exc->getTraceAsString();
}