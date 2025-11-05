document.addEventListener('DOMContentLoaded', function () {


    // Pour la confirmation 
    const confirmedInput = document.getElementById('confirmed');
    const reservationForm = document.getElementById('reservationForm');

    reservationForm.addEventListener('submit', function (event) {
        event.preventDefault();
        if (confirm('Êtes-vous sûr de vouloir participer à ce trajet ?')) {
            confirmedInput.value = true;
            reservationForm.submit();
        }
    })



});