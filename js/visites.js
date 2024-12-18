// Pour les filtres
document.addEventListener("DOMContentLoaded", function () {
    const reserverDetails = document.querySelector("#reserverDetails");
    const filtreDetails = document.querySelector("#filtreDetails");

    reserverDetails.addEventListener("toggle", function () {
        if (reserverDetails.open) {
            // Cache le bouton "Filtres" si "Ajouter/Réserver" est ouvert
            filtreDetails.classList.add("hidden");
        } else {
            // Affiche le bouton "Filtres" sinon
            filtreDetails.classList.remove("hidden");
        }
    });
});


// Pour le modal de modif des visites
document.addEventListener('DOMContentLoaded', function() {
    var modifModal = document.getElementById('modifModal');
    modifModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget; // Bouton qui a déclenché le modal
        var idVisite = button.getAttribute('data-id'); // Récupérer l'ID de la visite

        // Utiliser l'ID de la visite pour remplir le formulaire du modal
        var modalTitle = modifModal.querySelector('.modal-title');
        modalTitle.textContent = 'Modifier la visite ' + idVisite;

        // Remplir un champ caché avec l'ID de la visite
        var inputIdVisite = modifModal.querySelector('input[name="id_visite"]');
        inputIdVisite.value = idVisite;
    });
});
