<?php
class Contact
{
    public $id;
    public $name;
    public $email;
    public $phone;
    public $address;

    function __construct($values = null) {
        if (is_array($values)) {
            if (isset($values['id'])) $this->id = $values['id'];
            $this->name = $values['name'];
            $this->email = $values['email'];
            $this->phone = $values['phone'];
            $this->address = $values['address'];
        }
    }
}