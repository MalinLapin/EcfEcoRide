

<main class="mainContent">
    <section class="presentationSection">
        <h1 class="montserratBold titleColor">EcoRide, 
            <?php if(isset($title)&&$title){
                echo $title;
            }else{
                echo 'votre covoiturage éco-responsable.';
            } ?></h1>
    </section>
    <div class="card">
        <h3 class="robotoBold">Connexion</h3>
        <form method="POST" action="../../src/controller/AuthController.php/login">
            <div class="form-group">
                <label for="pseudo" class="robotoRegular">Pseudo</label>
                <input type="text" id="pseudo" name="pseudo" placeholder="Votre pseudo" required>
            </div>
            <div class="form-group">
                <label for="password" class="robotoRegular">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Mot de passe" required>
            </div>
            <button type="submit" class="robotoRegular">Se connecter</button>
            <div class="message robotoRegular">Pas encore inscrit ? <a href="/register">Créer un compte</a></div>
        </form>
    </div>
</main>

