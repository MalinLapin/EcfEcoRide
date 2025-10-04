<?php
        $ride = $ridesharing['ridesharingInfo'];
        $driver = $ridesharing['driverInfo'];
        $carEnergyType = $ridesharing['carEnergyType'];    
?>

<article class="ridesharingCard">
    <div class=infoRidesharing>
        <div class='infoDriver'>
            <div class='energyType'>
                <?php if($carEnergyType == "electric"):?>
                <img src="../assets/images/electric_bolt_24dp.png" alt="icone carburant" class="electricRide">
                <?php endif; ?>
            </div>
            <div class='driver robotoBold'>
                <img src="" alt="photo">
                <p><?=$driver->getPseudo()?></p>
                <p><?=$driver->getGrade()?></p>  
            </div>
        </div>
        <div class='infoDrive'>
            <div class='ridesharing robotoBold'>
                <p><?= $ride->getDepartureCity()?> <br><span class='robotoRegular'><?= $ride->getDepartureAddress()?></span></p>
                <p><?= $ride->getArrivalCity()?> <br><span class='robotoRegular'><?= $ride->getArrivalAddress()?></span></p>
            </div>
            <div class='otherInfo robotoBold'>
                <p class="departureDate"><?= $ride->getDepartureDate()->format('Y-m-d H:i')?></p>
                <p><span> <?= $ride->getPricePerSeat()?> </span> Cr.</p>
                <p><span> <?= $ride->getAvailableSeats()?> </span>places</p>
            </div>
        </div>
    </div>
    <button class='robotoBold detailBtn'>Détails</button>
</article>