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
                    <!-- Filtres destinés à la vue des conférenciers -->
                    <?php if ($_SESSION['filtreAApliquer'] === 'conférenciers') {?>
                        <p>
                            <label for='rechercheSpecialite'>Spécialité :</label>
                            <input type='text' name='rechercheSpecialite' id='rechercheSpecialite' placeholder='Ex. Temps modernes' 
                                value='<?php if (isset($_POST['rechercheSpecialite'])) {echo trim($_POST['rechercheSpecialite']);}?>'> <br>

                            <label for='rechercheMotsCles'>Mots clés :</label>
                            <input type='text' name='rechercheMotsCles' id='rechercheMotsCles' placeholder='Ex. histoire, art' 
                                value='<?php if (isset($_POST['rechercheMotsCles'])) {echo trim($_POST['rechercheMotsCles']);}?>'> <br>

                            <label for='rechercheNom'>Nom :</label>
                            <input type='text' name='rechercheNom' id='rechercheNom' placeholder='Entrez un nom' 
                                value='<?php if (isset($_POST['rechercheNom'])) {echo trim($_POST['rechercheNom']);}?>'> <br>

                            <label for='recherchePrenom'>Prénom :</label> 
                            <input type='text' name='recherchePrenom' id='recherchePrenom' placeholder='Entrez un prénom' 
                                value='<?php if (isset($_POST['recherchePrenom'])) {echo trim($_POST['recherchePrenom']);}?>'> <br>

                            <label for='rechercheType'>Type :</label>
                            <select class="form-control" name='rechercheType' id='rechercheType'>
                                <option value="" <?php echo empty($_POST['rechercheType']) ? "selected" : ""; ?>>-- Sélectionnez un type --</option>
                                <option value="1" <?php echo isset($_POST['rechercheType']) && $_POST['rechercheType'] === "1" ? "selected" : ""; ?>>Interne</option>
                                <option value="0" <?php echo isset($_POST['rechercheType']) && $_POST['rechercheType'] === "0" ? "selected" : ""; ?>>Externe</option>
                            </select>

                        </p>
                        
                    <!-- Filtres destinés à la vue des utilisateurs -->
                    <?php } else if ($_SESSION['filtreAApliquer'] === 'utilisateurs') {?>
                    <p>
                        <!-- TODO mettre une taille max pour le nom et prénom  -->
                        <label for='rechercheNom'>Nom :</label>
                        <input type='text' name='rechercheNom' id='rechercheNom' placeholder='Entrez un nom' value='<?php if (isset($_POST['rechercheNom'])) {echo trim($_POST['rechercheNom']);}?>'> <br >
                        <label for='recherchePrenom'>Prénom :</label> 
                        <input type='text' name='recherchePrenom' id='recherchePrenom' placeholder='Entrez un prénom' value='<?php if (isset($_POST['recherchePrenom'])) {echo trim($_POST['recherchePrenom']);}?>'>  
                    </p>
                    <?php } else {?>
                        <h6>Aucun filtre disponible pour la vue actuelle.</h6>
                    <?php }?>
                    <?php
                        if ($_SESSION['filtreAApliquer'] === 'visites') { ?>
                            <p>
                                <!-- Filtrer par exposition -->
                                <label for='filtreRechercheExposition'>Exposition :</label>
                                <select class="form-control" name='rechercheExposition' id='filtreRechercheExposition'>
                                    <option value="" <?php echo empty($_POST['rechercheExposition']) ? "selected" : ""; ?>>
                                        -- Sélectionnez une exposition --
                                    </option>
                                    <?php
                                    foreach ($expositions as $expo) {
                                        $selected = isset($_POST['rechercheExposition']) && $_POST['rechercheExposition'] == $expo['id_exposition'] ? "selected" : "";
                                        echo "<option value='{$expo['id_exposition']}' $selected>" . htmlspecialchars($expo['intitule']) . "</option>";
                                    }
                                    ?>
                                </select>
                                <br>

                                <!-- Filtrer par conférencier -->
                                <label for='filtreRechercheConferencier'>Conférencier :</label>
                                <select class="form-control" name='rechercheConferencier' id='filtreRechercheConferencier'>
                                    <option value="" <?php echo empty($_POST['rechercheConferencier']) ? "selected" : ""; ?>>
                                        -- Sélectionnez un conférencier --
                                    </option>
                                    <?php
                                    $conferenciers = getConferenciers($pdo);
                                    foreach ($conferenciers as $conf) {
                                        $selected = isset($_POST['rechercheConferencier']) && $_POST['rechercheConferencier'] == $conf['id_conferencier'] ? "selected" : "";
                                        echo "<option value='{$conf['id_conferencier']}' $selected>" . htmlspecialchars($conf['nom'] . " " . $conf['prenom']) . "</option>";
                                    }
                                    ?>
                                </select>
                                <br>

                                <!-- Filtrer par période de l'année -->
                                <label for='filtreRechercheDateDebut'>Période - Date début :</label>
                                <input type='date' name='rechercheDateDebut' id='filtreRechercheDateDebut'
                                    value='<?php echo isset($_POST['rechercheDateDebut']) ? trim($_POST['rechercheDateDebut']) : ""; ?>'>
                                <br>
                                <label for='filtreRechercheDateFin'>Période - Date fin :</label>
                                <input type='date' name='rechercheDateFin' id='filtreRechercheDateFin'
                                    value='<?php echo isset($_POST['rechercheDateFin']) ? trim($_POST['rechercheDateFin']) : ""; ?>'>
                                <br>

                                <!-- Filtrer par intervalle horaire -->
                                <label for='filtreRechercheHeureDebut'>Heure début :</label>
                                <input type='time' name='rechercheHeureDebut' id='filtreRechercheHeureDebut'
                                    value='<?php echo isset($_POST['rechercheHeureDebut']) ? trim($_POST['rechercheHeureDebut']) : ""; ?>'>
                            </p>
                        <?php } ?>
                    <hr>
                </div>
                <div class="modal-footer">
                        <button id='btn_retour' class='btn-action btn-modify btn-blue'><span class='fa-solid fa-magnifying-glass'></span> Rechercher</button>
                        <input type="hidden" name="demandeFiltrage" value="1">
                </div>
            </form>
        </div>
    </div>
</div>