<?php
// load models
require_once('models.php');

// initialize SOAP client and call web service function
$soapclient = new SoapClient('http://localhost/server.php?wsdl',['trace'=>1,'cache_wsdl'=>WSDL_CACHE_NONE]);

switch (@$_GET['mode']) {
    case 'add':
        if (!empty($_POST)) {
            $contact = new Contact($_POST);
            $result = $soapclient->addContact($contact);
            if ((bool)$result != true) var_dump($result);
        }
        break;
    default:
}
?>
<h1>Add a new contact</h1>
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
<table>
<thead>
    <th>
        Name
    </th>
    <th>
        Email
    </th>
    <th>
        Phone
    </th>
    <th>
        Address
    </th>
</thead>
<tbody>
<?php
$contacts = $soapclient->listContacts();
foreach (json_decode($contacts, true) as $contact) {
    $contact = new Contact($contact);
    echo '<tr>';
    echo "<td>$contact->name<td>";
    echo "<td>$contact->email<td>";
    echo "<td>$contact->phone<td>";
    echo "<td>$contact->address<td>";
    echo '</tr>';
}
?>
</tbody>
</table>