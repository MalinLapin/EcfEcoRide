<?php 
$pageCss = 'register';
require_once '../template/header.php'; ?>

<main class="mainContent">
    <section class="presentationSection">
        <h2 class="montserratBold titleColor">EcoRide</h2>
    </section>
    <div class="card">
        <h3 class="robotoBold">Inscription</h3>
        <form method="POST" action="#">
            <div class="form-group">
                <label for="pseudo" class="robotoRegular"></i>Pseudo :</label>
                <input type="text" id="pseudo" name="pseudo" placeholder="Votre pseudo" required>
            </div>
            <div class="form-group">
                <label for="pseudo" class="robotoRegular">Email:</label>
                <input type="text" id="pseudo" name="pseudo" placeholder="Votre mail" required>
            </div>
            <div class="form-group">
                <label for="pseudo" class="robotoRegular">Mot de passe sécuriser :</label>
                <input type="text" id="pseudo" name="pseudo" placeholder="Votre mot de passe" required>
            </div>
            <div class="form-group">
                <label for="password" class="robotoRegular">Confirmer :</label>
                <input type="password" id="password" name="password" placeholder="Confirmer le mot de passe" required>
            </div>
            <button type="submit" class="robotoRegular">Monter à bord !</button>
            <div class="message robotoRegular">Déjà un comtpe ? <a href="./login.php">Connexion</a></div>
        </form>
    </div>   
</main>

<?php require_once '../template/footer.php'; ?>