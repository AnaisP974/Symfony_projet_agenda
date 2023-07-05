<?php

//Le rôle du manager est d'effectuer des requêtes et intéragir avec la page PHP

/**
 * Cette fonction permet de se connecter à la base de données et d'y insérrer des données dans un certain ordre.
 *
 * Elle permet de lancer une requête vers la base de données pour injecter les données d'un nv contact
 * 
 * @param array $data
 * @return void
 */
function create_contact(array $data): void
{
    //Etablir la connexion avec la base de données :
    require __DIR__ . '/../db/connexion.php';

    //effectuer la requête d'insertion des données dans la table 'contact'
    //INSERT INTO contact => "je veux insérer dans la table "contact" .. le first_name, le last_name etc
    //Valeur qui vont être envoyé ; syntaxe PDO => :first_name, :last_name, => "je me prépare à envoyer une valeur, on ne les passe pas directement pour éviter les failles de sécurité"
    $req = $db->prepare("INSERT INTO contact (first_name, last_name, email, age, phone, comment, created_at, updated_at) VALUES (:first_name, :last_name, :email, :age, :phone, :comment, now(), now() ) ");
    // now() est une fonction SQL

    // puis on passe les valeurs attendues (les vraies valeurs) 
    $req->bindValue(":first_name", $data['first_name']);
    $req->bindValue(":last_name", $data['last_name']);
    $req->bindValue(":email", $data['email']);
    $req->bindValue(":age", $data['age'] ? $data['age'] : NULL);
    $req->bindValue(":phone", $data['phone']);
    $req->bindValue(":comment", $data['comment']);

    //Exécuter la requête
    $req->execute();

    //fermer la connexion établie avec la base de données
    $req->closeCursor();
}


/**
 * Cette fonction me permets de récupérer tous les contacts
 *
 * @return array
 */
function find_all_contacts() : array {

    // connexion à la base de données
    require __DIR__ . "/../db/connexion.php";
    //préparer les données
    $req = $db->prepare("SELECT * FROM contact");

    $req->execute();

    //fetchAll() => car on souhaite récupérer plusieurs enregistrements (!= fetch()) et on l'enregistre dans une variable $data
    $data = $req->fetchAll();

    $req->closeCursor();
    
    return $data;
}

/**
 * Cette fonction permet de récupérer un contact de la table "contact"
 *
 * @param integer $id
 * @return array||false
 */
function contact_find_by(int $id) : array|false {
    //établir une connexion avec la base de données
require __DIR__ . "/../db/connexion.php";

    //faire la requête:
    $req = $db->prepare("SELECT * FROM contact WHERE id=:id LIMIT 1");
    //remplacer l'id par la vraie valeur
    $req->bindValue(":id", $id);
    // exécuter la requête
    $req->execute();
    //récupérer l'enregistrement sélectionné
    $data = $req->fetch();
    //clôturer la requête
    $req->closeCursor();

    return $data;
}


/**
 * Cette fonction permet de modifier les infos enregistrées dans un contact
 *
 * @param array $data
 * @return void
 */
function edit_contact(array $data) : void {
    require __DIR__ . "/../db/connexion.php";

    $req = $db->prepare("UPDATE contact SET first_name=:first_name, last_name=:last_name, email=:email, age=:age, phone=:phone, comment=:comment, updated_at=now() WHERE id=:id");

    $req->bindValue(":first_name", $data['first_name']);
    $req->bindValue(":last_name",  $data['last_name']);
    $req->bindValue(":email",      $data['email']);
    $req->bindValue(":age",        $data['age'] ? $data['age'] : NULL);
    $req->bindValue(":phone",      $data['phone']);
    $req->bindValue(":comment",    $data['comment']);
    $req->bindValue(":id",         $data['id']);

    // Exécutons la requête
    $req->execute();

    // Fermons le curseur (Non obligatoire)
    $req->closeCursor();
}


function delete_contact(int $id) : void
{
    require __DIR__ . "/../db/connexion.php";

    $req = $db->prepare("DELETE FROM contact WHERE id=:id");

    $req->bindValue(":id", $id);

    $req->execute();

    $req->closeCursor();
}
