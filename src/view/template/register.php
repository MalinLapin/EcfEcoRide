<section class='register'>
    <div class="presentationSection">
            <h2 class="montserratBold titleColor">EcoRide,<br>votre covoiturage <br> éco-responsable.</h2>
        </div>
    <div class="card robotoRegular">
        <h3 class="robotoBold">Inscription</h3>
        <form method="POST" action="/register">
            <div class="form-group">
                <label for="pseudo"></i>Pseudo :</label>
                <input type="text" id="pseudo" name="pseudo" placeholder="Votre pseudo" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Votre mail" required>                
            </div>
            <div class="form-group">
                <label for="password">Mot de passe sécuriser :</label>
                <input type="password" id="password" name="password" placeholder="Votre mot de passe" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirmer :</label>
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirmer le mot de passe"
                    required>
            </div>
            <div class='errorsList'>
                    <ul>
                        <?php if(isset($errors) && $errors){
                        foreach ($errors as $error):?>
                            <li class='error robotoBold'><?=$error?></li>                                    
                        <?php endforeach;}?>
                    </ul>    
                </div>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <button type="submit" class='registerBtn'>Monter à bord !</button>
            <div class="message">Déjà un comtpe ? <a href="/login">Connexion</a></div>
        </form>
    </div>
</section>