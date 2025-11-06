<section id="employeeSpace" class="employeeSpace">
    <!-- En-tête avec statistiques -->
    <div class="employeeHeader">
        <h2 class="montserratBold titleColor">Gestion des Avis</h2>
        <div class="statsCards">
            <div class="statCard pending">
                <span class="material-symbols-outlined">pending_actions</span>
                <div class="statInfo">
                    <p class="statValue robotoBold"><?= $pendingReviewsCount ?></p>
                    <p class="statLabel">Avis à traiter</p>
                </div>
            </div>
            <div class="statCard processed">
                <span class="material-symbols-outlined">task_alt</span>
                <div class="statInfo">
                    <p class="statValue robotoBold"><?= $processedTodayCount ?></p>
                    <p class="statLabel">Traités aujourd'hui</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des avis -->
    <div class="reviewsContainer">
        <div class="reviewsList">
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="reviewItem <?= $review['status'] ?>" 
                         data-review-id="<?= $review['id'] ?>"
                         data-driver-id="<?= $review['driverId'] ?>"
                         data-passenger-id="<?= $review['passengerId'] ?>">
                        
                        <!-- Indicateur de statut -->
                        <div class="reviewStatus">
                            <?php if ($review['status'] === 'pending'): ?>
                                <span class="statusIndicator pending"></span>
                            <?php else: ?>
                                <span class="statusIndicator processed"></span>
                            <?php endif; ?>
                        </div>

                        <!-- Contenu de l'avis -->
                        <div class="reviewContent">
                            <!-- Informations du trajet -->
                            <div class="tripInfo">
                                <div class="tripDate">
                                    <span class="material-symbols-outlined">calendar_today</span>
                                    <span class="robotoBold"><?= $review['rideDate']->format('d/m/Y') ?></span>
                                </div>
                                <div class="tripRoute">
                                    <div class="routePoint">
                                        <span class="material-symbols-outlined">trip_origin</span>
                                        <div class="locationInfo">
                                            <p class="robotoBold"><?= htmlspecialchars($review['departureCity']) ?></p>
                                            <?php if (!empty($review['departureAddress'])): ?>
                                                <p class="addressDetail"><?= htmlspecialchars($review['departureAddress']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="routeArrow">
                                        <span class="material-symbols-outlined">arrow_forward</span>
                                    </div>
                                    <div class="routePoint">
                                        <span class="material-symbols-outlined">location_on</span>
                                        <div class="locationInfo">
                                            <p class="robotoBold"><?= htmlspecialchars($review['arrivalCity']) ?></p>
                                            <?php if (!empty($review['arrivalAddress'])): ?>
                                                <p class="addressDetail"><?= htmlspecialchars($review['arrivalAddress']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Informations de l'avis -->
                            <div class="reviewInfo">
                                <div class="reviewHeader">
                                    <div class="driverInfo">
                                        <span class="material-symbols-outlined">person</span>
                                        <span class="robotoBold">Chauffeur : <?= htmlspecialchars($review['driverPseudo']) ?></span>
                                    </div>
                                    <div class="reviewRating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star <?= $i <= $review['rating'] ? 'filled' : '' ?>">★</span>
                                        <?php endfor; ?>
                                        <span class="ratingValue">(<?= $review['rating'] ?>/5)</span>
                                    </div>
                                </div>
                                
                                <div class="reviewText">
                                    <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                                </div>

                                <div class="reviewMeta">
                                    <span class="reviewDate">
                                        <span class="material-symbols-outlined">schedule</span>
                                        Publié le <?= $review['createdAt']->format('d/m/Y à H:i') ?>
                                    </span>
                                    <?php if ($review['status'] === 'processed'): ?>
                                        <span class="processedBy">
                                            <span class="material-symbols-outlined">verified</span>
                                            Traité par <?= htmlspecialchars($review['processedBy']) ?> 
                                            le <?= $review['processedAt']->format('d/m/Y à H:i') ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="reviewActions">
                                <button class="detailsBtn robotoBold" data-review-id="<?= $review['id'] ?>">
                                    <span class="material-symbols-outlined">visibility</span>
                                    <span>Voir les détails</span>
                                </button>
                                <?php if ($review['status'] === 'pending'): ?>
                                    <button class="validateBtn robotoBold" data-review-id="<?= $review['id'] ?>">
                                        <span class="material-symbols-outlined">check_circle</span>
                                        <span>Valider</span>
                                    </button>
                                    <button class="rejectBtn robotoBold" data-review-id="<?= $review['id'] ?>">
                                        <span class="material-symbols-outlined">cancel</span>
                                        <span>Rejeter</span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="emptyState">
                    <span class="material-symbols-outlined">inbox</span>
                    <p>Aucun avis à afficher pour le moment</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Panneau de détails (initialement caché) -->
        <div id="detailsPanel" class="detailsPanel">
            <div class="detailsHeader">
                <h3 class="montserratBold">Détails complets</h3>
                <button id="closeDetailsBtn" class="closeDetailsBtn">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="detailsContent">
                <!-- Informations du chauffeur -->
                <div class="userDetails driverDetails">
                    <h4 class="robotoBold titleColor">
                        <span class="material-symbols-outlined">drive_eta</span>
                        Informations du chauffeur
                    </h4>
                    <div class="userInfo">
                        <div class="infoItem">
                            <span class="label">Pseudo :</span>
                            <span class="value" id="driverPseudo">-</span>
                        </div>
                        <div class="infoItem">
                            <span class="label">Email :</span>
                            <span class="value" id="driverEmail">-</span>
                        </div>
                        <div class="infoItem">
                            <span class="label">Téléphone :</span>
                            <span class="value" id="driverPhone">-</span>
                        </div>
                        <div class="infoItem">
                            <span class="label">Membre depuis :</span>
                            <span class="value" id="driverMemberSince">-</span>
                        </div>
                        <div class="infoItem">
                            <span class="label">Trajets effectués :</span>
                            <span class="value" id="driverTripsCount">-</span>
                        </div>
                        <div class="infoItem">
                            <span class="label">Note moyenne :</span>
                            <span class="value" id="driverRating">-</span>
                        </div>
                        <div class="infoItem">
                            <span class="label">Nombre d'avis :</span>
                            <span class="value" id="driverReviewsCount">-</span>
                        </div>
                        <div class="infoItem fullWidth">
                            <span class="label">Statut du compte :</span>
                            <span class="value statusBadge" id="driverStatus">-</span>
                        </div>
                    </div>
                </div>

                <!-- Informations du passager -->
                <div class="userDetails passengerDetails">
                    <h4 class="robotoBold titleColor">
                        <span class="material-symbols-outlined">person</span>
                        Informations du passager
                    </h4>
                    <div class="userInfo">
                        <div class="infoItem">
                            <span class="label">Pseudo :</span>
                            <span class="value" id="passengerPseudo">-</span>
                        </div>
                        <div class="infoItem">
                            <span class="label">Email :</span>
                            <span class="value" id="passengerEmail">-</span>
                        </div>
                        <div class="infoItem">
                            <span class="label">Téléphone :</span>
                            <span class="value" id="passengerPhone">-</span>
                        </div>
                        <div class="infoItem">
                            <span class="label">Membre depuis :</span>
                            <span class="value" id="passengerMemberSince">-</span>
                        </div>
                        <div class="infoItem">
                            <span class="label">Trajets effectués :</span>
                            <span class="value" id="passengerTripsCount">-</span>
                        </div>
                        <div class="infoItem">
                            <span class="label">Avis laissés :</span>
                            <span class="value" id="passengerReviewsGiven">-</span>
                        </div>
                        <div class="infoItem fullWidth">
                            <span class="label">Statut du compte :</span>
                            <span class="value statusBadge" id="passengerStatus">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions sur l'avis depuis les détails -->
            <div class="detailsActions" id="detailsActions">
                <!-- Boutons ajoutés dynamiquement si avis en attente -->
            </div>
        </div>
    </div>
</section>