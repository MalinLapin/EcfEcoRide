document.addEventListener('DOMContentLoaded', function () {

    // On recupère les éléments par leur Id
    const participatedTab = document.getElementById('participatedTab');
    const proposedTab = document.getElementById('proposedTab');
    const participatedRides = document.getElementById('participatedRides');
    const proposedRides = document.getElementById('proposedRides');

    // Gestion des onglets

    //  Selon sur quelle onglet on click, on ajoute et supprime des classe à l'élément pour dynamiser son Css.
    participatedTab.addEventListener('click', function () {
        participatedTab.classList.add('activeTab');
        proposedTab.classList.remove('activeTab');
        participatedRides.classList.add('activeContent');
        proposedRides.classList.remove('activeContent');
    });

    proposedTab.addEventListener('click', function () {
        proposedTab.classList.add('activeTab');
        participatedTab.classList.remove('activeTab');
        proposedRides.classList.add('activeContent');
        participatedRides.classList.remove('activeContent');
    });


    function getToken() {
        const tokenMeta = document.querySelector("meta[name='csrfToken']");
        if (!tokenMeta) {
            console.error('Token CSRF introuvable')
            return null;
        }

        return tokenMeta.getAttribute('content');
    }

    // --------------------- Partie passager--------------------------------------------


    // Gestion annulation participation
    // On selectionne tous les éléments dont la class est "cancelParticipationBtn"
    const cancelParticipationBtns = document.querySelectorAll('.cancelParticipationBtn');

    //On boucle sur chaque bouton pour ajouter un evenement.
    cancelParticipationBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const participateId = this.dataset.participateId;
            if (confirm('Êtes-vous sûr de vouloir annuler votre participation à ce trajet ?')) {
                cancelParticipation(participateId);
            }
        });
    });

    // Fonction pour annuler une participation
    function cancelParticipation(participateId) {
        fetch(`/cancelParticipation/${participateId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'csrfToken': getToken(),
                'typeRequete': 'ajax'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Votre participation a été annulée avec succès.');
                    location.reload();
                } else {
                    alert('Une erreur est survenue lors de l\'annulation de votre participation.');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue.');
            });
    }
    // --------------------- Partie avis sur le chauffeur ------------------------------------

    // Gestion du modal d'avis
    const reviewModal = document.getElementById('reviewModal');
    const closeReviewModal = document.getElementById('closeReviewModal');
    const cancelReviewBtn = document.getElementById('cancelReviewBtn');
    const reviewForm = document.getElementById('reviewForm');
    const reviewErrorMsg = document.getElementById('reviewErrorMsg');
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating');
    const commentTextarea = document.getElementById('comment');
    const charCount = document.querySelector('.charCount');

    let idDriver = null;
    let idParticipation = null;

    // Ouvrir le modal d'avis
    const letReviewBtns = document.querySelectorAll('.letReviewBtn');
    letReviewBtns.forEach(btn => {
        btn.addEventListener('click', function () {

            // On récupère les données du bouton
            const idParticipation = this.dataset.participateId;
            const idDriver = this.dataset.driverId;

            // On stock dans le modal
            reviewModal.dataset.idParticipation = idParticipation;
            reviewModal.dataset.idDriver = idDriver;

            reviewModal.classList.add('show');
            resetReviewForm();
        });
    });

    // Fermer le modal
    function closeReviewModalFunc() {
        reviewModal.classList.remove('show');
        resetReviewForm();
    }

    closeReviewModal.addEventListener('click', closeReviewModalFunc);
    cancelReviewBtn.addEventListener('click', closeReviewModalFunc);

    // Fermer en cliquant en dehors
    reviewModal.addEventListener('click', function (e) {
        if (e.target === reviewModal) {
            closeReviewModalFunc();
        }
    });

    // Gestion des étoiles
    stars.forEach(star => {
        star.addEventListener('click', function () {
            const rating = this.dataset.rating;
            ratingInput.value = rating;

            // Mettre à jour l'affichage des étoiles
            stars.forEach(s => {
                if (s.dataset.rating <= rating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });
    });

    // Soumission du formulaire d'avis
    reviewForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const rating = parseInt(ratingInput.value);
        const comment = commentTextarea.value.trim();
        const idDriver = reviewModal.dataset.idDriver;
        const idParticipation = reviewModal.dataset.idParticipation;


        // Validation
        if (rating === 0) {
            showReviewError('Veuillez sélectionner une note');
            return;
        }

        if (comment.length > 500) {
            showReviewError('Le commentaire ne peut pas dépasser 500 caractères');
            return;
        }

        // Envoi de l'avis
        submitReview(idDriver, idParticipation, rating, comment);
    });

    // Fonction pour envoyer l'avis
    function submitReview(idDriver, idParticipation, rating, comment) {
        fetch('/letReview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'csrfToken': getToken(),
                'typeRequete': 'ajax'
            },
            body: JSON.stringify({
                idDriver: parseInt(idDriver),
                idParticipation: parseInt(idParticipation),
                rating: parseInt(rating),
                comment: comment,
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Votre avis a été envoyé avec succès !');
                    closeReviewModalFunc();
                    location.reload();
                } else {
                    showReviewError(data.message || 'Une erreur est survenue lors de l\'envoi de votre avis.');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showReviewError('Une erreur est survenue. Veuillez réessayer.');
            });
    }

    // Fonction pour afficher une erreur
    function showReviewError(message) {
        reviewErrorMsg.textContent = message;
        reviewErrorMsg.classList.add('show');
        setTimeout(() => {
            reviewErrorMsg.classList.remove('show');
        }, 5000);
    }

    // Fonction pour réinitialiser le formulaire
    function resetReviewForm() {
        reviewForm.reset();
        ratingInput.value = '0';
        stars.forEach(s => s.classList.remove('active'));
        charCount.textContent = '0/500 caractères';
        reviewErrorMsg.classList.remove('show');
    }

    // --------------------- Partie conducteur--------------------------------------------


    // Gestion démarrage trajet
    const startRideBtns = document.querySelectorAll('.startRideBtn');
    startRideBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const rideId = this.dataset.rideId;
            if (confirm('Voulez-vous démarrer ce trajet ? Le statut passera à "En cours".')) {
                startRide(rideId);
            }
        });
    });

    // Gestion fin de trajet
    const completeRideBtns = document.querySelectorAll('.completeRideBtn');
    completeRideBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const rideId = this.dataset.rideId;
            if (confirm('Êtes-vous arrivé à destination ? Le trajet sera marqué comme effectué.')) {
                completeRide(rideId);
            }
        });
    });

    // Gestion annulation trajet
    const cancelRideBtns = document.querySelectorAll('.cancelRideBtn');
    cancelRideBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const rideId = this.dataset.rideId;
            if (confirm('Êtes-vous sûr de vouloir annuler ce trajet ? Cette action est irréversible.')) {
                cancelRide(rideId);
            }
        });
    });

    // Fonction pour démarrer un trajet
    function startRide(rideId) {
        fetch(`/startRide/${rideId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'csrfToken': getToken(),
                'typeRequete': 'ajax'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Le trajet a démarré avec succès.');
                    location.reload();
                } else {
                    alert('Une erreur est survenue lors du démarrage du trajet.');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue.');
            });
    }

    // Fonction pour terminer un trajet
    function completeRide(rideId) {
        fetch(`/completeRide/${rideId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'csrfToken': getToken(),
                'typeRequete': 'ajax'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Le trajet a été marqué comme effectué.');
                    location.reload();
                } else {
                    alert('Une erreur est survenue lors de la validation du trajet.');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue.');
            });
    }

    // Fonction pour annuler un trajet
    function cancelRide(rideId) {
        fetch(`/cancelRide/${rideId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'csrfToken': getToken(),
                'typeRequete': 'ajax'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Le trajet a été annulé avec succès.');
                    location.reload();
                } else {
                    alert('Une erreur est survenue lors de l\'annulation du trajet.');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue.');
            });
    }
});

