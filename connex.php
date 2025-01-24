
<?php
try{
	$db=new PDO('mysql:host=localhost;dbname=g_stock','root','');
}
catch(exception $e){
	die("Error:".$e->getmessage() );
}

?>