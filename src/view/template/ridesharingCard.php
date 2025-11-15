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
                <span class="material-symbols-outlined">electric_bolt</span>
                <?php endif; ?>
            </div>
            <div class='driver robotoBold'>
                <img src="" alt="photo">
                <p><?=htmlspecialchars($driver->getPseudo())?></p>
                <p><?=$driver->getGrade()?></p>  
            </div>
        </div>
        <div class='infoDrive'>
            <div class='ridesharing robotoBold'>
                <p><?= htmlspecialchars($ride->getDepartureCity())?> <br><span class='robotoRegular'><?= htmlspecialchars($ride->getDepartureAddress())?></span></p>
                <p><?= htmlspecialchars($ride->getArrivalCity())?> <br><span class='robotoRegular'><?= htmlspecialchars($ride->getArrivalAddress())?></span></p>
            </div>
            <div class='otherInfo robotoBold'>
                <p class="departureDate"><?= $ride->getDepartureDate()->format('Y-m-d H:i')?></p>
                <p><span> <?= $ride->getPricePerSeat()?> </span> Cr.</p>
                <p><span> <?= $ride->getAvailableSeats()?> </span>places</p>
            </div>
        </div>
    </div>
    <div>
        <a href="/ridesharingDetail/<?= $ride->getIdRidesharing() ?>" class="robotoBold detailBtn">
            <button type="submit" class="robotoBold detailBtn">Détails</button>
        </a> 
    </div>
</article>