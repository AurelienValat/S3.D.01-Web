/**
 * Remplit automatiquement le formulaire de modification des conférenciers avec les données fournies.
 * @param {number} idUtilisateur - Identifiant du conférencier.
 * @param {string} prenom - Prénom du conférencier.
 * @param {string} nom - Nom du conférencier.
 * @param {string} telephone - Numéro de téléphone.
 */
function remplirFormulaire(idConferencier, prenom, nom, telephone, motsCles) {
    // Remplir les champs du formulaire
    document.getElementById('idConferencier').value = idConferencier;   
    document.getElementById('prenomConferencier').value = prenom;
    document.getElementById('nomConferencier').value = nom;
    document.getElementById('telephoneConferencier').value = telephone;
    document.getElementById('motsCleSpe').value = motsCles;
}

// function resetFormulaire() {
// document.getElementById("formAjouterConferencier").reset(); // Réinitialise tous les champs du formulaire
// document.getElementById("prenom").value = ""; // Exemple : Efface le prénom
// document.getElementById("nom").value = ""; // Exemple : Efface le nom
// document.getElementById("telephone").value = ""; // Exemple : Efface le numéro de téléphone
// }