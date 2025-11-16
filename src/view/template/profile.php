<section class='profileSection robotoRegular'>
    <div class="titleDiv">
        <h2 class="montserratBold titleColor">Mon profil</h2>
    </div>

    <div class='navBarProfil robotoBold'>
        <div id="compteBtn" class="compteBtn">
            <a href="/profile">Mon profil</a>
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
            <p class='pseudo'><?=htmlspecialchars($user->getPseudo())?></p>
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
                    <p><?=htmlspecialchars($brand->getLabel())?></p>
                </div>
                <div class="modelCar">
                    <p><?=htmlspecialchars($car->getModel())?></p>
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
            <button id="openAddCarModal" class="addCarBtn robotoBold">
                <span class="material-symbols-outlined">directions_car</span>
                <span class="material-symbols-outlined">add</span>
            </button>  
        </div>        
    </div>

    <div id="modalAddCar" class="addCarModal">
        <div class="modalContent">
            <button id="closeModal" class="closeBtn" type="button">&times;</button>
            <h3 class="montserratBold titleColor">Ajouter un véhicule</h3>

            <form id="formAddCar" class="robotoRegular">

                <div class="formGroup">
                    <label for="brandId">Marque</label>
                    <select id="brandId" name="brandId" required>
                        <option value="">-- Choisir --</option>
                        <?php foreach ($listBrand as $brand): ?>
                            <option value="<?=$brand->getIdBrand()?>"><?=htmlspecialchars($brand->getLabel())?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="formGroup">
                    <label for="model">Modèle</label>
                    <input type="text" id="model" name="model" placeholder="Ex: Clio, 308..." required>
                </div>

                <div class="formGroup">
                    <label for="registrationNumber">Plaque d'immatriculation</label>
                    <input type="text" id="registrationNumber" name="registrationNumber" pattern="[A-Z]{2}-[0-9]{3}-[A-Z]{2}" placeholder="AB-123-CD" required>
                </div>

                <div class="formGroup">
                    <label for="firstRegistration">Date de 1ère mise en circulation</label>
                    <input type="date" id="firstRegistration" name="firstRegistration" required>
                </div>

                <div class="formGroup">
                    <label for="energyType">Type d'énergie</label>
                    <select id="energyType" name="energyType" required>
                        <option value="">-- Choisir --</option>
                        <option value="electric">Électrique</option>
                        <option value="hybrid">Hybride</option>
                        <option value="gpl">GPL</option>
                        <option value="essence">Essence</option>
                        <option value="diesel">Diesel</option>                
                    </select>
                </div>

                <div class="formGroup">
                    <label for="color">Couleur</label>
                    <input type="text" id="color" name="color" placeholder="Ex: Blanc, Noir..." required>
                </div>

                <div class="forActions">
                    <button type="submit" class="submitBtn robotoBold">Ajouter</button>
                    <button type="button" id="cancelBtn" class="cancelBtn robotoBold">Annuler</button>
                </div>

                <div id="errorMsg" class="errorMessage"></div>
            </form>
        </div>
    </div>

    <?php if (!empty($listCar)): ?>
    <div class='newRide'>
        <a href="/showCreateRidesharing" class="newRideBtn robotoBold">Proposer un trajet.</a>
    </div>
    <?php endif; ?>
</section> 