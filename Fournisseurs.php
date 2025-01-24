<?php
session_start();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Fournisseur <?php echo htmlspecialchars($_SESSION['nom']);?></title>
</head>
<body>

	<h1>BIENVENU Mr <?php echo htmlspecialchars($_SESSION['nom']); ?></h1>
	
	
	
	<form method='POST' action='Fournisseurs.php' name='Affichage'>
	<label for='selection'>	Afficher  </label>
	<select name='Affichage' id='selection' >
	
	<option>Aficher ...</option>
	<option value='liste des produits'>la liste des produits</option>
	<option value='demandes de livraisons'>les demandes de livraisons</option>
	<option value='liste des produits livrés'>la liste des produits livrés</option>
	<option value='liste des produits en attente de confirmation'>la liste des produits en attente de confirmation</option>

	</select><br>
	<input type='submit' value='valider' name='valider'>
	</form>
	<?php
	
	//connection à la base de donner
	include 'connex.php';
	
	//les affichages 
		if (isset($_POST['Affichage'])){
			if($_POST['Affichage']=='liste des produits'){
				$req=$db->prepare('select id,nom,description,prix,quantite,date_format(dateAjout,\'%d/%m/%Y à %Hh%imin%Ss\') as date  from produits where fournisseurID=? order by dateAjout DESC');
				$req->execute(array($_SESSION['id']));
				?>
				<table>
					<tr>
						<th scope="col">N°</th>
						<th scope="col">nom</th>
						<th scope="col">description</th>
						<th scope="col">prix</th>
						<th scope="col">quantite</th>
						<th scope="col">date</th>
				
					</tr>
				<?php	
				$i=1;
				while($reqOrd=$req->fetch()){
					?>
					<tr>
						<td> <?php echo $i ?></td>
						<td> <?php echo $reqOrd['nom'] ?></td>
						<td> <?php echo $reqOrd['description'] ?></td>
						<td> <?php echo $reqOrd['prix'] ?></td>
						<td> <?php echo $reqOrd['quantite'] ?></td>
						<td> <?php echo $reqOrd['date'] ?></td>
					</tr>
					<?php
					$i++;
				}
				?>
				</table>
				<?php
			}
			elseif($_POST['Affichage']=='demandes de livraisons'){
				$req=$db->prepare('select id,nom,description,quantite,date_format(\'dateLimite\',\'%/d/%m/%Y à %Hh%imin%Ss\') as date ,fournisseurID from demandes where fournisseurID=? or fournisseurID is null order by dateLimite DESC');
				$req->execute(array($_SESSION['id']));
				if($reqOrd=$req->rowCount()<=0){
						echo"Aucune demande pour l'instant ";
				}
				else{
					?>
					<table>
						<tr>
							<th scope="col">N°</th>
							<th scope="col">nom</th>
							<th scope="col">description</th>
							
							<th scope="col">quantite</th>
							<th scope="col">dateLimite</th>
							<th scope="col">Fournisseur</th>
					
						</tr>
					<?php	
					$i=1;
					while($reqOrd=$req->fetch()){
						?>
						<tr>
							<td> <?php echo $i ?></td>
							<td> <?php echo $reqOrd['nom'] ?></td>
							<td> <?php echo $reqOrd['description'] ?></td>
							<td> <?php echo $reqOrd['quantite'] ?></td>
							<td> <?php echo $reqOrd['date'] ?></td>
							<td> <?php echo $reqOrd['fournisseurID'] ?></td>
						</tr>
						<?php
						$i++;
					}
					?>
				</table>
				<?php
				}
				
			}
			elseif($_POST['Affichage']=='liste des produits livrés'){
				
				$req=$db->prepare('select id,nom,quantite,date_format(\'dateLivraison\',\'%/d/%m/%Y à %Hh%imin%Ss\') as date,dateConfirmation,fournisseurID,idCommande  from prodlivrer where fournisseurID=?  order by dateConfirmation DESC');
				$req->execute(array($_SESSION['id']));
				if($reqOrd=$req->rowCount()<=0){
						echo"Aucune confirmation pour l'instant ";
				}
				else{
					?>
					<table>
						<tr>
							<th scope="col">N°</th>
							<th scope="col">nom</th>
							<th scope="col">quantité</th>
							
							<th scope="col">date de livraison</th>
							<th scope="col">dateConfirmation</th>
							<th scope="col">Fournisseur</th>
							<th scope="col">Identifiant de la commande</th>
					
						</tr>
					<?php	
					$i=1;
					while($reqOrd=$req->fetch()){
						?>
						<tr>
							<td> <?php echo $i ?></td>
							<td> <?php echo $reqOrd['nom'] ?></td>
							<td> <?php echo $reqOrd['quantite'] ?></td>
							<td> <?php echo $reqOrd['date'] ?></td>
							<td> <?php echo $reqOrd['dateConfirmation'] ?></td>
							<td> <?php echo $reqOrd['fournisseurID'] ?></td>
							<td> <?php echo $reqOrd['idCommande'] ?></td>
						</tr>
						<?php
						$i++;
					}
					?>
				</table>
				<?php
					}
				
			}
			elseif($_POST['Affichage']=='liste des produits en attente de confirmation'){
				
				$req=$db->prepare('select id,nom,quantite,date_format(\'dateLivraison\',\'%/d/%m/%Y à %Hh%imin%Ss\') as date,fournisseurID,idCommande  from attconfirmation where fournisseurID=?  order by dateLivraison DESC');
				$req->execute(array($_SESSION['id']));
				if($reqOrd=$req->rowCount()<=0){
						echo"Aucune commande en attente pour l'instant ";
				}
				else{
					?>
					<table>
						<tr>
							<th scope="col">N°</th>
							<th scope="col">nom</th>
							<th scope="col">quantité</th>
							
							<th scope="col">dateLivraison</th>
							<th scope="col">fournisseur</th>
							<th scope="col">Id de la commande</th>
					
						</tr>
					<?php	
					$i=1;
					while($reqOrd=$req->fetch()){
						?>
						<tr>
							<td> <?php echo $i ?></td>
							<td> <?php echo $reqOrd['nom'] ?></td>
							<td> <?php echo $reqOrd['quantite'] ?></td>
							<td> <?php echo $reqOrd['date'] ?></td>
							<td> <?php echo $reqOrd['fournisseurID'] ?></td>
						
							<td> <?php echo $reqOrd['idCommande'] ?></td>
						</tr>
						<?php
						$i++;
					}
					?>
				</table>
				<?php
					}
				
			}
		}
	?>
	
	<form method='POST' action='Fournisseurs.php' name='action'>
	<select name='choix' id='choix' >
	<option >Chercher ...</option>
	<option value='chercherProduit'>chercher un produit particulier </option>
	
	<option value='livrerProduit'>livrer un produit</option>
	<option value='requette'>Lancer une requette</option>
	</select><br>
	
	<input value='valider' type='submit' name='valider'>
	</form>
	<?php
		if(isset($_POST['valider'])) {
			if(isset($_POST['choix'])){
				if($_POST['choix']=='chercherProduit'){
			?>
			<form action='fournAction.php' method='POST' name='confirmer'>
				<label>
					rechercher par nom
					<input name='nom' >
					
				</label><br>
				<label>
					rechercher par description
					<input  name='description' >
					
				</label><br>
				<label> 
					rechercher par id
					<input  name='id' >
				
					
				</label><br>
				<br><input type='submit' value='confirmer' name='confirmer'>
			</form>
				
	<?php
		}
		elseif( $_POST['choix']=='livrerProduit'){
			
			header('Location:fournAction.php?action=livrerProduit');
			exit();
			
		}
		elseif($_POST['choix']=='requette'){
			
			?>
				<form action='fournAction.php' method='POST'>
					<label>
						nom du produit
						<input name='nom' ><br>
						</label>
						<br>
					<label>
						quantite
						<input name='quantite'>
					</label><br>
					<label>
						date de livraison (Format YYYY-MM-DD )
						<input name='dateLivraison'> 
					</label><br>
					
						<input type='submit' value='envoyer' name='envoyer'>
				</form>
			
			<?php
			
		}
			}
		}			
			
			
		
	?>
	

</body>
</html>