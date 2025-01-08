<?php 
    session_start();
    require ('../bdd/fonctions.php');
    verifSession(); // Vérifie si une session valide existe
    $estAdmin = isset($_SESSION['est_admin']) && $_SESSION['est_admin'] == 1;
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">  
        <link href="../css/style.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://kit.fontawesome.com/17d5b3fa89.js" crossorigin="anonymous"></script> 
        <title>MUSEOFLOW - Acceuil</title>
    </head>
    <body>
    
        <?php require("../ressources/navBar.php");?>
       
        <h1 class="blanc">Bienvenue <?php echo htmlspecialchars($_SESSION['prenom']) . ','; ?></h1><br>
        <section class="actualites-section">
            <h2 class="blanc">Actualités</h2>
            <div class="news-widget">
                <div class="news-header">
                    <div class="nav-dots">
                        <div class="dot active" data-index="0"></div>
                        <div class="dot" data-index="1"></div>
                        <div class="dot" data-index="2"></div>
                        <div class="dot" data-index="3"></div>
                    </div>
                    <div class="progress-bar"></div>
                </div>
                <div class="news-content active" data-index="0">
                    <div class="image">
                        <img src="../ressources/images/actu1.png" alt="Actualité">
                    </div>
                    <div class="news-title">
                        <h3>Félicitations à notre employé du mois</h3>
                        <p>Bravo à Aurélien, responsable des expositions, 
                        pour son dévouement et ses idées innovantes qui enrichissent 
                        nos collections.</p>
                        <i class="fa fa-calendar" aria-hidden="true"></i> 8 Janvier 2025 
                    </div>
                </div>
                <div class="news-content" data-index="1">
                    <div class="image">
                        <img src="../ressources/images/actu2.png" alt="Actualité">
                    </div>
                    <div class="news-title">
                        <h3>Formation : Accueil des visiteurs internationaux</h3>
                        <p>Un atelier est prévu la semaine prochaine pour 
                        améliorer nos compétences d'accueil auprès d'un 
                        public diversifié.</p>
                        <i class="fa fa-calendar" aria-hidden="true"></i> 10 Janvier 2025
                    </div>
                </div>
                <div class="news-content" data-index="2">
                    <div class="image">
                        <img src="../ressources/images/actu3.png" alt="Actualité">
                    </div>
                    <div class="news-title">
                        <h3>Nouvelle équipe pour la gestion des expositions temporaires</h3>
                        <p>Bienvenue à Jean, Michael et Frédéric qui rejoignent 
                        notre équipe pour superviser les prochaines 
                        expositions.</p>
                        <i class="fa fa-calendar" aria-hidden="true"></i> 5 Janvier 2025
                    </div>
                </div>
                <div class="news-content" data-index="3">
                    <div class="image">
                        <img src="../ressources/images/actu4.png" alt="Actualité">
                    </div>
                    <div class="news-title">
                        <h3>Programme de bien-être au travail</h3>
                        <p>Le musée lance des séances de yoga et de méditation
                            hebdomadaires pour tout le personnel. 
                            Inscrivez-vous dès maintenant !</p>
                            <i class="fa fa-calendar" aria-hidden="true"></i> 7 Janvier 2025
                    </div>
                </div>
            </div>
        </section>
        <br><br><br><br><br><br><br><br><br><br><br><br><?php require("../ressources/footer.php");?>
        <script src="../js/accueil.js"></script>
    
    </body>
</html>