<?php
    $ride = $ridesharingDetails['ridesharing'];
    $driver = $ridesharingDetails['driver'];
    $car = $ridesharingDetails['car'];
    $brand = $ridesharingDetails['brand'];
    ?>
<section class='ridesharingDetail robotoRegular'>
    <?php if(!empty($flash)):?>
        <div class='errorInfo'>
            <p class='robotoBold'><?=$flash['message']?></p>
        </div>
        <?php endif; ?>
    <div class ='rideInfo'>
        <h3 class ='montserratBold'>Trajet</h3>
        <div class='rideAddress'>
            <div class='departure'>
                <p class='robotoBold'><?=$ride->getDepartureCity()?>,<br><span class='robotoRegular'><?=$ride->getDepartureAddress()?></span></p>
                <p><?=$ride->getDepartureDate()->format('H:i')?></p>
            </div>
            <span class="material-symbols-outlined">arrow_right_alt</span>
            <div class='arrival'>
                <p class='robotoBold'><?=$ride->getArrivalCity()?><br><span class='robotoRegular'><?=$ride->getArrivalAddress()?></span></p>
                <?php if($ride->getArrivalDate()):?>
                <p class='robotoRegular'><?=$ride->getArrivalDate()->format('H:i')?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class='infoCard'>
            <div class='date'>
                <span class="material-symbols-outlined">calendar_month</span>
                <p><?=$ride->getDepartureDate()->format('Y-m-d')?></p>
            </div>
            <div class='clock'>
                <span class="material-symbols-outlined">nest_clock_farsight_analog</span>
                <p>0 h 30</p>
            </div>
            <div class='road'>
                <span class="material-symbols-outlined">road</span>
                <p>70 km</p>
            </div>
            <div class='availableSeats'>
                <span class="material-symbols-outlined">group</span>
                <p><?=$ride->getAvailableSeats()?> dispo.</p>
            </div>
        </div>
    </div>
    
    <div class='participationInfo'>
        <form action="/participate" method='POST' class='seatForm'>
            <div class='participationCard'>
                <div class='ridesharingPrice'>
                    <p><span class='robotoBold'><?=$ride->getPricePerSeat()?> </span>Crédits /places</p>
                </div>
                <label for="nbSeat">Veuillez choisir le nombre de siège désiré :</label>
                <input type="number" name="nbSeats" id="nbSeats" value="1" max='6' placeholder="Nombre de place">
            </div>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="idRidesharing" value="<?=$ride->getIdRidesharing() ?>">
            <button type='submit' class="btnSearch robotoBold">Réserver</button>
        </form>
    </div>
    
    
    <div class='driver'>
        <div class='titleDriver'>
            <span class="material-symbols-outlined">search_hands_free</span>
            <h3 class='montserratBold'>Information du conducteur</h3>
        </div>
        <div class='infoDriver'>
            <img src="#" alt="Photo de profil">
            <div>
                <p class='robotoBold'><?=$driver->getPseudo()?></p>
                <p><?=$driver->getGrade()?> /5</p>
            </div>
        </div>           
    </div>
    
    <div class='car'>
        <div class='titleCar'>
            <span class="material-symbols-outlined">directions_car</span>
            <h3 class='montserratBold'>Information du véhicule</h3>
        </div>
        <div class='carInfo '>
            <div><p>Marque: <span><?= $brand?></span></p></div>
            <div><p>Modele: <span><?=$car->getModel()?></span></p></div>
            <div><p>Energie: <span><?=$car->getEnergyType()->value ?></span></p></div>
            <div><p>Couleur: <span><?=$car->getColor()?></span></p></div>           
        </div>
        
        <?php if (!empty($listPreference)):?>
        
        <div class='preferenceDriver'>            
            <h4>Préférence du conducteur</h4>           
            <ul>
                <?php foreach($listPreference as $preference):?>
                <li><?=$preference->getLabel()?></li>
                <?php endforeach; ?>                    
            </ul>
        </div>

        <?php endif; ?>        
    </div>


    <?php if (!empty($listReview)):?>
    <div class='reviewList'>
        <div class='reviewTitle'>
            <span class="material-symbols-outlined">reviews</span>
            <h3 class='montserratBold'>Avis des passagers</h3>
        </div>

        <?php foreach($listReview as $review):?>
            <?php 
            $reviewContent= $review['review'];
            $pseudoRedactor = $review['pseudoRedactor'];
            ?>
        <div class='review'>
            <div class='redactorReview robotoBold'>
                <p><?=$pseudoRedactor?></p>
                <p><?=$reviewContent->getRating() ?? null ?> /5 </p>
            </div>
            <div class='commentReview'>
                <p><?=$reviewContent->getComment() ?? null ?></p>
            </div>
        </div>
        <?php endforeach; ?>        
    </div>
    <?php endif; ?> 
</section>