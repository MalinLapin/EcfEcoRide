<section class="createRidesharing robotoBold">
        <h2 class="montserratBold titleColor">créer votre covoiturage</h2>
        <div class="searchContent">
            <form method="POST" action="/createRidesharing" class='searchForm'>
                <div class='departureCity'>
                    <label for="departureCity">Ville de départ :</label>
                    <input type="text" name="departureCity" id="departureCity" placeholder="Ville" required>
                </div>
                <div class="departureAddress">
                    <label for="departureAddress">Adresse de départ :</label>
                    <input type="text" name="departureAddress" id="departureAddress" placeholder="Rue" required>
                </div>
                <div class='arrivalCity'>
                    <label for="arrivalCity">Ville d'arriver :</label>
                    <input type="text" name="arrivalCity" id="arrivalCity" placeholder="Ville" required>
                </div>
                <div class="arrivalAddress">
                    <label for="arrivalAddress">Adresse d'arriver :</label>
                    <input type="text" name="arrivalAddress" id="arrivalAddress" placeholder="Rue">
                </div>
                <div class="departureDate">
                    <label for="departureDate">Date/heure de départ :</label>
                    <input type="datetime-local" name="departureDate" id="departureDate" required>
                </div>
                <div class="arrivalDate">
                    <label for="arrivalDate">Heure d'arrivée estimée :</label>
                    <input type="datetime-local" name="arrivalDate" id="arrivalDate" required>
                </div>
                <div class="availableSeats">
                    <label for="availableSeats">Places disponibles :</label>
                    <input type="number" name="availableSeats" id="availableSeats" max='6' value="1">
                </div>
                <div class="pricePerSeat">
                    <label for="pricePerSeat">Prix de la place :</label>
                    <input type="number" name="pricePerSeat" id="pricePerSeat" required>
                </div>
                <div class="voitureList">
                    <label for="carSelect">Veuillez choisir une voiture.</label>
                    <select id="carSelect" name="idCar">
                        <option value="">--choisir une option--</option>
                        <?php if(!empty($listCar)):?>
                        <?php foreach ($listCar as $car):
                            $brand = $car['brandInfo'];
                            $car = $car['carInfo']; ?>
                        <option value="<?=$car->getIdCar()?>"><span class="robotoBold"><?=$brand->getLabel()?></span> <?=$car->getModel()?></option>
                        <?php endforeach; ?>
                        <?php endif;?>
                    </select>
                </div>
                <div class="preferenceDiv">
                    <label for="preferenceList">Lister vos préférences :</label>
                    <div class="preferenceList">
                        <input type="text" name="preferenceList" id="preferenceList" placeholder="Ex: non fumeur!">
                    </div>
                    <button class="addPreferenceBtn"><span class="material-symbols-outlined">add</span></button>                    
                </div>
                
                <div class='errorsList'>
                    <ul>
                        <?php if(!empty($errors)){
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