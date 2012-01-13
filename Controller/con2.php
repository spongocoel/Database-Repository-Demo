<pre>
<?php

require_once('../Domain/Contact.php');
require_once('../Domain/ContactCriteria.php');
require_once('../Domain/Repository2.php');

// create object
$newContact = new Contact();
$newContact->name = 'New Contact';
$newContact->address = '123 Main St.';
$newContact->city = 'Austin';
$newContact->state = 'TX';
$newContact->zip = '78722';
$newContact->birthday = '2010-09-14';

$repo = new Repository2();



// save object
$id = $repo->saveContact($newContact);
if(!$id)
{
	die('Saving Contact Failed');
}
echo "Contact saved, id# $id \n\n";



// retrieve object
$criteria = new ContactCriteria();
$criteria->id = $id;
$criteria->limit = 1;
$retrievedObject = $repo->getContact($criteria);
if(!$retrievedObject)
{
	die('Retrieving Contact Failed');
}

print_r($retrievedObject);




// change and save object
$newContact->address = '456 Oak St.';
$repo->saveContact($newContact);



// get a bunch of contacts
$criteria = new ContactCriteria();
$criteria->active = true;
$criteria->nameLike = 'New';
$criteria->birthdayOnOrBefore = date('Y-m-d', strtotime('-30 years')); // 30 years old or older
$matches = $repo->getContact($criteria);

echo "\n\n" . count($matches) . " contacts found\n";
foreach($matches as $retrievedObject)
{
	echo $retrievedObject->name . ' ' . $retrievedObject->birthday . "\n";
}





