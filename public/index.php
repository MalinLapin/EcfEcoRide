<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Ecoride, la plateforme de covoiturage écologique dédiée aux trajets en voiture. Simplifiez vos déplacements tout en préservant l’environnement grâce à une application web moderne, intuitive et engagée dans la mobilité durable en France.">
    <link rel="canonical" href="https://www.Ecoride.com/home/" />
    <meta property="og:title" content="EcoRide - Plateforme de covoiturage écologique." />
    <meta property="og:description"
        content="Ecoride, la plateforme de covoiturage écologique dédiée aux trajets en voiture. Simplifiez vos déplacements tout en préservant l’environnement grâce à une application web moderne, intuitive et engagée dans la mobilité durable en France." />
    <meta property="og:image" content="https://www.exemple.com/assets/images/Logo_Slogan.jpeg" />
    <meta property="og:url" content="https://www.Ecoride.com/home/" />
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="EcoRide - Plateforme de covoiturage écologique.">
    <meta name="twitter:description"
        content="Ecoride, la plateforme de covoiturage écologique dédiée aux trajets en voiture. Simplifiez vos déplacements tout en préservant l’environnement grâce à une application web moderne, intuitive et engagée dans la mobilité durable en France.">
    <meta name="twitter:image" content="https://www.exemple.com/assets/images/Logo_Slogan.jpeg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:400,700,400italic&family=Roboto:400,700,400italic&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="pages/css/style.css">

    <title>EcoRide - Plateforme de covoiturage écologique.</title>

</head>

<body>
    <header class="header">
        <div class="headerContent">
            <div class="headerSearch">
                <span class="material-icons">search</span>
            </div>
            <div class="headerLogo">
                <img class="headerLogo" src="/public/assets/images/LogoSF.png" alt="Logo de la plateforme EcoRide">
            </div>
            <div class="headerProfil">
                <span class="material-icons">account_circle</span>
            </div>
        </div>
    </header>
    <main class="mainContent">
        <section class="presentationSection">
            <h1 class="montserratBold titleColor">EcoRide, votre covoiturage <br>éco-responsable</h1>
            <img class="imagePresentation" src="./assets/images/PhotoPresentation.png"
                alt="Photo représentant le covoiturage avec EcoRide.">
        </section>
        <section class="searchSection">
            <h2 class="montserratBold titleColor">Prenez place :</h2>
            <div class="searchContent">
                <form method="GET" action="#">
                    <div>
                        <label for="departureBar" class="robotoBold">Départ :</label>
                        <input type="text" name="departureBar" id="departureBar" placeholder="Lieu de départ">
                    </div>
                    <div>
                        <label for="arrivalBar" class="robotoBold">Déstination :</label>
                        <input type="text" name="arrivalBar" id="arrivalBar" placeholder="Lieu de destination">
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

    <footer class="footer">
        <address class="robotoRegular">Contact</address>
        <p class="robotoRegular"><span>&#169</span> Marc Uny | tous droits réservés</p>
        <a href="./mentionLegales.html" class="robotoRegular">Mention-légales</a>
    </footer>
</body>

</html>