<?php

include 'connex.php';

if(isset($_POST['pass'])){
	// vérification du mot de passe 
	
	$req=$db->query('select motDePasse from utilisateur where id=1');
	$reqOrd=$req->fetch();
	if($_POST['pass']===$reqOrd['motDePasse']){
		$req=$db->prepare('insert into fournisseurs(nom,adresse,telephone,email) values(?,?,?,?)');
		$req->execute(array($_POST['nom'],$_POST['adresse'],$_POST['telephone'],$_POST['email']));
		//insertion d'un nouveau utilisateur 
		
		$req=$db->prepare('insert into utilisateur(nom,motDePasse) values (?,?)');
		$req->execute(array($_POST['nom'],$_POST['passFour']));
		echo "fournisseur enrégistré avec succès";
		
	}
	else
	{
		echo"Mot de passe incorrect";
		
	}
}

?>