<pre>
<?php

require_once('../Domain/Contact.php');
require_once('../Domain/Repository1.php');

// create object
$newContact = new Contact();
$newContact->name = 'New Contact';
$newContact->address = '123 Main St.';
$newContact->city = 'Austin';
$newContact->state = 'TX';
$newContact->zip = '78722';
$newContact->birthday = '2010-09-14';

$repo = new Repository1();

// save object
$id = $repo->saveContact($newContact);
if(!$id)
{
	die('Saving Contact Failed');
}

echo "Contact saved, id# $id \n\n";

// retrieve object
$retrievedObject = $repo->getContact($id);
if(!$retrievedObject)
{
	die('Retrieving Contact Failed');
}

print_r($retrievedObject);
