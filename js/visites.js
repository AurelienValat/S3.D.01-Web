// Pour les filtres
/*
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
*/

// Pour le modal de modif des visites
document.addEventListener('DOMContentLoaded', function() {
    var modifModal = document.getElementById('modifModal');
    modifModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget; // Bouton qui a déclenché le modal
        var idVisite = button.getAttribute('data-id'); // Récupérer l'ID de la visite

		console.log (JSON.stringify (idVisite));
		
        // Utiliser l'ID de la visite pour remplir le formulaire du modal
        var modalTitle = modifModal.querySelector('.modal-title');
        modalTitle.textContent = 'Modifier la visite ' + idVisite;

        // Remplir un champ caché avec l'ID de la visite
        var inputIdVisite = modifModal.querySelector('input[name="id_visite_Modif"]');
        inputIdVisite.value = idVisite;
    });
});

/**
 * Remplit automatiquement le formulaire de modification d'utilisateur avec les données fournies.
 * @param {number} id_visite - ID de la visite
 * @param {number} intitule_client - Intitulé du client
 * @param {string} no_tel_client - No de tel du client.
 * @param {string} date_visite - Date de la visite.
 * @param {string} horaire_debut - Horaire de début.
 */
function remplirModalModif(id_visite, intitule_client, no_tel_client, date_visite, horaire_debut) {
	
	console.log(id_visite, intitule_client, no_tel_client, date_visite, horaire_debut);
	
	let formattedDate;
	let formattedTime;
			
	// Convertir les champs date heure JJ/MM/AAAA HHhMM en AAAA-MM-JJ HH:MM si besoin
	if (!date_visite.match(/^\d{4}-\d{2}-\d{2}/) && !horaire_debut.match(/^\d{2}:\d{2}$/)) {
		// Séparer la date et l'heure
		const [day, month, year] = date_visite.split('/');
		const [hours, minutes] = horaire_debut.split('h');
		
		// Formater la date et l'heure au format attendu
		formattedDate = `${year}-${month}-${day}`;
		formattedTime = `${hours}:${minutes}`;
		
	} else {
		formattedDate = date_visite;
		formattedTime = horaire_debut;
	}
		
    // Remplir les champs du formulaire
	document.getElementById('id_visite_Modif').value = id_visite;
    document.getElementById('intitule_client_Modif').value = intitule_client;
    document.getElementById('no_tel_client_Modif').value = no_tel_client;
    document.getElementById('date_visite_Modif').value = formattedDate;
    document.getElementById('horaire_debut_Modif').value = formattedTime;
}