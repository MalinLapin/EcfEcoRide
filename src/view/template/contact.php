<section class='contactSection'>
    <div class="presentationSection">
            <h2 class="montserratBold titleColor">EcoRide,<br>votre covoiturage <br> éco-responsable.</h2>
        </div>
    <div class="card robotoRegular">
        <h3 class="robotoBold">Contact</h3>
        <form method="POST" action="/contact">
            <div class="form-group">
                <label for="emailSender"></i>Votre adresse email:</label>
                <input type="email" id="emailSender" name="emailSender" placeholder="Votre email" required>
            </div>
            <div class="form-group">
                <label for="subject">Objet :</label>
                <input type="text" id="subject" name="subject" placeholder="Raison du contact" required>                
            </div>
            <div class="form-group">
                <label for="content">Votre message :</label>
                <textarea id="content" name="content" placeholder="Votre message" rows="8" required></textarea>
            </div>
            
            <div class='errorsList'>
                <ul>
                    <?php if(!empty($errors)){
                    foreach ($errors as $error):?>
                        <li class='error robotoBold'><?=$error?></li>                                    
                    <?php endforeach;}?>
                </ul>    
            </div>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <button type="submit" class='contactBtn'>Envoyer</button>
        </form>

        <?php if(!empty($flash)):?>
                <div class='flashInfo'>
                    <p class='robotoBold'><?=$flash['message']?></p>
                </div>
        <?php endif; ?>
    </div>

</section>