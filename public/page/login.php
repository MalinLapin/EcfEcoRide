<?php 
$pageCss = 'login';
require_once '../template/header.php'; ?>

<main class="mainContent">
    <section class="presentationSection">
        <h2 class="montserratBold titleColor">EcoRide</h2>
    </section>
    <div class="card">
        <h3 class="robotoBold">Connexion</h3>
        <form method="POST" action="">
            <div class="form-group">
                <label for="pseudo" class="robotoRegular">Pseudo</label>
                <input type="text" id="pseudo" name="pseudo" placeholder="Votre pseudo" required>
            </div>
            <div class="form-group">
                <label for="password" class="robotoRegular">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Mot de passe" required>
            </div>
            <button type="submit" class="robotoRegular">Se connecter</button>
            <div class="message robotoRegular">Pas encore inscrit ? <a href="./register.php">Créer un compte</a></div>
        </form>
    </div>
</main>

<?php require_once '../template/footer.php'; ?>