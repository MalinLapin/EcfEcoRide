<section class='profileSection robotoRegular'>
    <div class="titleDiv">
        <h2 class="montserratBold titleColor">Mon profil</h2>
    </div>

    <div class='navBarProfil robotoBold'>
        <div id="compteBtn" class="compteBtn">
            <a href="/profile">Mon compte</a>
        </div>
        <div id="myRideBtn" class="myRideBtn">
            <a href="/myRidesharing">Mes trajets</a>
        </div>        
    </div>

    <div class='userCard'>
        <div class ='picture'>
            <img src="" alt="photo de profil">
        </div>
        <div class='userInfo'>
            <p class='pseudo'><?=$user->getPseudo()?></p>
            <p class='creationDate'><?=$user->getCreatedAt()->format('d-m-Y')?></p>
            <p class='creditBalance'><span class="robotoBold"><?=$user->getCreditBalance()?> </span>crédits</p>
        </div>
    </div>

    <div class="myCars">
        <ul class="listCars">
            <?php if (!empty($listCar)): ?>
            <?php foreach ($listCar as $row):
                $car = $row['carInfo'];
                $brand = $row['brandInfo'];?>
            <li>
                <div class="carLogo">
                    <span class="material-symbols-outlined">directions_car</span>
                </div>
                <div class ="brandCar">
                    <p><?=$brand->getLabel()?></p>
                </div>
                <div class="modelCar">
                    <p><?=$car->getModel()?></p>
                </div>
                <?php if ($car->getEnergyType()->value=='electric'):?>
                    <div class="energyType">
                        <span class="material-symbols-outlined">electric_bolt</span>
                    </div>
                <?php else: ?>
                    <div class="energyType">
                        <span class="material-symbols-outlined">local_gas_station</span>
                    </div>
                    <?php endif; ?>
                <div class="editCar">
                    <span class="material-symbols-outlined">edit</span>
                </div>    
            </li>
            <?php endforeach;  else: ?>
                <li><span class="material-symbols-outlined">warning</span> Si vous souhaitez créer votre propre covoiturage il vous faut ajouter une voiture.</li>
            <?php endif; ?>
        </ul>
        <div class="addCarBtn">
            <a href="/addCar">
                <span class="material-symbols-outlined">directions_car</span>
                <span class="material-symbols-outlined">add</span>
            </a>  
        </div>        
    </div>

    <div class='newRide'>
        <a href="/showCreateRidesharing" class="newRideBtn robotoBold">Proposer un trajet.</a>
    </div>
</section> 