<?php
    $ride = $listRidesharing['ridesharing'];
    $driver = $listRidesharing['driverInfo'];
    $carEnergyType = $listRidesharing['carEnergyType'];
?>

<article class="ridesharingCard">
    <div class=infoRidesharing>
        <div class='infoDriver'>
            <div class='energyType'>
                <img src="../assets/images/electric_bolt_24dp.png" alt="icone carburant">
            </div>
            <div class='driver robotoBold'>
                <img src="" alt="photo">
                <p><?=$ride->getPseudo()?></p>
                <p><?=$ride->getGrade()?></p>  
            </div>
        </div>
        <div class='infoDrive'>
            <div class='ridesharing robotoBold'>
                <p>Lyon, <br><span class='robotoRegular'>Part Dieu</span></p>
                <p>Saint Etienne <br><span class='robotoRegular'>Rue du test</span></p>
            </div>
            <div class='otherInfo robotoBold'>
                <p>16h45</p>
                <p><span> 8 </span> Cr.</p>
                <p><span> 5 </span>places</p>
            </div>
        </div>
    </div>
    <button class='robotoBold detailBtn'>Détails</button>
</article>