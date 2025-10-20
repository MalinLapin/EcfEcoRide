<section class="searchRidesharing robotoBold">
        <h2 class="montserratBold titleColor">Trouver votre covoiturage</h2>
        <div class="searchContent">
            <form method="POST" action="/search" class='searchForm'>
                <div class='departureCity'>
                    <label for="departureCity">Ville de départ :</label>
                    <input type="text" name="departureCity" id="departureCity" placeholder="Ville" required>
                </div>
                <div class="departureAddress">
                    <label for="departureAddress">Adresse de départ :</label>
                    <input type="text" name="departureAddress" id="departureAddress" placeholder="CP, rue">
                </div>
                <div class='arrivalCity'>
                    <label for="arrivalCity">Ville d'arriver :</label>
                    <input type="text" name="arrivalCity" id="arrivalCity" placeholder="Ville" required>
                </div>
                <div class="arrivalAddress">
                    <label for="arrivalAddress">Adresse d'arriver :</label>
                    <input type="text" name="arrivalAddress" id="arrivalAddress" placeholder="CP, rue">
                </div>
                <div class="departureDate">
                    <label for="departureDate">Date/heure :</label>
                    <input type="datetime-local" name="departureDate" id="departureDate" required>
                </div>
                <div class="nbSeats">
                    <label for="nbSeats">Place :</label>
                    <input type="number" name="nbSeats" id="nbSeats" value="1" max='6'>
                </div>
                <div class="pricePerSeat">
                    <label for="pricePerSeat">Prix de la place</label>
                    <input type="number" name="pricePerSeat" id="pricePerSeat">
                </div>
                <div class="gradeDriver">
                    <label for="gradeDriver">Note du conducteur</label>
                    <input type="number" name="gradeDriver" id="gradeDriver" min='1' max='5'>
                </div>
                <div class="energyType">
                    <label for="energyType">Trajet électrique uniquement ?</label>
                    <select name="energyType" id="energyType">
                        <option value="electric">oui</option>
                        <option value="">non</option>
                    </select>
                </div>
                <div class='errorsList'>
                    <ul>
                        <?php if(isset($errors) && $errors){
                        foreach ($errors as $error):?>
                            <li class='error robotoBold'><?=$error?></li>                                    
                        <?php endforeach;}?>
                    </ul>    
                </div>

                <div>
                    <input type="hidden" name="status" value="pending">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <button type="submit" class="btnSearch">C'est partie!</button>
                </div>
            </form>
        </div>
</section>