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
                <label for="pseudo"><i class="fas fa-envelope form-icon robotoRegular"></i>Pseudo :</label>
                <input type="text" id="pseudo" name="pseudo" placeholder="Votre pseudo" required>
            </div>
            <div class="form-group">
                <label for="pseudo"><i class="fas fa-envelope form-icon robotoRegular"></i>Email:</label>
                <input type="text" id="pseudo" name="pseudo" placeholder="Votre mail" required>
            </div>
            <div class="form-group">
                <label for="pseudo"><i class="fas fa-envelope form-icon robotoRegular"></i>Mot de passe sécuriser :</label>
                <input type="text" id="pseudo" name="pseudo" placeholder="Votre mot de passe" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock form-icon robotoRegular"></i>Confirmer :</label>
                <input type="password" id="password" name="password" placeholder="Confirmer le mot de passe" required>
            </div>
            <button type="submit"><i class="fas fa-sign-in-alt robotoRegular"></i>Monter à bord !</button>
            <div class="message robotoRegular">Déjà un comtpe ? <a href="./login.php">Connexion</a></div>
        </form>
    </div>   
</main>

<?php require_once '../template/footer.php'; ?>