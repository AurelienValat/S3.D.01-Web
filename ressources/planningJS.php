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
        
                    // Mise à jour du formulaire de modification
                    document.getElementById('idIndisponibilite').value = idIndispo;
                    document.getElementById('debut').value = debut;
                    document.getElementById('fin').value = fin;

                    // Mise à jour du formulaire de suppression
                    document.getElementById('hiddenDeleteInput').value = idIndispo;

                    // Afficher la modale
                    var actionModal = new bootstrap.Modal(document.getElementById('actionModal'));
                    actionModal.show();
                }
            }
        });

        calendar.render(); // Rendu du calendrier
    });
</script>