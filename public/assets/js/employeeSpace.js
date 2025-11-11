document.addEventListener('DOMContentLoaded', function () {
    // Récupération des éléments du DOM
    const modal = document.getElementById('detailsModal');
    const closeModalBtn = modal.querySelector('.closeModal');

    // Variable pour stocker l'ID de l'avis actuellement affiché
    let currentReviewId = null;

    // ===== OUVERTURE DU MODAL =====
    // On récupère tous les boutons "Voir les détails"
    const detailButtons = document.querySelectorAll('.btnDetails');

    detailButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Récupération des données de l'avis stockées dans l'attribut data-review
            const reviewData = JSON.parse(this.dataset.review);

            // Stockage de l'ID pour les actions ultérieures
            currentReviewId = reviewData.id;

            // Remplissage du modal avec les données
            fillModal(reviewData);

            // Affichage du modal
            modal.classList.add('active');
        });
    });

    // ===== FERMETURE DU MODAL =====
    // Fermeture au clic sur le bouton X
    closeModalBtn.addEventListener('click', function () {
        closeModal();
    });

    // Fonction pour fermer le modal
    function closeModal() {
        modal.classList.remove('active');
        currentReviewId = null;
    }

    // ===== REMPLISSAGE DU MODAL =====
    function fillModal(data) {
        // Informations de l'avis
        document.getElementById('modalRating').textContent = `${data.rating}/5 ⭐`;
        document.getElementById('modalCreatedAt').textContent = data.createdAt;
        document.getElementById('modalComment').textContent = data.comment;

        // Informations du trajet
        document.getElementById('modalRideId').textContent = data.ride.id;
        document.getElementById('modalDepartureDate').textContent = data.ride.departureDate;

        // Construction de l'adresse de départ
        let departureLocation = data.ride.departureCity;
        if (data.ride.departureAddress) {
            departureLocation += ` - ${data.ride.departureAddress}`;
        }
        document.getElementById('modalDepartureLocation').textContent = departureLocation;

        // Date d'arrivée
        document.getElementById('modalArrivalDate').textContent =
            data.ride.arrivalDate || "Non renseignée";

        // Construction de l'adresse d'arrivée
        let arrivalLocation = data.ride.arrivalCity;
        if (data.ride.arrivalAddress) {
            arrivalLocation += ` - ${data.ride.arrivalAddress}`;
        }
        document.getElementById('modalArrivalLocation').textContent = arrivalLocation;

        // Informations du chauffeur
        document.getElementById('modalDriverPseudo').textContent = data.driver.pseudo;
        document.getElementById('modalDriverEmail').textContent = data.driver.email;
        document.getElementById('modalDriverCreatedAt').textContent = data.driver.createdAt;
        document.getElementById('modalDriverGrade').textContent =
            data.driver.grade ? `${data.driver.grade}/5 ⭐` : 'Aucune note';

        // Informations du passager
        document.getElementById('modalPassangerPseudo').textContent = data.passanger.pseudo;
        document.getElementById('modalPassangerEmail').textContent = data.passanger.email;
        document.getElementById('modalPassangerCreatedAt').textContent = data.passanger.createdAt;
    }

    // ===== GESTION DES VALIDATIONS/REJETS =====

    // Boutons dans les cartes
    document.querySelectorAll('.btnValidate').forEach(button => {
        button.addEventListener('click', function () {
            const card = this.closest('.reviewCard');
            const reviewId = card.querySelector('.reviewId').value;
            approvedReview(currentReviewId, card);
        });
    });

    document.querySelectorAll('.btnReject').forEach(button => {
        button.addEventListener('click', function () {
            const card = this.closest('.reviewCard');
            const reviewId = card.querySelector('.reviewId').value;
            rejectReview(currentReviewId, card);
        });
    });

    // Boutons dans le modal
    document.querySelector('.btnValidateModal').addEventListener('click', function () {
        if (currentReviewId) {
            const card = document.querySelector(`[data-review-id="${currentReviewId}"]`);
            approvedReview(currentReviewId, card);
        }
    });

    document.querySelector('.btnRejectModal').addEventListener('click', function () {
        if (currentReviewId) {
            const card = document.querySelector(`[data-review-id="${currentReviewId}"]`);
            rejectReview(currentReviewId, card);
        }
    });

    // ===== FONCTION PRINCIPALE DE TRAITEMENT =====


    function approvedReview(reviewId, cardElement) {
        // Confirmation de l'action
        const confirmMessage = `Êtes-vous sûr de vouloir approuver cet avis ?`;

        if (!confirm(confirmMessage)) {
            return;
        }

        // Désactivation de tous les boutons pendant le traitement
        disableAllButtons(cardElement);

        // Envoi de la requête au serveur
        fetch('/approvedReview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'csrfToken': getCsrfToken(),
                'typeRequete': 'ajax'
            },
            body: JSON.stringify({
                reviewId: currentReviewId,
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Succès : on ferme le modal et on supprime la carte
                    closeModal();

                    setTimeout(() => {

                        // Mise à jour des statistiques
                        updateStats('approved');

                        // Vérification s'il reste des avis
                        checkIfEmpty();

                        // Message de succès
                        alert(`Avis approuver avec succès !`);
                    }, 300);
                } else {
                    // Erreur : on réactive les boutons et on affiche le message
                    enableAllButtons(cardElement);
                    alert(data.message || 'Une erreur est survenue');
                }
            })
            .catch(error => {
                // Erreur réseau : on réactive les boutons
                console.error('Erreur:', error);
                enableAllButtons(cardElement);
                alert('Une erreur est survenue lors de la communication avec le serveur');
            });
    }

    function rejectReview(reviewId, cardElement) {
        // Confirmation de l'action
        const reasonOfReject = prompt('Veuillez donner une raison à ce rejet.');

        if (!reasonOfReject || reasonOfReject.trim() === '') {
            alert('Le rejet nécessite une raison');
            return;
        }

        // Désactivation de tous les boutons pendant le traitement
        disableAllButtons(cardElement);

        // Envoi de la requête au serveur
        fetch('/rejectReview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'csrfToken': getCsrfToken(),
                'typeRequete': 'ajax'
            },
            body: JSON.stringify({
                reviewId: currentReviewId,
                reasonOfReject: reasonOfReject
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Succès : on ferme le modal et on supprime la carte
                    closeModal();

                    setTimeout(() => {

                        // Vérification s'il reste des avis
                        checkIfEmpty();

                        // Message de succès
                        alert(`Avis rejeter avec succès !`);
                    }, 300);
                } else {
                    // Erreur : on réactive les boutons et on affiche le message
                    enableAllButtons(cardElement);
                    alert(data.message || 'Une erreur est survenue');
                }
            })
            .catch(error => {
                // Erreur réseau : on réactive les boutons
                console.error('Erreur:', error);
                enableAllButtons(cardElement);
                alert('Une erreur est survenue lors de la communication avec le serveur');
            });
    }

    // ===== FONCTIONS UTILITAIRES =====

    // Désactivation des boutons
    function disableAllButtons(cardElement) {
        const buttons = cardElement.querySelectorAll('button');
        buttons.forEach(btn => {
            btn.disabled = true;
            btn.style.opacity = '0.5';
        });

        // Désactivation aussi des boutons du modal si ouvert
        if (modal.classList.contains('active')) {
            modal.querySelectorAll('button').forEach(btn => {
                if (!btn.classList.contains('closeModal')) {
                    btn.disabled = true;
                    btn.style.opacity = '0.5';
                }
            });
        }
    }

    // Réactivation des boutons
    function enableAllButtons(cardElement) {
        const buttons = cardElement.querySelectorAll('button');
        buttons.forEach(btn => {
            btn.disabled = false;
            btn.style.opacity = '1';
        });

        if (modal.classList.contains('active')) {
            modal.querySelectorAll('button').forEach(btn => {
                btn.disabled = false;
                btn.style.opacity = '1';
            });
        }
    }

    // Mise à jour des compteurs de statistiques
    function updateStats(action) {
        const pendingCount = document.getElementById('pendingCount');
        const processedCount = document.getElementById('processedCount');

        // Diminution du nombre d'avis en attente
        const currentPending = parseInt(pendingCount.textContent);
        pendingCount.textContent = Math.max(0, currentPending - 1);

        // Si l'avis a été validé, augmentation du compteur traités
        if (action === 'validate') {
            const currentProcessed = parseInt(processedCount.textContent);
            processedCount.textContent = currentProcessed + 1;
        }
    }

    // Vérification si la liste est vide
    function checkIfEmpty() {
        const container = document.querySelector('.reviewsListContainer');
        const remainingCards = container.querySelectorAll('.reviewCard');

        if (remainingCards.length === 0) {
            container.innerHTML = `
                <div class="emptyState">
                    <span class="material-symbols-outlined">inbox</span>
                    <p>Aucun avis en attente pour le moment</p>
                </div>
            `;
        }
    }

    // Récupération du token CSRF
    function getCsrfToken() {
        const tokenMeta = document.querySelector("meta[name='csrfToken']");
        if (!tokenMeta) {
            console.error('Token CSRF introuvable');
            return null;
        }
        return tokenMeta.getAttribute('content');
    }
});
