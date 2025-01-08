<?php

session_start();
require ('../bdd/fonctions.php');
require ('../bdd/connecterBD.php');
require ('../bdd/requetes.php');
verifSession(); // Vérifie si une session valide existe

$pdo = initierConnexion();
    if ($pdo == FALSE) {
        header("Location: erreurs/erreurBD.php");
    }

// Vérifier si le formulaire a été soumis avec l'ID du conférencier
if (isset($_POST['idConferencier'])) {
    $_SESSION['idConferencier'] = intval($_POST['idConferencier']); // Mise à jour de la session
}

if (isset($_SESSION['idConferencier'])) {
    $idConferencier = $_SESSION['idConferencier'];
} else {
    die("Aucun conférencier sélectionné.");
}
    

$indisponibilites = recupIndisponibilite($pdo, $idConferencier);
$visites = recupVisites($pdo, $idConferencier);

// Vérification si une suppression est demandée
if (!empty($_POST['supprimerIndisponibilite'])) {
    $indispoASuppr = intval($_POST['supprimerIndisponibilite']); // Sécuriser la donnée    
    try {
        supprimerLigne($pdo, $indispoASuppr, "Indisponibilite");
        header("Location: planning.php");
        exit(); // Arrêter l'exécution du script
    } catch (PDOException) {
        $_SESSION['donneeEnErreur'] = 'indisponibilite';
        $_SESSION['cheminDernierePage'] = '/S3.D.01-Web/pages/planning.php';
        header("Location: ./erreurs/impossibleDeTraiterVotreDemande.php");
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Planning du Conférencier</title>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/17d5b3fa89.js" crossorigin="anonymous"></script>

</head>
<body>

<div class="container ">

    <a href="conferenciers.php" class="btn btn-secondary mt-3"><i class="fa-solid fa-arrow-left"></i></a>

    <h2>Planning du Conférencier (ID: <?php echo htmlspecialchars($idConferencier) ?>)</h2>
    <!-- Tableau des indisponibilités -->
    <?php if (count($indisponibilites) == 0){ ?>
        <p>Aucune indisponibilité enregistrée.</p>
    <?php } ?>

    <!-- Conteneur du calendrier -->
    <div id="calendar"></div>

    <?php if (count($indisponibilites) > 0){ ?>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th>Début de l'indisponibilité</th>
                    <th>Fin de l'indisponibilité</th> 
                </tr>
            </thead>
            <tbody>
                <?php foreach ($indisponibilites as $indispo){ ?>
                    <tr>
                        <td><?php echo htmlspecialchars($indispo['debut']); ?></td>
                        <td><?php echo htmlspecialchars($indispo['fin']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    <?php } ?>
</div>

<!-- Modale d'action (Modifier / Supprimer) -->
<div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="actionModalLabel">Modifier l'indisponibilité</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <p id="indispoDetails"></p>
            
            <div class="mb-3">
                <label for="debutIndispo" class="form-label">Début</label>
                <input type="date" id="debutIndispo" name="debut" class="form-control">
            </div>
            <div class="mb-3">
                <label for="finIndispo" class="form-label">Fin</label>
                <input type="date" id="finIndispo" name="fin" class="form-control">
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST" action="planning.php">
                    <input type="hidden" name="supprimerIndisponibilite" id="hiddenDeleteInput">
                            <button type="submit" class="btn-action btn-delete" 
                                id="deleteBtn"
                                title="Supprimer l'indisponibilité">
                                Supprimer
                            </button>
                </form>
                <button type="submit" class="btn btn-primary">Modifier</button>
            </div>
            
        </div>
    </div>
</div>


<!-- FullCalendar JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth', // Vue du mois
        events: [
            <?php 
            foreach ($indisponibilites as $indispo) {
                ?>
                {
                    title: 'Indisponible',
                    start: '<?php echo $indispo['debut']; ?>',
                    end: '<?php echo $indispo['fin']; ?>', 
                    color: 'red', // Couleur de l'événement
                    id_indispo: '<?php echo $indispo['id_indisponibilite']; ?>', // ID de l'indisponibilité
                    debut: '<?php echo $indispo['debut']; ?>', // Début
                    fin: '<?php echo $indispo['fin']; ?>' // Fin
                },
            <?php } ?>
            <?php foreach ($visites as $visite) { ?>
                {
                    title: 'Visite',
                    start: '<?php echo $visite['date_visite']; ?>', 
                    end: '<?php echo $visite['date_visite']; ?>', 
                    color: 'blue', // Couleur de l'événement
                },
            <?php } ?>
        ],
        eventClick: function(info) {
            if (info.event.extendedProps.id_indispo) {
                var idIndispo = info.event.extendedProps.id_indispo;
                var debut = info.event.extendedProps.debut;
                var fin = info.event.extendedProps.fin;

                // Mettre à jour l'input caché
                document.getElementById('hiddenDeleteInput').value = idIndispo;



                // Afficher la modale
                $('#actionModal').modal('show');
            }
        }
    });

    calendar.render(); // Rendu du calendrier
});
</script>

</body>
</html>