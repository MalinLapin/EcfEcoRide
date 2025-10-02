<section class="listRidesharing">
        <h2 class="montserratBold titleColor">Liste des trajets :</h2>
        <div class="sectionContent">
            <?php if($listRidesharing){
                foreach ($listRidesharing as $ridesharing):
                    require '../template/ridesharingCard.php';            
                endforeach;}
            ?>
            <h2> Aucun trajet ne correspond à votre recherche</h2>
            <a href="/search"><button >Nouvelle recherche</button></a>               
        </div>
</section>