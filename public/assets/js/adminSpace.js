// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function () {

    let day = new Date();

    const ridesChart = initRidesChart();
    const creditsChart = initCreditsChart();


    // on met a jour les graphiques à chaque chargement du DOM
    updateRidesChart(ridesChart, day);  // on met a jour les graphiques à chaque chargement du DOM
    updateCreditsChart(creditsChart, day);


    // Initialiser le graphique des covoiturages
    function initRidesChart() {
        const ctx = document.getElementById('ridesChart').getContext('2d');

        // Exemple de données (vous remplacerez avec vos vraies données)
        const labels = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        let data = [12, 19, 15, 25, 22, 30, 28];

        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nombre de covoiturages',
                    data: data,
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: 'rgba(102, 126, 234)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: 'rgba(102, 126, 234)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5,
                            font: { size: 12 }
                        }
                    },
                    x: {
                        ticks: {
                            font: { size: 12 }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Initialiser le graphique des crédits
    function initCreditsChart() {
        const ctx = document.getElementById('creditsChart').getContext('2d');

        // Exemple de données (vous remplacerez avec vos vraies données)
        const labels = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        const data = [450, 680, 520, 890, 760, 1020, 950];

        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Crédits gagnés',
                    data: data,
                    backgroundColor: 'rgba(39, 174, 96, 0.8)',
                    borderColor: 'rgb(39, 174, 96)',
                    borderWidth: 2,
                    hoverBackgroundColor: 'rgb(39, 174, 96)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                return 'Crédits: ' + context.parsed.y + ' €';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return value + ' €';
                            },
                            font: { size: 12 }
                        },
                    },
                    x: {
                        ticks: {
                            font: { size: 12 }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Mettre à jour le graphique des covoiturages
    function updateRidesChart(ridesChart, date) {
        // Ici vous feriez un appel AJAX pour récupérer les nouvelles données
        fetch(`/getParticipationInfoPerWeek`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'typeRequete': 'ajax',
                'csrfToken': getToken(),
            },
            body: JSON.stringify({
                date: date,
            })
        })
            .then(response => response.json())
            .then(data => {
                ridesChart.data.labels = data.labels;
                ridesChart.data.datasets[0].data = data.values;
                ridesChart.update();
            })
            .catch(error => {
                console.error('Erreur lors du chargement des données:', error);
                alert('Erreur lors du chargement des données du graphique');
            });
    }

    // Mettre à jour le graphique des crédits
    function updateCreditsChart(creditsChart, date) {
        // Ici vous feriez un appel AJAX pour récupérer les nouvelles données
        fetch(`/getCreditInfoPerWeek`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'typeRequete': 'ajax',
                'csrfToken': getToken(),
            },
            body: JSON.stringify({
                date: date,
            })
        })
            .then(response => response.json())
            .then(data => {
                creditsChart.data.labels = data.labels;
                creditsChart.data.datasets[0].data = data.values;
                creditsChart.update();
            })
            .catch(error => {
                console.error('Erreur lors du chargement des données:', error);
                alert('Erreur lors du chargement des données du graphique');
            });
    }

    // ===== MODAL EMPLOYÉ =====

    const btnAddEmployee = document.getElementById('btnAddEmployee');
    const modalCancel = document.querySelectorAll('.modalCancel');

    btnAddEmployee.addEventListener('click', openEmployeeModal);

    modalCancel.forEach(btn => {
        btn.addEventListener('click', closeEmployeeModal)
    });

    function openEmployeeModal() {
        document.getElementById('adminModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeEmployeeModal() {
        document.getElementById('adminModal').style.display = 'none';
        document.body.style.overflow = '';
        document.getElementById('modalForm').reset();
    }

    // Gestion du formulaire d'ajout d'employé
    document.getElementById('modalForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const name = document.getElementById('employeeName').value;
        const email = document.getElementById('employeeEmail').value;
        const password = document.getElementById('employeePassword').value;

        const regexMdp = /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[\W]).{12,}$/

        // Validation par le regex
        if (!regexMdp.test(password)) {
            alert('Le mot de passe n\'est pas assez sécurisé');
            return;
        }

        // Envoi des données
        fetch('/createEmployee', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'csrfToken': getToken(),
            },
            body: JSON.stringify({
                name: name,
                email: email,
                password: password,
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Employé créé avec succès !');
                    closeEmployeeModal();
                    location.reload(); // Recharge la page pour afficher le nouvel employé
                } else {
                    alert('Erreur : ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de la création du compte');
            });
    });

    // ------ Suspension de compte ---------

    // on sélectionne les tableaux plutôt que les lignes pour évite de surcharger la mémoire si bcp de lignes.
    document.querySelectorAll('.dataTable tbody').forEach(tbody => {
        // On créer l’événement dès le click dans le tbody.
        tbody.addEventListener('click', function (e) {

            // Si on click dans le tbody on remonte jusqu'au bouton
            const btn = e.target.closest('.btnSuspend');

            // on recherche les info qui se trouve dans les balises <tr> de chaque tableau
            const userInfo = btn.closest('tr');

            // L'id est dans les dataset. 
            const userId = userInfo.dataset.userId;

            // on determine l'action de suspendre ou réactiver.
            const shouldSuspend = btn.classList.contains('active');

            suspendUser(userId, shouldSuspend);
        })
    })




    function suspendUser(id, suspend) {
        const action = suspend ? 'suspendre' : 'réactiver';
        const confirmMessage = `Êtes-vous sûr de vouloir ${action} ce compte ?`;

        if (!confirm(confirmMessage)) {
            return;
        }

        fetch('/suspendUser', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'csrfToken': getToken(),
            },
            body: JSON.stringify({
                id: id,
                suspend: suspend
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Compte ${suspend ? 'suspendu' : 'réactivé'} avec succès`);
                    location.reload();
                } else {
                    alert('Erreur : ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
    }

    // ---------- utilitaire --------
    function getToken() {
        const tokenMeta = document.querySelector("meta[name='csrfToken']");
        if (!tokenMeta) {
            console.error('Token CSRF introuvable')
            return null;
        }

        return tokenMeta.getAttribute('content');
    }


});


