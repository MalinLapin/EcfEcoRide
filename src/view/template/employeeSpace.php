<section id="employeeSpace" class="employeeSpace">
    <!-- En-tête avec statistiques -->
    <div class="employeeHeader">
        <h2 class="montserratBold titleColor">Gestion des Avis</h2>
        <div class="statsCards">
            <div class="statCard pending">
                <span class="material-symbols-outlined">pending_actions</span>
                <div class="statInfo">
                    <p class="statValue robotoBold" id="pendingCount"><?= $countReviewPending ?></p>
                    <p class="statLabel">Avis à traiter</p>
                </div>
            </div>
            <div class="statCard processed">
                <span class="material-symbols-outlined">task_alt</span>
                <div class="statInfo">
                    <p class="statValue robotoBold" id="processedCount"><?= $countReviewApproved ?? 0 ?></p>
                    <p class="statLabel">Traités aujourd'hui</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des avis avec scroll -->
    <div class="reviewsListContainer">
    <?php if (!empty($reviewsInfo)): ?>
        <?php foreach ($reviewsInfo as $reviewInfo):
            $review = $reviewInfo['review'];
            $ride = $reviewInfo['ride'];
            $driver = $reviewInfo['driver'];
            $passanger = $reviewInfo['passanger'];?>
            
            <div class="reviewCard" data-review-id="<?= $review->getId() ?>">
                
                <!-- Informations du trajet -->
                <div class="rideSection">
                    <div class="tripDate">
                        <span class="material-symbols-outlined">calendar_today</span>
                        <span class="robotoBold"><?= $ride->getDepartureDate()->format('d/m/Y H:i') ?></span>
                    </div>
                    
                    <div class="rideRoute">
                        <!-- Point de départ -->
                        <div class="ridePoint">
                            <span class="material-symbols-outlined">trip_origin</span>
                            <div>
                                <p class="robotoBold"><?= htmlspecialchars($ride->getDepartureCity()) ?></p>
                                <?php if ($ride->getDepartureAddress()): ?>
                                    <p class="address"><?= htmlspecialchars($ride->getDepartureAddress()) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        
                        <div class="arrow">
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </div>
                        
                        <!-- Point d'arrivée -->
                        <div class="ridePoint">
                            <span class="material-symbols-outlined">location_on</span>
                            <div>
                                <p class="robotoBold"><?= htmlspecialchars($ride->getArrivalCity()) ?></p>
                                <?php if ($ride->getArrivalAddress()): ?>
                                    <p class="address"><?= htmlspecialchars($ride->getArrivalAddress()) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Avis -->
                <div class="reviewSection">
                    <div class="reviewHeader">
                        <!-- Info chauffeur -->
                        <div class="driverInfo">
                            <span class="material-symbols-outlined">person</span>
                            <span class="robotoBold"><?= htmlspecialchars($driver->getPseudo()) ?></span>
                        </div>
                        
                        <!-- Note -->
                        <div class="rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?= $i <= $review->getRating() ? 'filled' : '' ?>">★</span>
                            <?php endfor; ?>
                            <span class="ratingNumber"><?= $review->getRating() ?>/5</span>
                        </div>
                    </div>
                    
                    <!-- Commentaire -->
                    <div class="reviewComment">
                        <p><?= htmlspecialchars($review->getComment()) ?></p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="actionsSection">
                    <button class="btnDetails robotoBold" 
                            data-review='<?= json_encode([
                                "id" => $review->getId(),
                                "rating" => $review->getRating(),
                                "comment" => htmlspecialchars($review->getComment()),
                                "createdAt" => $review->getCreatedAt()->format('d/m/Y H:i'),
                                "driver" => [
                                    "pseudo" => htmlspecialchars($driver->getPseudo()),
                                    "email" => htmlspecialchars($driver->getEmail()),
                                    "createdAt" => $driver->getCreatedAt()->format('d/m/Y'),
                                    "grade" => $driver->getGrade()
                                ],
                                "passanger" => [
                                    "pseudo" => htmlspecialchars($passanger->getPseudo()),
                                    "email" => htmlspecialchars($passanger->getEmail()),
                                    "createdAt" => $passanger->getCreatedAt()->format('d/m/Y')
                                ],
                                "ride" => [
                                    "id" => $ride->getIdRidesharing(),
                                    "departureCity" => htmlspecialchars($ride->getDepartureCity()),
                                    "departureAddress" => $ride->getDepartureAddress() ? htmlspecialchars($ride->getDepartureAddress()) : null,
                                    "departureDate" => $ride->getDepartureDate()->format('d/m/Y H:i'),
                                    "arrivalCity" => htmlspecialchars($ride->getArrivalCity()),
                                    "arrivalAddress" => $ride->getArrivalAddress() ? htmlspecialchars($ride->getArrivalAddress()) : null,
                                    "arrivalDate" => $ride->getArrivalDate() ? $ride->getArrivalDate()->format('d/m/Y H:i') : null
                                ]
                            ])?>'>
                        <span class="material-symbols-outlined">visibility</span>
                        Détails
                    </button>
                    
                    <input type="hidden" class="reviewId" value="<?= $review->getId() ?>">
                </div>
                
            </div>
        <?php endforeach; ?>
        
    <?php else: ?>
        <div class="emptyState">
            <span class="material-symbols-outlined">inbox</span>
            <p>Aucun avis en attente pour le moment</p>
        </div>
    <?php endif; ?>
</div>
</section>

<!-- Modal de détails (caché par défaut) -->
<div id="detailsModal" class="modal">
    <div class="modalContent">
        <div class="modalHeader">
            <h3 class="montserratBold titleColor">Détails complets de l'avis</h3>
            <button class="closeModal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="modalBody">
            <!-- Informations de l'avis -->
            <div class="modalDiv">
                <h4 class="robotoBold titleColor">
                    <span class="material-symbols-outlined">rate_review</span>
                    L'avis
                </h4>
                <div class="infoGrid">
                    <div class="infoItem">
                        <span class="label">Note :</span>
                        <span class="value" id="modalRating">-</span>
                    </div>
                    <div class="infoItem">
                        <span class="label">Publié le :</span>
                        <span class="value" id="modalCreatedAt">-</span>
                    </div>
                    <div class="infoItem fullWidth">
                        <span class="label">Commentaire :</span>
                        <div class="commentBox" id="modalComment">-</div>
                    </div>
                </div>
            </div>

            <!-- Informations du trajet -->
            <div class="modalDiv">
                <h4 class="robotoBold titleColor">
                    <span class="material-symbols-outlined">road</span>
                    Le trajet
                </h4>
                <div class="modalInfoDiv">
                    <div class="infoItem">
                        <span class="label">N° de trajet :</span>
                        <span class="value" id="modalRideId">-</span>
                    </div>
                    <div class="infoItem">
                        <span class="label">Date de départ :</span>
                        <span class="value" id="modalDepartureDate">-</span>
                    </div>
                    <div class="infoItem fullWidth">
                        <span class="label">Départ :</span>
                        <span class="value" id="modalDepartureLocation">-</span>
                    </div>
                    <div class="infoItem">
                        <span class="label">Date d'arrivée :</span>
                        <span class="value" id="modalArrivalDate">-</span>
                    </div>
                    <div class="infoItem fullWidth">
                        <span class="label">Arrivée :</span>
                        <span class="value" id="modalArrivalLocation">-</span>
                    </div>
                </div>
            </div>

            <!-- Informations du chauffeur -->
            <div class="modalDiv">
                <h4 class="robotoBold titleColor">
                    <span class="material-symbols-outlined">drive_eta</span>
                    Le chauffeur
                </h4>
                <div class="modalInfoDiv">
                    <div class="infoItem">
                        <span class="label">Pseudo :</span>
                        <span class="value" id="modalDriverPseudo">-</span>
                    </div>
                    <div class="infoItem">
                        <span class="label">Email :</span>
                        <span class="value" id="modalDriverEmail">-</span>
                    </div>
                    <div class="infoItem">
                        <span class="label">Membre depuis :</span>
                        <span class="value" id="modalDriverCreatedAt">-</span>
                    </div>
                    <div class="infoItem">
                        <span class="label">Note moyenne :</span>
                        <span class="value" id="modalDriverGrade">-</span>
                    </div>
                </div>
            </div>

            <!-- Informations du passager -->
            <div class="section">
                <h4 class="robotoBold titleColor">
                    <span class="material-symbols-outlined">person</span>
                    Le passager
                </h4>
                <div class="modalInfoDiv">
                    <div class="infoItem">
                        <span class="label">Pseudo :</span>
                        <span class="value" id="modalPassangerPseudo">-</span>
                    </div>
                    <div class="infoItem">
                        <span class="label">Email :</span>
                        <span class="value" id="modalPassangerEmail">-</span>
                    </div>
                    <div class="infoItem">
                        <span class="label">Membre depuis :</span>
                        <span class="value" id="modalPassangerCreatedAt">-</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="modalFooter">
            <button class="btnValidateModal robotoBold">
                <span class="material-symbols-outlined">check_circle</span>
                Valider l'avis
            </button>
            <button class="btnRejectModal robotoBold">
                <span class="material-symbols-outlined">cancel</span>
                Rejeter l'avis
            </button>
        </div>
    </div>
</div>
