<?php
session_start();
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>admin</title>
</head>
<body>
    <div>
        <form action='admin.php' method='post'>
            <label for="idDeAction">Sélection de l'action</label>
            <select name="Action" id="idDeAction">
                <option value="AfficherProduit">Afficher Produit</option>
                <option value="AfficherFournisseur">Afficher Fournisseur</option>
                <option value="AjouterFournisseur">Ajouter Fournisseur</option>
            </select>
            <input type="submit" value="Valider">
        </form>
    </div>
    
<?php
    if (isset($_POST['Action'])) {
        try{
			$db=new PDO('mysql:host=localhost;dbname=g_stock','root','');
		}
		catch(exception $e){
			die("Error:".$e->getmessage() );
		}
        if ($_POST['Action'] == 'AfficherProduit') {
			
			?>
			
				<form action='adminAction.php' method='post'>
				
					
				
				</form>
			
			<?php
			
            $requete = $db->query('SELECT id, nom, description, prix, quantite, DATE_FORMAT(dateAjout, \'%d/%m/%Y %Hh%imin%ss\') AS date FROM Produits ORDER BY nom DESC');
            
            if ($requete->rowCount() <= 0) {
                echo "Aucun produit dans le stock";
            } else {
                while ($req = $requete->fetch()) {
                    ?>
                    <div>Prod°<?php echo $req['id'] . " " . $req['nom'] . " coût : " . $req['prix'] . " ajouté le " . $req['date']; ?>
                    <div><?php echo $req['description']; ?></div>
                    </div>
                    <?php
                }
            }
        }
		elseif($_POST['Action']=='AfficherFournisseur'){
			$requete=$db->query('select * FROM Fournisseurs ');
			if ($requete->rowCount() <=0)
				echo "Aucun fournisseur";
			else{
				while($req= $requete->fetch()){
					?>
					<div>Fourn° <?php echo $req['id']." Mr ".$req['nom']." Adresse ".$req['adresse']." email: ".$req['email'] ?> </div>
					<?php
				}
			}
		}
	
		elseif($_POST['Action']=='AjouterFournisseur'){
			
			?>
			
			<form method="post" action="ajoutFournnisseur.php" name='fournisseur'>
				<label for='nom'>Entrer le nom du fournisseur* :</label>
				<input  id='nom' name='nom'><br>
				<label for ='adresse'> Entrer l'adresse :</label>
				<input id='adresse' name='adresse'><br>
				<label for='telephone'> Entrer le tel:</label>
				<input id='telephone' name='telephone'><br>
				<label for='email'>Email :</label>
				<input type='email' id='email' name='email'><br>
				<label for='passFour'>Entrer le mot de passe du Fournisseur :</label>
				<input id='passFour' name='passFour' type='password'><br>
				<label for="pass">Entrer le mot de pass admin :</label>
				<input type="password" id ='pass' name='pass'> 
				<br>
				<input type="submit" value="Valider">
			</form>
			
			<?php 
			
			
			
			
		}
    }
	
?>
    
</body>
</html>