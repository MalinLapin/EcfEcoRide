<?php
$pageCss='searchRidesharing';
require_once '../template/header.php'; ?>
<section class="searchSection">
        <h2 class="montserratBold titleColor">Trouver votre covoiturage</h2>
        <div class="searchContent">
            <form method="POST" action="#" class='searchForm'>
                <div class='departureCity'>
                    <label for="departureCity" class="robotoBold">Ville de départ :</label>
                    <input type="text" name="departureCity" id="departureCity" placeholder="Ville" required>
                </div>
                <div class="departureAddress">
                    <label for="departureAddress" class="robotoBold">Adresse de départ :</label>
                    <input type="text" name="departureAddress" id="departureAddress" placeholder="CP, rue">
                </div>
                <div class='arrivalCity'>
                    <label for="arrivalCity" class="robotoBold">Ville d'arriver :</label>
                    <input type="text" name="arrivalCity" id="arrivalCity" placeholder="Ville" required>
                </div>
                <div class="arrivalAddress">
                    <label for="arrivalAddress" class="robotoBold">Adresse d'arriver :</label>
                    <input type="text" name="arrivalAddress" id="arrivalAddress" placeholder="CP, rue">
                </div>
                <div class="departureDate">
                    <label for="departureDate" class="robotoBold">Date/heure :</label>
                    <input type="datetime-local" name="departureDate" id="departureDate" required>
                </div>
                <div class="nbSeats">
                    <label for="nbSeats" class="robotoBold">Place :</label>
                    <input type="number" name="nbSeats" id="nbSeats" value="1" max='6'>
                </div>
                <div class="pricePerSeat">
                    <label for="pricePerSeat" class="robotoBold">Prix de la place</label>
                    <input type="number" name="pricePerSeat" id="pricePerSeat">
                </div>
                <div class="gradeDriver">
                    <label for="gradeDriver" class="robotoBold">Note du conducteur</label>
                    <input type="number" name="gradeDriver" id="gradeDriver" min='1' max='5'>
                </div>
                <div class="energyType">
                    <label for="energyType"class="robotoBold">Trajet électrique uniquement ?</label>
                    <input type="checkbox" name="energyType" id="energyType">
                </div>

                <div>
                    <button type="submit" class="btnSearch robotoBold">C'est partie!</button>
                </div>
            </form>
        </div>
</section>

<?php require_once '../template/footer.php'; ?>