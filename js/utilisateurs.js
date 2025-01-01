/**
 * Remplit automatiquement le formulaire de modification d'utilisateur avec les données fournies.
 * @param {number} idUtilisateur - Identifiant de l'utilisateur.
 * @param {string} pseudo - Nom d'utilisateur.
 * @param {string} prenom - Prénom de l'utilisateur.
 * @param {string} nom - Nom de l'utilisateur.
 * @param {string} telephone - Numéro de téléphone.
 */
function remplirFormulaire(idUtilisateur, pseudo, prenom, nom, telephone) {
    // Remplir les champs du formulaire
    document.getElementById('idUtilisateur').value = idUtilisateur;
    document.getElementById('pseudoUtilisateur').value = pseudo;
    document.getElementById('prenomUtilisateur').value = prenom;
    document.getElementById('nomUtilisateur').value = nom;
    document.getElementById('telephoneUtilisateur').value = telephone;
}

function resetFormulaire() {
    document.getElementById("formAjouterUtilisateur").reset(); // Réinitialise tous les champs du formulaire
    document.getElementById("pseudo").value = ""; // Exemple : Efface le pseudo
    document.getElementById("prenom").value = ""; // Exemple : Efface le prénom
    document.getElementById("nom").value = ""; // Exemple : Efface le nom
    document.getElementById("telephone").value = ""; // Exemple : Efface le numéro de téléphone
}