document.addEventListener('DOMContentLoaded', function () {
    // Variables globales
    const detailsPanel = document.getElementById('detailsPanel');
    const closeDetailsBtn = document.getElementById('closeDetailsBtn');
    const detailsActions = document.getElementById('detailsActions');
    let currentReviewId = null;

    // Gestion des boutons de détails
    document.querySelectorAll('.detailsBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const reviewId = this.dataset.reviewId;
            const reviewItem = this.closest('.reviewItem');
            const driverId = reviewItem.dataset.driverId;
            const passengerId = reviewItem.dataset.passengerId;

            currentReviewId = reviewId;
            loadUserDetails(driverId, passengerId, reviewItem.classList.contains('pending'));
        });
    });

    // Fermeture du panneau de détails
    closeDetailsBtn.addEventListener('click', closeDetailsPanel);

    // Fermeture au clic sur l'overlay (mobile)
    detailsPanel.addEventListener('click', function (e) {
        if (e.target === this) {
            closeDetailsPanel();
        }
    });

    // Fonction pour fermer le panneau
    function closeDetailsPanel() {
        detailsPanel.classList.remove('active');
        currentReviewId = null;
        detailsActions.innerHTML = '';
    }

    // Fonction pour charger les détails des utilisateurs
    function loadUserDetails(driverId, passengerId, isPending) {
        fetch('/getReviewDetails', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'csrfToken': getToken(),
                'typeRequete': 'ajax'
            },
            body: JSON.stringify({
                driverId: parseInt(driverId),
                passengerId: parseInt(passengerId)
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayUserDetails(data.driver, data.passenger);
                    detailsPanel.classList.add('active');

                    // Ajouter les boutons d'action si l'avis est en attente
                    if (isPending) {
                        displayDetailsActions();
                    }
                } else {
                    alert('Erreur lors du chargement des détails');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
    }

    // Fonction pour afficher les détails des utilisateurs
    function displayUserDetails(driver, passenger) {
        // Détails du chauffeur
        document.getElementById('driverPseudo').textContent = driver.pseudo;
        document.getElementById('driverEmail').textContent = driver.email;
        document.getElementById('driverPhone').textContent = driver.phone || 'Non renseigné';
        document.getElementById('driverMemberSince').textContent = new Date(driver.createdAt).toLocaleDateString('fr-FR');
        document.getElementById('driverTripsCount').textContent = driver.tripsCount;
        document.getElementById('driverRating').textContent = driver.averageRating ? `${driver.averageRating}/5 ⭐` : 'Aucune note';
        document.getElementById('driverReviewsCount').textContent = driver.reviewsCount;

        const driverStatus = document.getElementById('driverStatus');
        driverStatus.textContent = driver.status === 'active' ? 'Actif' : 'Suspendu';
        driverStatus.className = 'value statusBadge ' + driver.status;

        // Détails du passager
        document.getElementById('passengerPseudo').textContent = passenger.pseudo;
        document.getElementById('passengerEmail').textContent = passenger.email;
        document.getElementById('passengerPhone').textContent = passenger.phone || 'Non renseigné';
        document.getElementById('passengerMemberSince').textContent = new Date(passenger.createdAt).toLocaleDateString('fr-FR');
        document.getElementById('passengerTripsCount').textContent = passenger.tripsCount;
        document.getElementById('passengerReviewsGiven').textContent = passenger.reviewsGiven;

        const passengerStatus = document.getElementById('passengerStatus');
        passengerStatus.textContent = passenger.status === 'active' ? 'Actif' : 'Suspendu';
        passengerStatus.className = 'value statusBadge ' + passenger.status;
    }

    // Fonction pour afficher les boutons d'action dans les détails
    function displayDetailsActions() {
        detailsActions.innerHTML = `
        <button class="validateBtn robotoBold" onclick="processReview(${currentReviewId}, 'validate')">
            <span class="material-symbols-outlined">check_circle</span>
            <span>Valider</span>
        </button>
        <button class="rejectBtn robotoBold" onclick="processReview(${currentReviewId}, 'reject')">
            <span class="material-symbols-outlined">cancel</span>
            <span>Rejeter</span>
        </button>
    `;
    }

    // Gestion des boutons de validation/rejet dans la liste
    document.querySelectorAll('.validateBtn, .rejectBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const reviewId = this.dataset.reviewId;
            const action = this.classList.contains('validateBtn') ? 'validate' : 'reject';

            if (confirm(`Êtes-vous sûr de vouloir ${action === 'validate' ? 'valider' : 'rejeter'} cet avis ?`)) {
                processReview(reviewId, action);
            }
        });
    });

    // Fonction pour traiter un avis (validation ou rejet)
    function processReview(reviewId, action) {
        const btn = document.querySelector(`button[data-review-id="${reviewId}"]`);
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Traitement...';
        }

        fetch('/processReview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'csrfToken': getToken(),
                'typeRequete': 'ajax'
            },
            body: JSON.stringify({
                reviewId: parseInt(reviewId),
                action: action
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Avis ${action === 'validate' ? 'validé' : 'rejeté'} avec succès`);
                    location.reload();
                } else {
                    alert(data.message || 'Une erreur est survenue');
                    if (btn) {
                        btn.disabled = false;
                        btn.textContent = action === 'validate' ? 'Valider' : 'Rejeter';
                    }
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = action === 'validate' ? 'Valider' : 'Rejeter';
                }
            });
    }

    function getToken() {
        const tokenMeta = document.querySelector("meta[name='csrfToken']");
        if (!tokenMeta) {
            console.error('Token CSRF introuvable')
            return null;
        }

        return tokenMeta.getAttribute('content');
    }
});