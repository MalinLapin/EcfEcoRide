
<?php $pageCss = 'index';
require_once "../public/template/header.php"; ?>

<main class="mainContent">
    <section class="presentationSection">
        <h1 class="montserratBold titleColor">EcoRide, votre covoiturage <br>éco-responsable</h1>
        <img class="imagePresentation" src="./assets/images/PhotoPresentation.png"
            alt="Photo représentant le covoiturage avec EcoRide.">
    </section>
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
                    <label for="dateSearch" class="robotoBold">Date :</label>
                    <input type="datetime-local" name="dateSearch" id="dateSearch">
                </div>
                <div>
                    <label for="seatsBar" class="robotoBold">Place :</label>
                    <input type="number" name="seatsBar" id="seatsBar" value="1">
                </div>
                <div>
                    <button type="submit" class="btnSearch robotoBold">En route !</button>
                </div>
            </form>
        </div>
    </section>
    <section class="aboutSection">
        <h3 class="montserratBold titleColor">EcoRide c'est quoi ?</h3>
        <div class="aboutContent">
            <div class="card">
                <h4 class="robotoBold">Qui sommes-nous ?</h4>
                <p class="robotoRegular">EcoRide est une start-up française née de la volonté de rendre la mobilité
                    plus verte et
                    accessible à tous.
                    Nous croyons que le covoiturage est la solution de demain pour voyager autrement.
                </p>
            </div>
            <div class="card">
                <h4 class="robotoBold">Notre mission</h4>
                <p class="robotoRegular">Encourager le partage des trajets et lutter contre la pollution au
                    quotidien, en rendant le
                    covoiturage simple, écologique et convivial.
                </p>
            </div>
            <div class="card">
                <h4 class="robotoBold">En chiffres</h4>
                <ul>
                    <li class="robotoRegular">
                        + de 500 trajets partagés chaque mois
                    </li>
                    <li class="robotoRegular">
                        1200 membres déjà engagés
                    </li>
                    <li class="robotoRegular">
                        15 tonnes de CO₂ économisées depuis la création
                    </li>
                </ul>
            </div>
            <div class="card">
                <h4 class="robotoBold">Pourquoi choisir EcoRide ?</h4>
                <ul>
                    <li class="robotoRegular">
                        Plateforme simple et rapide à utiliser
                    </li>
                    <li class="robotoRegular">
                        Communauté engagée et bienveillante
                    </li>
                    <li class="robotoRegular">
                        Réduisez vos frais de transport
                    </li>
                    <li class="robotoRegular">
                        Agissez concrètement pour la planète
                    </li>
                </ul>
            </div>
        </div>
    </section>
</main>

<?php require_once '../public/template/footer.php'; ?>