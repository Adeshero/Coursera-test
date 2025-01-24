<?php
session_start();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Fournisseur <?php echo htmlspecialchars($_SESSION['nom']); ?></title>
</head>
<body>

<?php
include 'connex.php';

if (isset($_POST['confirmer'])) {
    $query = 'SELECT id, nom, description, prix, quantite, 
                     DATE_FORMAT(dateAjout, \'%d/%m/%Y à %Hh%imin%Ss\') AS date, fournisseurID 
              FROM produits 
              WHERE fournisseurID = ?';

    $param = null; // Initialisation du paramètre

    if (isset($_POST['nom'])) {
        $query .= ' AND nom LIKE ?';
        $param = '%' . htmlspecialchars($_POST['nom']) . '%';
    } elseif (isset($_POST['id'])) {
        $query .= ' AND id = ?';
        $param = htmlspecialchars($_POST['id']);
    } elseif (isset($_POST['description'])) {
        $query .= ' AND description LIKE ?';
        $param = '%' . htmlspecialchars($_POST['description']) . '%';
    }

    $req = $db->prepare($query);
    $req->execute(array($_SESSION['id'], $param));

    if ($req->rowCount() <= 0) {
        echo "Aucun produit trouvé avec ce nom.";
    } else {
        echo "Résultat des recherches :";
        ?>
        <table>
            <tr>
                <th scope="col">N°</th>
                <th scope="col">Nom</th>
                <th scope="col">Description</th>
                <th scope="col">Prix</th>
                <th scope="col">Quantité</th>
                <th scope="col">Date</th>
            </tr>
            <?php
            $i = 1;
            while ($reqOrd = $req->fetch()) {
                ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo htmlspecialchars($reqOrd['nom']); ?></td>
                    <td><?php echo htmlspecialchars($reqOrd['description']); ?></td>
                    <td><?php echo htmlspecialchars($reqOrd['prix']); ?></td>
                    <td><?php echo htmlspecialchars($reqOrd['quantite']); ?></td>
                    <td><?php echo htmlspecialchars($reqOrd['date']); ?></td>
                </tr>
                <?php
                $i++;
            }
            ?>
        </table>
        <?php
    }
} elseif (isset($_POST['envoyer'])) {
    $req = $db->prepare('INSERT INTO attconfirmation(nom, quantite, dateLivraison, fournisseurID) VALUES(?, ?, ?, ?)');
    $req->execute(array(
        htmlspecialchars($_POST['nom']),
        htmlspecialchars($_POST['quantite']),
        htmlspecialchars($_POST['dateLivraison']),
        $_SESSION['id']
    ));

    echo "Insertion faite";
} elseif (isset($_GET['action']) || $_SESSION['choix'] == 'livrer un produit') {
    $_SESSION['choix'] = 'livrer un produit';
    $req = $db->prepare('SELECT id, nom, description, quantite, DATE_FORMAT(dateLimite, \'%d/%m/%Y à %Hh%imin%Ss\') AS date, fournisseurID 
                         FROM demandes 
                         WHERE (fournisseurID = ? OR fournisseurID IS NULL) AND DATEDIFF(dateLimite, SYSDATE()) >= 0 
                         ORDER BY dateLimite DESC');
    $req->execute(array($_SESSION['id']));

    if ($req->rowCount() <= 0) {
        echo "Aucune demande pour l'instant.";
    } else {
        ?>
        <table>
            <tr>
                <th scope="col">N°</th>
                <th scope="col">Id°</th>
                <th scope="col">Nom</th>
                <th scope="col">Description</th>
                <th scope="col">Quantité</th>
                <th scope="col">Date Limite</th>
                <th scope="col">Fournisseur</th>
            </tr>
            <?php
            $i = 1;
            while ($reqOrd = $req->fetch()) {
                ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo htmlspecialchars($reqOrd['id']); ?></td>
                    <td><?php echo htmlspecialchars($reqOrd['nom']); ?></td>
                    <td><?php echo htmlspecialchars($reqOrd['description']); ?></td>
                    <td><?php echo htmlspecialchars($reqOrd['quantite']); ?></td>
                    <td><?php echo htmlspecialchars($reqOrd['date']); ?></td>
                    <td><?php echo htmlspecialchars($reqOrd['fournisseurID']); ?></td>
                </tr>
                <?php
                $i++;
            }
            ?>
        </table>
        <?php
    }
    ?>
    <form action='fournAction.php' method='POST'>
        <label>
            Entrer l'identifiant de la commande :
            <input name='id' id='id'>
        </label><br>
        <label>
            Entrer le nom du produit :
            <input name='name' id='name'>
        </label><br>
        <label>
            Entrer la quantité fournie :
            <input name='quantite' id='quantite'>
        </label><br>
        <input type='submit' value='livrer' name='livrer' id='livrer'> 
    </form>

    <?php
    if (isset($_POST['livrer'])) {
        $req = $db->prepare('SELECT id FROM demandes WHERE nom = ?');
        $req->execute([htmlspecialchars($_POST['name'])]);
        $reqOrd = $req->fetch();

        if ($reqOrd) {
            // Supprimer d'abord les références dans attconfirmation
            $reqDeleteAttConfirmation = $db->prepare('DELETE FROM attconfirmation WHERE idCommande = ?');
            $reqDeleteAttConfirmation->execute(array($reqOrd['id']));

            // Maintenant, vous pouvez supprimer la demande
            $reqDeleteDemande = $db->prepare('DELETE FROM demandes WHERE id = ?');
            $reqDeleteDemande->execute(array($reqOrd['id']));

            // Ajouter la livraison à la table des livraisons en attente de confirmation
            $reqInsertLivraison = $db->prepare('INSERT INTO attconfirmation (nom, quantite, dateLivraison, fournisseurID, idCommande) VALUES (?, ?, SYSDATE(), ?, ?)');
            $reqInsertLivraison->execute(array(
                htmlspecialchars($_POST['name']),
                htmlspecialchars($_POST['quantite']),
                $_SESSION['id'],
                $reqOrd['id']
            ));

            echo "Produit livré en attente de confirmation.";
        } else {
            echo "Le produit est inexistant.";
        }
    }
}
?>

</body>
</html>