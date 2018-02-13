<?php
class Contact
{
    public $id;
    public $name;
    public $email;
    public $phone;
    public $address;
    private $outputmode;

    function __construct($values = null, $outputmode = false) {
        $this->outputmode = $outputmode;
        if (is_array($values)) {
            $this->id = (isset($values['id'])) ? $values['id'] : 0;
            $this->name = $this->displayString($values['name']);
            $this->email = $this->displayString($values['email']);
            $this->phone = $this->displayString($values['phone']);
            $this->address = $this->displayString($values['address']);
        }
    }
    private function displayString($input) {
        if ($this->outputmode && strlen($input) == 0) {
            return '[not set]';
        } else return $input;
    }
}