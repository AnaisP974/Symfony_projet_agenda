<?php
session_start();
//--------------------------LOGIQUE ----------------------------------

//Si le serveur confirme que les données ont été envoyées via la méthode "POST",
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /*
        *----------------------------------
        *      1) Faire De la cybersécurité :)
        *----------------------------------
        */

    require __DIR__ . "/functions/security.php";




    /* Protéger le serveur contre la faille de type CSRF : https://www.vaadata.com/blog/fr/attaques-csrf-principes-impacts-exploitations-bonnes-pratiques-securite/
    * Si le token de sécurité provenant du formulaire n'est pas le même que celui généré par le système,*/
    if (csrf_middleware($_POST['create_form_csrf_token'], $_SESSION['create_form_csrf_token'])) {
        // On redirige automatiquement l'utilisateur vers la page de laquelle proviennent les informations
        // Puis, on arrête l'exécution du script
        return header("Location: " . $_SERVER['HTTP_REFERER']);

        //ou sans le "return" => die() ; OU => exit() ;
    }

    unset($_SESSION['create_form_csrf_token']);



    // HONEYPOT : https://nordvpn.com/fr/blog/honeypot-informatique/
    // permet de protéger le serveur contre les robots spameurs, 
    // si le pot de miel a décter un robot
    if (honeypot_middleware($_POST['create_form_honeypot'])) {

        /* on redirige automatiquement l'utilisateur vers la page de laquelle proviennent les infos,
        * puis on arrêtera l'éxécution du script
        */
        return header("Location:" . $_SERVER['HTTP_REFERER']);
    }

    //  var_dump("On peut continuer"); die();

    //Protegons le serveur contre la faille de type XSS => injection de code HTML ou JavaScript dans le formulaire
    $post_clean = xss_protection($_POST);





    /*
    *----------------------------------------------------------------
    *
    *       2) Gestion de la validation des données du formulaire
    *
    *-----------------------------------------------------------------
    */

    require __DIR__ . "/functions/validator.php";

    $errors = [];

    //Pour le prénom : si elle existe..
    if (isset($post_clean['first_name'])) {
        // ..si elle est vide
        if (is_blank($post_clean['first_name'])) {
            $errors['first_name'] = "Le prénom est obligatoire.";
        }

        if (length_is_greater_than($post_clean['first_name'], 255)) {
            $errors['first_name'] = "Le prénom ne doit pas dépasser 255 caractères.";
        }
    }


    //Pour le nom : si elle existe..
    if (isset($post_clean['last_name'])) {
        // ..si elle est vide
        if (is_blank($post_clean['last_name'])) {
            $errors['last_name'] = "Le nom est obligatoire.";
        }

        if (length_is_greater_than($post_clean['last_name'], 255)) {
            $errors['last_name'] = "Le nom ne doit pas dépasser 255 caractères.";
        }
    }
    //Pour l'email : si elle existe..
    if (isset($post_clean['email'])) {
        // ..si elle est vide
        if (is_blank($post_clean['email'])) {
            $errors['email'] = "L'email est obligatoire.";
        } elseif (length_is_greater_than($post_clean['email'], 255)) {
            $errors['email'] = "L'email ne doit pas dépasser 255 caractères.";
        } elseif (length_is_less_than($post_clean['email'], 5)) {
            $errors['email'] = "L'email ne doit avoir une longueur minimale de 5 caractères.";
        } elseif (is_invalid_email($post_clean['email'])) {
            $errors['email'] = "Veuillez entrer un email valide.";
        } elseif (is_already_exist_on_create($post_clean['email'], "contact", "email")) {
            $errors['email'] = "Email déjà utilisé pour un contact.";
        }
    }

    if (isset($post_clean['age'])) {
        if (is_not_blank($post_clean['age'])) {

            if (is_not_a_number($post_clean['age'])) {
                $errors['age'] = "L'age doit être un nombre.";
            }

            if (is_not_between($post_clean['age'], 3, 130)) {
                $errors['age'] = "L'age doit être compris entre 3 et 130 ans.";
            }
        }
    }

    if (isset($post_clean['phone'])) {
        if (is_blank($post_clean['phone'])) {
            $errors['phone'] = "Le numéro de téléphone est obligatoire.";
        } elseif (is_invalid_phone($post_clean['phone'])) {
            $errors['phone'] = "Veuillez entrer un numéro de téléphone valide.";
        } else if (is_already_exist_on_create($post_clean['phone'], "contact", "phone")) {
            $errors['phone'] = "Ce numéro de téléphone appartient déjà à l'un de vos contacts.";
        }
    }

    if (isset($post_clean['comment'])) {
        if (is_not_blank($post_clean['comment'])) {

            if (length_is_greater_than($post_clean['comment'], 4000)) {
                $errors['comment'] = "Votre commantaire est trop long, il doit contenir 4000 caractères maximum.";
            }
        }
    }


    /*
    *----------------------------------------------------------------
    *
    *       3) Gestion de l'affichage des messages d'erreur
    *
    *-----------------------------------------------------------------
    */

    //Si le tableau d'erreur contient au moins 1 erreur
    if (count($errors) > 0) {
        // die('hello');
        //sauvegarde des messages d'erreur en session
        $_SESSION['create_form_errors'] = $errors;

        //sauvegarde des données du formulaire en session
        $_SESSION['create_form_old_values'] = $post_clean;


        //faire une redirection vers la page d'où viennent les infos
        // puis, on arrête l'éxécution du script

        return header("Location: " . $_SERVER["HTTP_REFERER"]);
        // grâce à =>(header("Location: ") php sait vers où il doit faire la redirection
    }

    //s'il n'y a pas d'erreur on peut éffectuer la requ^te qui permettra l'envoi des infos vers la base de données
    //Etablir la connexion avec la base de données => Par un appel du manager
    require __DIR__ . "/functions/manager.php";

    //effectuer la requête d'insertion des données dans la table 'contact'
    create_contact([
        "first_name" => $post_clean['first_name'],
        "last_name"  => $post_clean['last_name'],
        "email"      => $post_clean['email'],
        "age"        => $post_clean['age'],
        "phone"      => $post_clean['phone'],
        "comment"    => $post_clean['comment'],
    ]);

    // Générer un message à afficher à l'utilisateur pour lui consfirmer que son nouveau contact a bien été ajouté à la liste
    $_SESSION['success'] = "Le contact a été ajouté à la liste avec succès.";

    //Faire la redirection vers la page d'accueil
    return header("Location: index.php");
    //Arrêter l'axécution du script

}

// création d'un token pour chaque formulaire
// on crée une chaîne de caractaire aléatoire côté serveur et on l'enregistre dans la session.. 
$_SESSION['create_form_csrf_token'] = bin2hex(random_bytes(40));

?>

<?php //--------------------------VIEW ----------------------------------
$title = "Nouveau contact";
$description = "Page qui permet l'ajout d'un nouveau contact à la liste via un formulaire à compléter.";
$keyword = "Agenda, Contacts, php, php8, Projet, DWWM, Contact, Répertoire";
?>

<?php require __DIR__ . "./partials/head.php"; ?>
<!-- __DIR__ . permet de définir le point d'encrage, le dossier dans lequel ce trouve le fichier ATTENTION il faut bienmettre le / -->
<header>
    <?php require __DIR__ . "./partials/nav.php"; ?>
</header>


<main class="container">

    <h1 class="text-center my-3 display-5">Nouveau contact</h1>

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-lg-7 mx-auto p-4 shadow bg-white">

                <?php if (isset($_SESSION['create_form_errors']) && !empty($_SESSION['create_form_errors'])) : ?>
                    <div class="alert alert-danger" role="alert">
                        <ul>
                            <?php foreach ($_SESSION['create_form_errors'] as $error) : ?>
                                <li><?= $error ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <!-- unset — Détruit une variable -->
                    <?php unset($_SESSION['create_form_errors']); ?>
                <?php endif ?>

                <form action="" method="post">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="create_form_first_name">Prénom</label>
                                <!-- form_control permet de prendre 100% de place disponible -->
                                <input type="text" name="first_name" id="create_form_first_name" class="form-control" value="<?= isset($_SESSION['create_form_old_values']['first_name']) ? $_SESSION['create_form_old_values']['first_name'] : '';
                                                                                                                                unset($_SESSION['create_form_old_values']['first_name']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="create_form_last_name">Nom</label>
                                <input type="text" name="last_name" id="create_form_last_name" class="form-control" value="<?= isset($_SESSION['create_form_old_values']['last_name']) ? $_SESSION['create_form_old_values']['last_name'] : '';
                                                                                                                            unset($_SESSION['create_form_old_values']['last_name']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row bg-ligth">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="create_form_email">Email</label>
                                <!-- form_control permet de prendre 100% de place disponible -->
                                <input type="email" name="email" id="create_form_email" class="form-control" value="<?= isset($_SESSION['create_form_old_values']['email']) ? $_SESSION['create_form_old_values']['email'] : '';
                                                                                                                    unset($_SESSION['create_form_old_values']['email']); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="create_form_age">Âge</label>
                                <input type="number" name="age" id="create_form_age" class="form-control" value="<?= isset($_SESSION['create_form_old_values']['age']) ? $_SESSION['create_form_old_values']['age'] : '';
                                                                                                                    unset($_SESSION['create_form_old_values']['age']); ?>">
                            </div>
                        </div>
                    </div>


                    <div class="mb-3">
                        <label for="create_form_phone">Numéro de téléphone</label>
                        <input type="tel" name="phone" id="create_form_phone" class="form-control" value="<?= isset($_SESSION['create_form_old_values']['phone']) ? $_SESSION['create_form_old_values']['phone'] : '';
                                                                                                            unset($_SESSION['create_form_old_values']['phone']); ?>">
                    </div>


                    <div class="mb-3">
                        <label for="create_form_comment">Commentaire</label>
                        <textarea name="comment" id="create_form_comment" class="form-control" rows="4"><?= isset($_SESSION['create_form_old_values']['comment']) ? $_SESSION['create_form_old_values']['comment'] : '';
                                                                                                        unset($_SESSION['create_form_old_values']['comment']); ?></textarea>
                    </div>

                    <div class="mb-3 d-none">
                        <input type="hidden" name="create_form_csrf_token" value="<?= $_SESSION['create_form_csrf_token'] ?>">
                    </div>

                    <div class="mb-3 d-none">
                        <!-- value doit resté vide car les robots ont tendences à tout remplir. Vu que se champs n'est pas visible pour un humain, cette valeur doit nous être retournée vide. Alors que les robots vont la remplir par défaut. Donc pour savoir s'il s'agit d'un robot, on va juste regardé si ça a été rempli ou non -->
                        <input type="hidden" name="create_form_honeypot" value="">
                    </div>

                    <div class="mb-3">
                        <input type="submit" value="Ajouter" class="btn btn-primary shadow" formnovalidate>
                    </div>

                </form>
            </div>
        </div>

    </div>


</main>



<?php require __DIR__ . "./partials/footer.php" ?>

<?php require __DIR__ . "./partials/foot.php"; ?>