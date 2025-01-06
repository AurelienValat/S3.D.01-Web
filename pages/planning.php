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
    $idConferencier = intval($_POST['idConferencier']); // Sécurisation de l'entrée
} else {
    die("Aucun conférencier sélectionné.");
}

$indisponibilites = recupIndisponibilite($pdo, $idConferencier);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Planning du Conférencier</title>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container ">
    <h2>Planning du Conférencier (ID: <?php echo htmlspecialchars($idConferencier) ?>)</h2>

    <!-- Tableau des indisponibilités -->
    <?php if (count($indisponibilites) > 0){ ?>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th>Début de l'indisponibilité</th>
                    <th>Fin de l'indisponibilité</th> 
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($indisponibilites as $indispo){ ?>
                    <tr>
                        <td><?php echo htmlspecialchars($indispo['debut']); ?></td>
                        <td><?php echo htmlspecialchars($indispo['fin']); ?></td>
                        <td>
                            <button type='button' class='btn-action btn-modify btn-blue'>Modifier</button>
                            <button type='button' class='btn btn-danger'>Supprimer</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>Aucune indisponibilité enregistrée.</p>
    <?php } ?>

    <a href="<?php echo $_SERVER['HTTP_REFERER'] ?? 'index.php'; ?>" class="btn btn-secondary mt-3">Retour</a>

    <!-- Conteneur du calendrier -->
    <div id="calendar"></div>

</div>

<!-- Modale d'action (Modifier / Supprimer) -->
<div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="actionModalLabel">Actions sur l'indisponibilité</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="indispoDetails"></p>
        <button type="button" class="btn btn-primary" id="modifyBtn">Modifier</button>
        <button type="button" class="btn btn-danger" id="deleteBtn">Supprimer</button>
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
            <?php foreach ($indisponibilites as $indispo) { ?>
                {
                    title: 'Indisponible',
                    start: '<?= $indispo['debut']; ?>', // Format: 'YYYY-MM-DD'
                    end: '<?= $indispo['fin']; ?>', // Format: 'YYYY-MM-DD'
                    color: 'red', // Couleur de l'événement
                    id_indispo: '<?= $indispo['id_indisponibilite']; ?>', // ID de l'indisponibilité
                    debut: '<?= $indispo['debut']; ?>', // Début
                    fin: '<?= $indispo['fin']; ?>' // Fin
                },
            <?php } ?>
        ],
        eventClick: function(info) {
            // Lors du clic sur un événement
            var idIndispo = info.event.extendedProps.id_indispo;
            var debut = info.event.extendedProps.debut;
            var fin = info.event.extendedProps.fin;
            
            // Mettre à jour le contenu de la modale avec les détails de l'indisponibilité
            document.getElementById('indispoDetails').innerHTML = "Indisponibilité du " + debut + " au " + fin;
            
            // Afficher la modale
            $('#actionModal').modal('show');

            // Action pour Modifier
            document.getElementById('modifyBtn').onclick = function() {
             };

            // Action pour Supprimer
            document.getElementById('deleteBtn').onclick = function() {
                if (confirm('Êtes-vous sûr de vouloir supprimer cette indisponibilité ?')) {
                    window.location.href = 'supprimer_indispo.php?id=' + idIndispo; // Supprimer l'indisponibilité
                }
            };
        }
    });

    calendar.render(); // Rendu du calendrier
});
</script>

</body>
</html>