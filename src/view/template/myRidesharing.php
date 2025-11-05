<section class='profileSection robotoRegular'>
    <div class="titleDiv">
        <h2 class="montserratBold titleColor">Mes trajets</h2>
    </div>

    <div class='navBarProfil robotoBold'>
        <div id="compteBtn" class="compteBtn">
            <a href="/profile">Mon profil</a>
        </div>
        <div id="myRideBtn" class="myRideBtn">
            <a href="/myRidesharing">Mes trajets</a>
        </div>        
    </div>

    <div class='ridesTabsContainer'>
        <div class='ridesTabs robotoBold'>
            <div id="participatedTab" class="rideTab activeTab">
                <span>Trajets participés</span>
            </div>
            <div id="proposedTab" class="rideTab">
                <span>Trajets proposés</span>
            </div>
        </div>

        <!-- Trajets participés -->
        <div id="participatedRides" class="ridesContent activeContent">
            <?php if (!empty($participations)): ?>
                <div class="ridesList">
                    <?php foreach ($participations as $participationInfo):
                        $ride = $participationInfo['ride'];
                        $participation = $participationInfo['participation'];?>
                        <div class="rideCard <?=$ride->getStatus()->value?>">
                            <div class="rideHeader">
                                <div class="rideStatus">
                                    <?php if ($ride->getStatus()->value == 'ongoing'): ?>
                                        <span class="statusBadge inProgress">En cours</span>
                                    <?php elseif ($ride->getStatus()->value == 'pending'): ?>
                                        <span class="statusBadge upcoming">À venir</span>
                                    <?php else: ?>
                                        <span class="statusBadge completed">Effectué</span>
                                    <?php endif; ?>
                                </div>
                                <div class="rideDate">
                                    <span class="material-symbols-outlined">calendar_today</span>
                                    <span><?=$ride->getDepartureDate()->format('d/m/Y')?></span>
                                </div>
                            </div>

                            <div class="rideInfo">
                                <div class="rideRoute">
                                    <div class="routePoint">
                                        <span class="material-symbols-outlined">trip_origin</span>
                                        <p class="robotoBold"><?=$ride->getDepartureCity()?></p>
                                    </div>
                                    <div class="routeLine"></div>
                                    <div class="routePoint">
                                        <span class="material-symbols-outlined">location_on</span>
                                        <p class="robotoBold"><?=$ride->getArrivalCity()?></p>
                                    </div>
                                </div>

                                <div class="rideDetails">
                                    <div class="detailItem">
                                        <span class="material-symbols-outlined">person</span>
                                        <span class="robotoBold"><?= $participation->getNbSeats()?> siège(s) reservé(s)</span>
                                    </div>
                                    <div class="detailItem">
                                        <span class="material-symbols-outlined">payments</span>
                                        <span class="robotoBold"><?=$ride->getPricePerSeat()?> crédits / sièges</span>
                                    </div>
                                </div>
                            </div>

                            <?php if ($ride->getStatus()->value == 'pending'):?>
                                <div class="rideActions">
                                    <button class="cancelParticipationBtn robotoBold" data-participate-id="<?=$participation->getIdParticipate()?>">
                                        <span class="material-symbols-outlined">cancel</span>
                                        <span>Annuler ma participation</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="emptyState">
                    <span class="material-symbols-outlined">info</span>
                    <p>Vous n'avez participé à aucun trajet pour le moment.</p>
                    <a href="/searchRidesharing" class="searchRideBtn robotoBold">Rechercher un trajet</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Trajets proposés -->
        <div id="proposedRides" class="ridesContent">
            <?php if (!empty($offeredRides)): ?>
                <div class="ridesList">
                    <?php foreach ($offeredRides as $offeredRide):
                        $ride = $offeredRide['ride']; ?>
                        <div class="rideCard <?=$ride->getStatus()->value?>">
                            <div class="rideHeader">
                                <div class="rideStatus">
                                    <?php if ($ride->getStatus()->value == 'ongoing'): ?>
                                        <span class="statusBadge inProgress">En cours</span>
                                    <?php elseif ($ride->getStatus()->value == 'pending'): ?>
                                        <span class="statusBadge pending">En attente</span>
                                    <?php else: ?>
                                        <span class="statusBadge completed">Effectué</span>
                                    <?php endif; ?>
                                </div>
                                <div class="rideDate">
                                    <span class="material-symbols-outlined">calendar_today</span>
                                    <span><?=$ride->getDepartureDate()->format('d/m/Y')?></span>
                                </div>
                            </div>

                            <div class="rideInfo">
                                <div class="rideRoute">
                                    <div class="routePoint">
                                        <span class="material-symbols-outlined">trip_origin</span>
                                        <p class="robotoBold"><?=$ride->getDepartureCity()?></p>
                                    </div>
                                    <div class="routeLine"></div>
                                    <div class="routePoint">
                                        <span class="material-symbols-outlined">location_on</span>
                                        <p class="robotoBold"><?=$ride->getArrivalCity()?></p>
                                    </div>
                                </div>

                                <div class="rideDetails">
                                    <div class="detailItem">
                                        <span class="material-symbols-outlined">group</span>
                                        <span><?=$ride->getNbParticipant()?> places reservées</span>
                                    </div>
                                    <div class="detailItem">
                                        <span class="material-symbols-outlined">payments</span>
                                        <span class="robotoBold"><?=$ride->getPricePerSeat()-2?> crédits / participants</span>
                                    </div>
                                </div>
                            </div>

                            <div class="rideActions">
                                <?php if ($ride->getStatus()->value == 'pending'): ?>
                                    <button class="startRideBtn robotoBold" data-ride-id="<?=$ride->getIdRidesharing()?>">
                                        <span class="material-symbols-outlined">play_arrow</span>
                                        <span>Démarrer le trajet</span>
                                    </button>
                                    <button class="cancelRideBtn robotoBold" data-ride-id="<?=$ride->getIdRidesharing()?>">
                                        <span class="material-symbols-outlined">delete</span>
                                        <span>Annuler le trajet</span>
                                    </button>
                                <?php elseif ($ride->getStatus()->value == 'ongoing'): ?>
                                    <button class="completeRideBtn robotoBold" data-ride-id="<?=$ride->getIdRidesharing()?>">
                                        <span class="material-symbols-outlined">check_circle</span>
                                        <span>Arrivée à destination</span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="emptyState">
                    <span class="material-symbols-outlined">info</span>
                    <p>Vous n'avez proposé aucun trajet pour le moment.</p>
                    <a href="/showCreateRidesharing" class="newRideBtn robotoBold">Proposer un trajet</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>