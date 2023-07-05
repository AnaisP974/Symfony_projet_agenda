<?php
session_start();


    if ( $_SERVER['REQUEST_METHOD'] !== "POST" ) 
    {
        return header("Location: index.php");
    }
    
    
    require __DIR__  . "/functions/security.php";
    
    if(csrf_middleware($_POST['delete_contact_csrf_token'], $_SESSION['delete_contact_csrf_token']))
    {
        return header("Location: index.php");
    }
    
    unset($_SESSION['delete_contact_csrf_token']);
    
    
    $post_clean = xss_protection($_POST);
    
    if( !isset($post_clean['contact_id']) || empty($post_clean['contact_id']) )
    {
        return header("Location: index.php");
    }
    
    $contact_id_converted = (int) $post_clean['contact_id'];
    
    
    require __DIR__ . "/functions/manager.php";
    
    $contact = contact_find_by($contact_id_converted);
    
    if ( ! $contact ) 
    {
        return header("Location: index.php");
    }


    delete_contact($contact['id']);


    // Générons un message à afficher à l'utilisateur pour lui expliquer que son nouveau contact
    // a bien été ajouté à la liste.
    $_SESSION['success'] = "Le contact a été supprimé!.";

    // Effectuons une redirection vers la page d'accueil
    // Puis, arrêtons l'exécution du script.
    return header("Location: index.php");
