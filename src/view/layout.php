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

    <link rel="stylesheet" href="/assets/css/default.css">
    <?php if (isset($pageCss)&& $pageCss): ?>
        <link rel="stylesheet" href="/assets/css/<?=$pageCss?>.css">
    <?php endif; ?>

    <title>EcoRide - Covoiturage écologique.</title>

</head>

<body>
    <header class="header">
        <div class="headerContent">
            <div class="headerSearch">
                <a href="/search"><span class="material-icons">search</span></a>
            </div>
            <div class="headerLogo">
                <a href="/"><img class="headerLogo" src="/assets/images/LogoSF.png"
                        alt="Logo de la plateforme EcoRide"></a>
            </div>
            <div class="headerProfil">
                <?php if (isset($_SESSION['pseudo'])):?>
                    <a href="/profil"><span class="material-icons">account_circle</span><br>
                    <p class='robotoBold'><?=$_SESSION['pseudo']?></p></a>
                <?php else :?>
                    <a href="/login"><span class="material-icons">account_circle</span></a>
                <?php endif;?>
            </div>
        </div>
    </header>

    <main class='mainContent'>
        <?php if(isset($content)&&$content){
        echo $content;}?>

    </main>
    <footer class="footer">
        <address class="robotoRegular">Contact</address>
        <p class="robotoRegular"><span>&#169</span> Marc Uny | tous droits réservés</p>
        <a href="../view/template/mentionLegale.php" class="robotoRegular">Mention-légales</a> 
        
        <!--Element provisoir-->
        <?php if (isset($_SESSION['pseudo'])):?>
        <form method="POST" action="/logout">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <button type="submit">Se déconnecter</button>
        </form>
        <?php endif; ?>
    </footer>
</body>

</html>