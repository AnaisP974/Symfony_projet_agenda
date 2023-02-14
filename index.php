<?php 
$title = "Liste des contacts"; 
$description = "Il faut mettre une description de 150 caractères, Donc ici, il y aura notre agenda digital et vous êtes ici sur la page d'accueil du site";
?>

<?php require __DIR__ . "./partials/head.php"; ?>
<!-- __DIR__ . permet de définir le point d'encrage, le dossier dans lequel ce trouve le fichier ATTENTION il faut bienmettre le / -->
<header>
    <?php require __DIR__ . "./partials/nav.php"; ?>
</header>


<main class="container">

    <h1 class="text-center my-3 display-5">Liste des contacts</h1>

    <div class="d-flex justify-content-end align-items-center">
        <a href="create.php" class="btn btn-primary shadow"><i class="fa-solid fa-plus"></i> Nouveau contact</a>
    </div>
</main>



<?php require __DIR__ . "./partials/footer.php" ?>

<?php require __DIR__ . "./partials/foot.php"; ?>