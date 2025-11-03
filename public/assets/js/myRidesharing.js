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
        console.log('🚀 Début de la requête, ID:', participateId);

        fetch(`/cancelParticipation/${participateId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'csrfToken': getToken(),
                'typeRequete': 'ajax'
            }
        })
            .then(response => {
                console.log('📊 Status HTTP:', response.status);
                console.log('📋 Headers:', [...response.headers.entries()]);

                // ⚠️ On lit d'abord en TEXT (pas JSON)
                return response.text();
            })
            .then(text => {
                console.log('📄 RÉPONSE BRUTE (200 premiers caractères):');
                console.log(text.substring(0, 200));
                console.log('📄 RÉPONSE COMPLÈTE:');
                console.log(text);

                // Maintenant on essaie de parser en JSON
                try {
                    const data = JSON.parse(text);
                    console.log('✅ JSON parsé avec succès:', data);

                    if (data.success) {
                        alert('Votre participation a été annulée avec succès.');
                        location.reload();
                    } else {
                        alert(data.message || 'Une erreur est survenue.');
                    }
                } catch (error) {
                    console.error('❌ Impossible de parser en JSON');
                    console.error('Erreur:', error);
                    alert('Erreur technique : la réponse n\'est pas au bon format.');
                }
            })
            .catch(error => {
                console.error('💥 Erreur réseau:', error);
                alert('Une erreur réseau est survenue.');
            });
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
});

// Fonction pour démarrer un trajet
function startRide(rideId) {
    fetch(`/startRide/${rideId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
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