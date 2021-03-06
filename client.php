<?php
//load models
require_once('models.php');

//initialize SOAP client
$soapclient = new SoapClient('http://localhost/server.php?wsdl',[
    'trace' => 1,
    'cache_wsdl' => WSDL_CACHE_NONE,
    'classmap' => [
        'contact' => 'Contact',
    ]
]);

function showContactResult($result) {
    $result = json_decode($result, true);
    if (is_array($result) && count($result) > 0) {
        $return = "<br>Listing ".count($result)." Entries";
    } else if (empty($result)) {
        return "[nothing to show]";
    } else {
        return print_r($result, true);
    }
    $return .= '<table><thead><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Options</th></thead><tbody>';
    foreach ($result as $contact) {
        $contact = new Contact($contact, true);
        $return .= '<tr>';
        $return .= "<td>$contact->name</td>";
        $return .= "<td>$contact->email</td>";
        $return .= "<td>$contact->phone</td>";
        $return .= "<td>$contact->address</td>";
        $return .= "<td><a href='?mode=delete&id=$contact->id'>Delete</a> | <a href='?mode=edit&id=$contact->id'>Edit</a></td>";
        $return .= '</tr>';
    }
    return $return . '</tbody></table>';
}

?>
<h1><u>Phone book</u></h1>
<?php

switch (@$_GET['mode']) {
    case 'add':
        if (!empty($_POST)) {
            $contact = new Contact($_POST);
            $result = $soapclient->addContact($contact);
            if ($result != true) var_dump($result);
        }
        break;
    case 'delete':
        $id = $_GET['id'];
        $result = $soapclient->deleteContact($id);
        if ($result != true) {
            var_dump($result);
        } else {
            echo 'Contact deleted. Please <a href="'.$_SERVER['SCRIPT_NAME'].'">refresh</a>';
        }
        break;
    case 'search':
        if (!empty($_POST) && strlen($_POST['name']) > 0) {
            $name = $_POST['name'];
            $result = $soapclient->searchContacts($name);
            echo "<h2>Search results for '$name':</h2>" . showContactResult($result);
        }
        break;
    case 'edit':
        if (empty($_POST)) {
            $id = $_GET['id'];
            $result = json_decode($soapclient->listContacts($id), true);
            $contact = new Contact($result);
            echo '
                <h2>Edit contact no.'.$id.'</h2>
                <form method="POST" action="' . $_SERVER['SCRIPT_NAME'] . '?mode=edit">
                    <input name="id" type="hidden" value="'.$contact->id.'">
                    <p>
                        Name:
                        <input name="name" value="'.$contact->name.'">
                    </p>
                    <p>
                        Email:
                        <input name="email" value="'.$contact->email.'" type="email">
                    </p>
                    <p>
                        Phone:
                        <input name="phone" value="'.$contact->phone.'" type="number">
                    </p>
                    <p>
                        Address:
                        <input name="address" value="'.$contact->address.'">
                    </p>
                    <p>
                        <input type="submit">
                    </p>
                </form>';
        } else {
            $contact = new Contact($_POST);
            $status = $soapclient->editContact($contact);
            if ($status != true) {
                var_dump($status);
            } else {
                echo 'Contact updated.';
            }
        }
        break;
}
?>

<h2>Search for a contact</h2>
<form method="POST" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>?mode=search">
    <p>
        Name:
        <input name="name">
    </p>
    <p>
        <input type="submit">
    </p>
</form>

<h2>Add a new contact</h2>
<form method="POST" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>?mode=add">
    <p>
        Name:
        <input name="name">
    </p>
    <p>
        Email:
        <input name="email" type="email">
    </p>
    <p>
        Phone:
        <input name="phone" type="number">
    </p>
    <p>
        Address:
        <input name="address">
    </p>
    <p>
        <input type="submit">
    </p>
</form>
<br>
<h2>All contacts</h2>
<?php
$contacts = $soapclient->listContacts();
echo showContactResult($contacts);
?>