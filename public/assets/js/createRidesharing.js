document.addEventListener('DOMContentLoaded', function () {

    // On recupère les éléments par leur Id
    const addPreferenceBtn = document.getElementById('addPreferenceBtn');
    const preferenceList = document.getElementById('preferenceList');


    addPreferenceBtn.addEventListener('click', function (event) {
        event.preventDefault();

        const currentInputs = preferenceList.querySelectorAll('.preferenceChoice');

        if (currentInputs.length >= 4) {
            alert(`Maximum 4 préférences autorisées`, 'warning');
            return;
        }

        let newInpute = document.createElement("input");
        newInpute.type = "text";
        newInpute.name = "preferenceChoice[]";
        newInpute.placeholder = "Ex: 1 bagage /personne.";
        newInpute.className = "preferenceChoice";

        preferenceList.appendChild(newInpute);
    });
});
