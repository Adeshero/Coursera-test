<?php
session_start();

try{
	$db=new PDO('mysql:host=localhost;dbname=g_stock','root','');
}
catch(exception $e){
	die("Error:".$e->getmessage() );
}

$req=$db->prepare ('select id,nom,motDePasse from utilisateur where nom=? and motDePasse=?');
$req->execute(array($_POST['Nom'],$_POST['pass']));
$reqOrd=$req->fetch();

if ($reqOrd['id'] == 1 ){
	$_SESSION['id']=urlencode($reqOrd['id']);
	$_SESSION['nom']=urlencode($reqOrd['nom']);
	header("location:admin.php");
	
}
else{

	if(in_array($_POST['Nom'],$reqOrd) and in_array($_POST['pass'],$reqOrd)){
		$_SESSION['id']=urlencode($reqOrd['id']);
		$_SESSION['nom']=urlencode($reqOrd['nom']);
		header('Location:Fournisseurs.php');
		exit();
	}
	else
		echo "Profil non trouvé";
}

?>