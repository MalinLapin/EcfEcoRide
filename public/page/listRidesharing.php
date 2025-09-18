<?php
$pageCss='list-ridesharing';
require_once '../template/header.php'; ?>
<section class="searchSection">
        <h2 class="montserratBold titleColor">Prenez place :</h2>
        <div class="searchContent">
            <form method="POST" action="#">
                <div>
                    <label for="departureBar" class="robotoBold">Départ :</label>
                    <input type="text" name="departureBar" id="departureBar" placeholder="Ville, CP, rue">
                </div>
                <div>
                    <label for="arrivalBar" class="robotoBold">Déstination :</label>
                    <input type="text" name="arrivalBar" id="arrivalBar" placeholder="Ville, CP, rue">
                </div>
                <div>
                    <label for="dateSearch" class="robotoBold">Date/heure :</label>
                    <input type="datetime-local" name="dateSearch" id="dateSearch">
                </div>
                <div>
                    <label for="seatsBar" class="robotoBold">Place :</label>
                    <input type="number" name="seatsBar" id="seatsBar" value="1">
                </div>
                <div>
                    <label for="price" class="robotoBold">Place :</label>
                    <input type="number" name="seatsBar" id="seatsBar">
                </div>

                <div>
                    <button type="submit" class="btnSearch robotoBold">En route !</button>
                </div>
            </form>
        </div>
</section>
<section class='list_ridesharing'>

</section>

<?php require_once '../template/footer.php'; ?>