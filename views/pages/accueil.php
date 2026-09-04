<!-- ==================== HERO (épuré, sans formulaire) ==================== -->
<section class="hero-accueil">
    <div class="contenu-hero">
        <span class="hero-eyebrow">TTM Cameroun</span>
        <h1 class="hero-titre fw-bold mb-3">Trouvez votre prochain chez-vous</h1>
        <p class="hero-soustitre mb-4">Chambres, studios, appartements et maisons, à louer ou à vendre, partout au Cameroun.</p>
        <a href="<?= url('biens/recherche') ?>" class="btn btn-accent rounded-pill px-4 py-2">Explorer les annonces</a>
    </div>
</section>

<!-- ==================== RAIL DE CATÉGORIES (élément signature) ==================== -->
<section class="mb-4">
    <?php
    $categories = [
        '' => [ 'libelle' => 'Tout voir'],
        'chambre' => [ 'libelle' => 'Chambre'],
        'studio' => [ 'libelle' => 'Studio'],
        'appartement' => [ 'libelle' => 'Appartement'],
        'maison' => [ 'libelle' => 'Maison'],
    ];
    ?>
    <div class="rail-categories">
        <?php foreach ($categories as $valeur => $categorie): ?>
            <a class="item-categorie" href="<?= url('biens/recherche') ?><?= $valeur ? '?type_bien=' . $valeur : '' ?>">
                <span class="libelle"><?= $categorie['libelle'] ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- ==================== BARRE DE CONFIANCE ==================== -->
<section class="barre-confiance text-center mb-5 py-3">
    <div class="row g-3 justify-content-center">
        <div class="col-4 col-md-3">
            <div class="chiffre"><?= $totalAnnonces ?>+</div>
            <div class="small text-muted">Annonces publiées</div>
        </div>
        <div class="col-4 col-md-3">
            <div class="chiffre"><?= $totalVilles ?></div>
            <div class="small text-muted">Villes couvertes</div>
        </div>
        <div class="col-4 col-md-3">
            <div class="chiffre"><?= $totalAvis ?></div>
            <div class="small text-muted">Avis de locataires</div>
        </div>
    </div>
</section>

<!-- ==================== DERNIÈRES ANNONCES ==================== -->
<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">Dernières annonces</h2>
        <a href="<?= url('biens/recherche') ?>" class="small">Voir toutes les annonces </a>
    </div>

    <?php if (empty($dernieresAnnonces)): ?>
        <p class="text-muted text-center py-5">Aucune annonce publiée pour l'instant. Revenez bientôt !</p>
    <?php endif; ?>

    <div class="row g-4">
        <?php foreach ($dernieresAnnonces as $index => $annonce): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <?php
                // Comme demandé : les annonces ne sont CLIQUABLES que pour les
                // utilisateurs connectés. Un visiteur non connecté voit la
                // carte (aperçu) mais est invité à se connecter pour découvrir
                // le détail — jamais un lien mort ni un vrai lien de détail.
                $balise = estConnecte() ? 'a' : 'div';
                ?>
                <<?= $balise ?>
                    <?php if (estConnecte()): ?>href="<?= url('biens/detail', [(int) $annonce['id']]) ?>"<?php endif; ?>
                    class="text-decoration-none text-dark d-block position-relative">
                    <div class="card carte-bien h-100 shadow-sm">
                        <div class="position-relative overflow-hidden">
                            <?php if ($annonce['photo_url']): ?>
                                <img src="<?= nettoyer($annonce['photo_url']) ?>" class="card-img-top"
                                     <?= $index < 3 ? '' : 'loading="lazy"' ?>
                                     style="height:190px;object-fit:cover;" alt="<?= nettoyer($annonce['titre']) ?>">
                            <?php else: ?>
                                <div class="bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height:190px;">
                                    <span class="text-muted small">Aucune photo</span>
                                </div>
                            <?php endif; ?>
                            <span class="badge position-absolute top-0 start-0 m-2"
                                  style="background-color: var(--couleur-primaire);">
                                <?= $annonce['type_transaction'] === 'location' ? 'Location' : 'Vente' ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title fs-6"><?= nettoyer($annonce['titre']) ?></h3>
                            <p class="card-text small text-muted mb-1">📍 <?= nettoyer($annonce['ville']) ?></p>
                            <p class="card-text fw-bold fs-5 mb-0" style="color: var(--couleur-primaire);">
                                <?= number_format((float) $annonce['prix'], 0, ',', ' ') ?> FCFA
                                <?= $annonce['type_transaction'] === 'location' ? '<span class="fs-6 fw-normal text-muted">/ mois</span>' : '' ?>
                            </p>

                            <?php if (!estConnecte()): ?>
                                <div class="small mt-2" style="color: var(--couleur-accent);">
                                    🔒 <a href="<?= url('connexion') ?>">Connectez-vous</a> pour voir le détail
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </<?= $balise ?>>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ==================== COMMENT ÇA MARCHE ==================== -->
<section class="mb-5">
    <h2 class="h5 mb-4 text-center">Comment ça marche</h2>
    <div class="row">
        <div class="col-12 col-md-4 etape-processus">
            <div class="etape-numero">1</div>
            <h3 class="h6">Recherchez</h3>
            <p class="small text-muted mb-0">Filtrez par ville, quartier, prix et type de bien pour trouver ce qui vous correspond.</p>
        </div>
        <div class="col-12 col-md-4 etape-processus">
            <div class="etape-numero">2</div>
            <h3 class="h6">Visitez et échangez</h3>
            <p class="small text-muted mb-0">Réservez une visite et discutez directement avec le propriétaire via la messagerie.</p>
        </div>
        <div class="col-12 col-md-4 etape-processus">
            <div class="etape-numero">3</div>
            <h3 class="h6">Emménagez</h3>
            <p class="small text-muted mb-0">Finalisez en toute confiance, avis et notes vérifiés à l'appui.</p>
        </div>
    </div>
</section>

<!-- ==================== CTA PROPRIÉTAIRES ==================== -->
<?php if (!estConnecte()): ?>
<section class="bandeau-cta mb-4">
    <h2 class="h4 mb-2">Vous avez un bien à louer ou à vendre ?</h2>
    <p class="text-muted mb-3">Inscrivez-vous en tant que Proprietaire puis publiez votre annonce et touchez des milliers de visiteurs.</p>
    <a href="<?= url('inscription') ?>" class="btn btn-accent rounded-pill px-4">Inscrivez vous et publiez </a>
</section>
<?php endif; ?>
