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

        <!-- Meta contenant le token CSRF-->
        <meta name="csrfToken" content="<?= htmlspecialchars($csrf_token) ?>">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat&family=Roboto&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
        
        
        <link rel="stylesheet" href="/assets/css/default.css">
        <?php if (!empty($pageCss)): ?>
        <link rel="stylesheet" href="/assets/css/<?=$pageCss?>.css">
        <?php endif; ?>
        
        <title>EcoRide - Covoiturage écologique.</title>
        
    </head>

    <body>
        <header class="header">
            <div class="headerContent">
                <div class="headerSearch">
                    <a href="/search"><span class="material-symbols-outlined">search</span></a>
                </div>                
                <div class="headerLogo">
                    <a href="/"><img class="headerLogo" src="/assets/images/LogoSF.png"
                        alt="Logo de la plateforme EcoRide"></a>
                </div>
                <div class="headerProfil">
                    <?php if (isset($_SESSION['pseudo'])): ?>
                <button class="profilToggle" id="profilToggle" aria-label="Menu profil" aria-expanded="false">
                    <span class="material-symbols-outlined">account_circle</span>
                    <p class='robotoBold'><?=$_SESSION['pseudo']?></p>
                </button>
                <nav class="navMenu" id="navMenu">
                    <ul>
                        <!--Les employer aurons accès uniquement à leur espace.-->
                        <?php if($_SESSION['role'] == 'employe'):?>
                            <li><a href="/employeeSpace"><span class="material-symbols-outlined">work</span> Espace Employer</a></li>
                            <li>
                                <form method="POST" action="/logout">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    <button type="submit"><span class="material-symbols-outlined">logout</span> Déconnexion</button>
                                </form>
                            </li>
                        <?php else :?>
                            <!--L'admin aura accès à toutes les page en plus de son espace-->
                            <?php if($_SESSION['role'] == 'admin'):?>
                                <li><a href="/employeeSpace"><span class="material-symbols-outlined">work</span> Espace Employer</a></li>
                                <li><a href="/adminSpace"><span class="material-symbols-outlined">work</span> Espace Admin</a></li>
                            <?php endif;?>
                            <!--Les utilisateurs auront accès aux pages de service de l'application. -->
                            <li><a href="/profile"><span class="material-symbols-outlined">person</span> Mon profil</a></li>
                            <li><a href="/myRidesharing"><span class="material-symbols-outlined">directions_car</span> Mes trajets</a></li>
                            <li><a href="/contact"><span class="material-symbols-outlined">mail</span>Contact</a></li>
                            <li>
                                <form method="POST" action="/logout">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    <button type="submit"><span class="material-symbols-outlined">logout</span> Déconnexion</button>
                                </form>
                            </li>
                        <?php endif;?>
                    </ul>
                </nav>
            <?php else: ?>
                <a href="/login"><span class="material-symbols-outlined">account_circle</span></a>
            <?php endif; ?>
                </div>
            </div>
        </header>
        
        <!--On chargera uniquement des templates dans cette balise main afin de garder une page unique.-->
        <main class='mainContent'>
            <?php if(!empty($content)){ echo $content;}?>                        
        </main>

        <footer class="footer">
            <address class="robotoRegular">
                <a href="mailto: ecoride.contact@exemple.com">ecoride.contact@exemple.com</a>
            </address>
            <p class="robotoRegular"><span>&#169</span> Marc Uny | tous droits réservés</p>
            <a href="/mentionLegal" class="robotoRegular">Mention-légales</a>
        </footer>
        <script src="/assets/js/navbar.js"></script>
        <?php if (!empty($scriptJs)): ?>
        <script src="/assets/js/<?=$scriptJs?>.js"></script>
        <?php endif; ?>
    </body>
            
</html>