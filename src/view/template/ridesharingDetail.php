<section class='ridesharingDetail mainContent robotoRegular'>
    <section class ='rideInfo'>
        <h3 class ='montserratBold'>Trajet</h3>
        <div class='rideAddress'>
            <div class='departure'>
                <p class='robotoBold'>Lyon,<br><span class='robotoRegular'>Part Dieu</span></p>
                <p>10h45</p>
            </div>
            <img src="../assets/images/arrow_to_right.png" alt="logo flèche">
            <div class='arrival'>
                <p class='robotoBold'>Saint-Etienne,<br><span class='robotoRegular'> Rue du test</span></p>
                <p class='robotoRegular'>11h15</p>
            </div>
        </div>
        <div class='infoCard'>
            <div class='date'>
                <img src="../assets/images/calendar.png" alt="icone calendar">
                <p>Jeu 05 Mai</p>
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
                <p>2 Places dispo.</p>
            </div>
        </div>
    </section>
    
    <section class='participationInfo'>
        <form action="#" method='GET' class='seatForm'>
            <div class='participationCard'>
                <div class='ridesharingPrice'>
                    <p><span class='robotoBold'>10</span>Crédits</p>
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
                <p class='robotoBold'>Pseudo Driver</p>
                <p>4.5/5</p>
            </div>
        </div>           
    </section>
    
    <section class='car'>
        <div class='titleCar'>
            <img src="../assets/images/car_logo.png" alt="icon voiture">
            <h3 class='montserratBold'>Information du véhicule</h3>
        </div>
        <div class='carInfo '>
            <div><p>Marque: <span>Renault</span></p></div>
            <div><p>Modele: <span>Zoé</span></p></div>
            <div><p>Energie: <span>Electrique</span></p></div>
            <div><p>Couleur: <span>Bleu</span></p></div>           
        </div>
        <div class='preferenceDriver'>
            <h4>Préférence du conducteur</h4>           
            <ul>
                <li>Fumeur accepter</li>
                <li>Animaux non accepter</li>
                <li>1 bagage par passager max.</li>                    
            </ul>
        </div>
        
    </section>
    
    
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


<?php require_once '../template/footer.php'; ?> 