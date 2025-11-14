document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalAddCar');
    const openModalBtn = document.getElementById('openAddCarModal');
    const closeModalBtn = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const form = document.getElementById('formAddCar');
    const errorMsg = document.getElementById('errorMsg');

    // Ouvrir le modal
    openModalBtn.addEventListener('click', function () {
        modal.classList.add('show');
    });

    // Fermer le modal
    function closeModal() {
        modal.classList.remove('show');
        form.reset();
        errorMsg.classList.remove('show');
    }

    closeModalBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    // Fermer en cliquant en dehors
    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    // Soumission du formulaire
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = {
            brandId: document.getElementById('brandId').value,
            model: document.getElementById('model').value,
            registrationNumber: document.getElementById('registrationNumber').value,
            firstRegistration: document.getElementById('firstRegistration').value,
            energyType: document.getElementById('energyType').value,
            color: document.getElementById('color').value
        };

        // Validation date
        const registrationDate = new Date(formData.firstRegistration);
        const today = new Date();
        if (registrationDate > today) {
            showError('La date de mise en circulation ne peut pas être dans le futur');
            return;
        }

        function getToken() {
            const tokenMeta = document.querySelector("meta[name='csrfToken']");
            if (!tokenMeta) {
                console.error('Token CSRF introuvable')
                return null;
            }

            return tokenMeta.getAttribute('content');
        }

        // Envoi
        fetch('/addCar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'csrfToken': getToken()
            },
            body: JSON.stringify(formData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Votre véhicule a été ajouté avec succès !');
                    closeModal();
                    location.reload();
                } else {
                    showError(data.message || 'Une erreur est survenue');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showError('Une erreur est survenue. Veuillez réessayer.');
            });
    });

    function showError(message) {
        errorMsg.textContent = message;
        errorMsg.classList.add('show');
    }

    // Auto-formatage plaque
    const plateInput = document.getElementById('registrationNumber');
    plateInput.addEventListener('input', function (e) {
        let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');

        if (value.length > 2 && value.length <= 5) {
            value = value.slice(0, 2) + '-' + value.slice(2);
        } else if (value.length > 5) {
            value = value.slice(0, 2) + '-' + value.slice(2, 5) + '-' + value.slice(5, 7);
        }

        e.target.value = value;
    });

    // Date max
    const dateInput = document.getElementById('firstRegistration');
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('max', today);
});