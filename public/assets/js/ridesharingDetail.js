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

    // Affichage du prix selon le nombre de place.

    const nbSeats = document.getElementById('nbSeats');
    const pricePerSeat = document.getElementById('ridesharingPrice').dataset.price;
    const totalAmount = document.getElementById('totalAmount');

    function totalPrice() {
        totalAmount.textContent = pricePerSeat * nbSeats.value;
    }

    nbSeats.addEventListener('input', totalPrice);

});