// Sélectionne tous les éléments de "dot" (points) dans la page
const dots = document.querySelectorAll('.dot');

// Sélectionne tous les contenus des actualités dans la page
const newsContents = document.querySelectorAll('.news-content');

// Fonction qui gère le défilement automatique des onglets
let currentIndex = 0; // Index de l'onglet actuel (commence à 0)
const totalNews = newsContents.length; // Nombre total d'onglets

// Fonction qui active le contenu d'une actualité et son "dot" associé
function activateTab(index) {
    // Retire la classe "active" de tous les contenus et des "dots" précédemment sélectionnés
    newsContents.forEach(content => content.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));

    // Ajoute la classe "active" au contenu d'actualité et au "dot" correspondant
    newsContents[index].classList.add('active');
    dots[index].classList.add('active');
}

// Ajoute un événement de clic à chaque "dot" pour changer le contenu affiché
dots.forEach(dot => {
    dot.addEventListener('click', () => {
        // Récupère l'index de l'actualité associé au "dot" cliqué
        const index = dot.getAttribute('data-index');

        // Convertit l'index en entier (parfois les attributs HTML sont des chaînes)
        const indexInt = parseInt(index, 10);

        // Active l'onglet correspondant
        activateTab(indexInt);
    });
});

// Fonction pour faire défiler automatiquement les onglets toutes les 5 secondes
function autoSlide() {
    // Active le contenu de l'onglet actuel
    activateTab(currentIndex);

    // Passe à l'onglet suivant
    currentIndex = (currentIndex + 1) % totalNews; // Utilise le modulo pour revenir au premier onglet quand on atteint la fin
}

// Lance la fonction de défilement automatique toutes les 5 secondes (5000 millisecondes)
setInterval(autoSlide, 5000);

// Démarre le défilement automatique dès le début
autoSlide(); // Affiche le premier onglet dès que la page est chargée
