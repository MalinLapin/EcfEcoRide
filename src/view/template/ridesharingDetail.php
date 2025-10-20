<?php
    $ride = $ridesharingDetails['ridesharing'];
    $driver = $ridesharingDetails['driver'];
    $car = $ridesharingDetails['car'];
    $brand = $ridesharingDetails['brand'];

?>
<section class='ridesharingDetail mainContent robotoRegular'>
    <section class ='rideInfo'>
        <h3 class ='montserratBold'>Trajet</h3>
        <div class='rideAddress'>
            <div class='departure'>
                <p class='robotoBold'><?=$ride->getDepartureCity()?>,<br><span class='robotoRegular'><?=$ride->getDepartureAddress()?></span></p>
                <p><?=$ride->getDepartureDate()->format('H:i')?></p>
            </div>
            <img src="../assets/images/arrow_to_right.png" alt="logo flèche">
            <div class='arrival'>
                <p class='robotoBold'><?=$ride->getArrivalCity()?><br><span class='robotoRegular'><?=$ride->getArrivalAddress()?></span></p>
                <?php if($ride->getArrivalDate()):?>
                <p class='robotoRegular'><?=$ride->getArrivalDate()->format('H:i')?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class='infoCard'>
            <div class='date'>
                <img src="../assets/images/calendar.png" alt="icone calendar">
                <p><?=$ride->getDepartureDate()->format('Y-m-d')?></p>
            </div>
            <div class='clock'>
                <img src="../assets/images/clock.png" alt="icone clock">
                <p>0 h 30</p>
            </div>
            <div class='road'>
                <img src="../assets/images/road.png" alt="icone road">
                <p>70 km</p>
            </div>
            <div class='availableSeats'>
                <img src="../assets/images/passengerIcon.png" alt="icone availableSeat">
                <p><?=$ride->getAvailableSeats()?></p>
            </div>
        </div>
    </section>
    
    <section class='participationInfo'>
        <form action="#" method='GET' class='seatForm'>
            <div class='participationCard'>
                <div class='ridesharingPrice'>
                    <p><span class='robotoBold'><?=$ride->getPricePerSeat()?></span>Crédits</p>
                </div>
                <label for="nbSeat">Veuillez choisir le nombre de siège désiré :</label>
                <input type="number" name="nbSeats" id="nbSeats" value="1" max='6' placeholder="Nombre de place">
            </div>
            <button type='submit' class="btnSearch robotoBold">Réserver vos places.</button>
        </form>
    </section>
    
    
    <section class='driver'>
        <div class='titleDriver'>
            <img src="../assets/images/steeringWheel.png" alt="logo volant">
            <h3 class='montserratBold'>Information du conducteur</h3>
        </div>
        <div class='infoDriver'>
            <img src="#" alt="Photo de profil">
            <div>
                <p class='robotoBold'><?=$driver->getPseudo()?></p>
                <p><?=$driver->getGrade()?> /5</p>
            </div>
        </div>           
    </section>
    
    <section class='car'>
        <div class='titleCar'>
            <img src="../assets/images/car_logo.png" alt="icon voiture">
            <h3 class='montserratBold'>Information du véhicule</h3>
        </div>
        <div class='carInfo '>
            <div><p>Marque: <span><?= $brand?></span></p></div>
            <div><p>Modele: <span><?=$car->getModel()?></span></p></div>
            <div><p>Energie: <span><?=$car->getEnergyType()->value ?></span></p></div>
            <div><p>Couleur: <span><?=$car->getColor()?></span></p></div>           
        </div>
        <?php
            /* Reste la partie préférence */
        ?>
        <div class='preferenceDriver'>
            <h4>Préférence du conducteur</h4>           
            <ul>
                <li>Fumeur accepter</li>
                <li>Animaux non accepter</li>
                <li>1 bagage par passager max.</li>                    
            </ul>
        </div>
        
    </section>
    
    <?php
    /* Reste la partie avis */
    ?>
    <section class='reviewList'>
        <div class='reviewTitle'>
            <img src="../assets/images/reviews_icon.png" alt="icon avis">
            <h3 class='montserratBold'>Avis des passagers</h3>
        </div>
        <div class='review'>
            <div class='redactorReview robotoBold'>
                <p>Passenger01</p>
            </div>
            <div class='contentReview'>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Atque ipsa praesentium aliquam earum temporibus reprehenderit quibusdam nesciunt officiis qui sint aliquid quia iure voluptates maiores nisi neque, rem sit ut!</p>
            </div>
        </div>
        <div class='review'>
            <div class='redactorReview robotoBold'>
                <p>Passanger02</p>
            </div>
            <div class='contentReview'>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Atque ipsa praesentium aliquam earum </p>
            </div>
        </div>
        
    </section>
</section>