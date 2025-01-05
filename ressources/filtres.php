
<!-- Modal de filtrage dynamique en fonction de la page appelante -->
<div class="modal fade" 
     id="modalFiltrage" 
     tabindex="-1" 
     aria-labelledby="modalFiltrageLabel">
     <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtrage</h5>
                <a href="" class="btn-close" aria-label="Close"></a> <!-- href= vide pour se rapeller soi même, peu importe la page appelante -->
            </div>
            <form method="POST" action=""> <!-- action= vide pour se rapeller soi même, peu importe la page appelante -->
                <div class="modal-body">
                    <h6>Recherche textuelle :</h6>
                    <!-- Filtres destinés à la vue desconférenciers -->
                    <?php if ($_SESSION['filtreAApliquer'] === 'conférenciers') {?>
                    <p>
                        <label for='rechercheSpecialite'>Spécialité :</label>
                        <input type='text' name='rechercheSpecialite' id='rechercheSpecialite' placeholder='Ex. Temps modernes' value='<?php if (isset($_POST['rechercheSpecialite'])) {echo trim($_POST['rechercheSpecialite']);}?>'>
                    </p>
                    <?php }?>
                    <hr>
                    <h6>//Autres recherches//</h6>
                    <p>autres champs</p>
                </div>
                <div class="modal-footer">
                        <button id='btn_retour' class='btn-action btn-modify btn-blue'><span class='fa-solid fa-magnifying-glass'></span> Rechercher</button>
                        <input type="hidden" name="demandeFiltrage" value="1">
                </div>
            </form>
        </div>
    </div>
</div>