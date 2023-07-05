<?php
session_start();

// ------------ POUR LE READ ------------
//j'appelle le manager
require __DIR__ . "/functions/manager.php";
//j'enregistre dans une variable tous les contacts
$contacts = find_all_contacts();

$_SESSION['delete_contact_csrf_token'] = bin2hex(random_bytes(40));


?>

<?php
// ------------ ------------ VIEW ------------ ------------

$title = "Liste des contacts";
$description = "Il faut mettre une description de 150 caractères, Donc ici, il y aura notre agenda digital et vous êtes ici sur la page d'accueil du site";
?>

<?php require __DIR__ . "/partials/head.php"; ?>
<!-- __DIR__ . permet de définir le point d'encrage, le dossier dans lequel ce trouve le fichier 
ATTENTION : il faut bien mettre le / -->
<header>
    <?php require __DIR__ . "/partials/nav.php"; ?>
</header>


<main class="container">

    <h1 class="text-center my-3 display-5">Liste des contacts</h1>

    <!---------------    Afficher une alerte : Action réussi !   --------------->
    <?php if (isset($_SESSION['success']) && !empty($_SESSION["success"])) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; ?>
            <!-- bouton réprésenté par une croix pour le retrait de l'alerte après création d'un nouveau contact -->
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <?php unset($_SESSION['success']); ?>
    <?php endif ?>

    <!-- Bouton creer un nv contact -->
    <div class="d-flex justify-content-end align-items-center" data-aos="fade-left" data-aos-duration="3000">
        <a href="create.php" class="btn btn-primary shadow"><i class="fa-solid fa-plus"></i> Nouveau contact</a>
    </div>

    <div class="container">
        <div class="d-flex flex-column justify-content-center align-items-center">
            <?php foreach ($contacts as $contact) : ?>
                <div class="my-card my-3 shadow p-4" data-aos="fade-up-right" data-aos-duration="3000">
                    <p> <strong> Prénom : </strong> <?= htmlspecialchars($contact['first_name']); ?> </p>
                    <p> <strong> Nom : </strong> <?= htmlspecialchars($contact['last_name']); ?> </p>
                    <p> <strong> Email : </strong> <?= htmlspecialchars($contact['email']); ?> </p>
                    <p> <strong> Téléphone : </strong> <?= htmlspecialchars($contact['phone']); ?> </p>
                    <hr>
                    <!-- icon trigger modal -->
                    <a 
                        href="#" 
                        class="text-dark mx-2" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modal_<?= htmlspecialchars($contact['id']); ?>" 
                        title="Voir le détail"
                    >
                        <i class="fa-solid fa-eye"></i>
                    </a>

                       
                    <a 
                        href="edit.php?contact_id=<?=htmlspecialchars($contact['id']); ?>" 
                        class="text-dark mx-2" 
                        title="Modifier le contact" 
                    >
                        <i class="fa-regular fa-pen-to-square"></i>
                    </a>

                    <a class="text-danger" href="#" onclick="event.preventDefault(); confirm('Confirmer la suppression?') && document.querySelector('#delete_' + <?= htmlspecialchars($contact['id']) ?>).submit();" title="Supprimer ce contact"><i class="fa-solid fa-trash-can"></i></a>
                    <form id="delete_<?= htmlspecialchars($contact['id']) ?>" action="delete.php" method="post">
                            <input type="hidden" name="delete_contact_csrf_token" value="<?= $_SESSION['delete_contact_csrf_token'] ?>">
                            <input type="hidden" name="contact_id" value="<?= htmlspecialchars($contact['id']) ?>">
                            <!-- <input type="submit" class="btn btn-sm btn-danger" value="Supprimer"> -->
                        </form>


                    <!-- Modal -->
                    <div class="modal fade" id="modal_<?= htmlspecialchars($contact['id']); ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                                        <?= htmlspecialchars($contact['first_name']); ?> 
                                        <?= htmlspecialchars($contact['last_name']); ?>
                                    </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <?php if((isset($contact['age'])) && !empty($contact['age'])) :  ?>
                    <p> <strong> Age : </strong> <?= htmlspecialchars($contact['age']); ?> </p>

                                    <?php else : ?>
                                        <p><em>Age non renseigné.</em></p>
                                    <?php endif ?>

                                    <?php if((isset($contact['comment'])) && !empty($contact['comment'])) :  ?>
                    <p> <strong> Commentaire : </strong> <?= htmlspecialchars($contact['comment']); ?> </p>

                                    <?php else : ?>
                                        <p><em>Commentaire non renseigné.</em></p>
                                    <?php endif ?>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>



</main>



<?php require __DIR__ . "/partials/footer.php" ?>

<?php require __DIR__ . "/partials/foot.php"; ?>