<?php
$pageCss='listRidesharing';
require_once '../template/header.php'; ?>

<section class="listRidesharing">
        <h2 class="montserratBold titleColor">Liste des trajets :</h2>
        <div class="sectionContent">
            <?php if($listRidesharing){
                foreach ($listRidesharing as $ridesharing):
                    require '../template/ridesharingCard.php';            
                endforeach;}
            else{ ?>
            <h2> Aucun trajet ne correspond à votre recherche</h2>
            <a href="../page/searchRidesharing.php"><button >Nouvelle recherche</button></a>
            <?php } ?>
                
        </div>
</section>

<?php require_once '../template/footer.php'; ?>