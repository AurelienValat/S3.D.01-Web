// Récupère tous les éléments des "dots" et du contenu des actualités
const dots = document.querySelectorAll('.dot');
const newsContents = document.querySelectorAll('.news-content');

// Ajoute un événement de clic à chaque "dot"
dots.forEach(dot => {
    dot.addEventListener('click', () => {
        // Récupère l'index de l'actualité à afficher
        const index = dot.getAttribute('data-index');

        // Retire la classe "active" de tous les contenus et "dots"
        newsContents.forEach(content => content.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        // Ajoute la classe "active" au contenu et au "dot" correspondant
        newsContents[index].classList.add('active');
        dot.classList.add('active');
    });
});
