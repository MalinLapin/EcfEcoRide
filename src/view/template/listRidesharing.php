<section class="listRidesharing robotoRegular">
        <h2 class="montserratBold titleColor">Liste des trajets :</h2>
        <div class="listCard">
            <?php if (!empty($listRidesharing)): ?>
                <?php foreach ($listRidesharing as $ridesharing): ?>
                    <?php include __DIR__.'/ridesharingCard.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <h2>Aucun trajet ne correspond à votre recherche</h2>
            <?php endif; ?> 
            <a href="/search"><button class='newSearchBtn'>Nouvelle recherche</button></a>               
        </div>
</section>