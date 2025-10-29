<section class='login'>  
    <div class="presentationSection">
        <h2 class="montserratBold titleColor">EcoRide,<br>votre covoiturage <br> éco-responsable.</h2>
    </div>
    <div class="card">
        <h3 class="robotoBold">Connexion</h3>
        <form method="POST" action="/login">
            <div class="form-group">
                <label for="email" class="robotoRegular">Email</label>
                <input type="email" id="email" name="email" placeholder="Votre email" required>
            </div>
            <div class="form-group">
                <label for="password" class="robotoRegular">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Mot de passe" required>
            </div>
            <?php if (!empty($message)):?>
            <div class =errorMessage>
                <p class="robotoBold"><?=$message?></p>
            </div>
            <?php endif; ?>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <button type="submit" class="robotoRegular loginBtn">Se connecter</button>
            <div class="message robotoRegular">Pas encore inscrit ? <a href="/register">Créer un compte</a></div>
        </form>
    </div>
</section> 


