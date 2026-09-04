<?php
/**
 * views/layouts/header.php
 * --------------------------
 * Tous les liens utilisent url() — jamais d'URL écrite en dur — pour que
 * la navigation reste cohérente et facile à faire évoluer.
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($titrePage) ? nettoyer($titrePage) . ' - ImmoApp' : 'ImmoApp' ?></title>

    <!-- Polices du design system -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= cheminBase() ?>/css/design-system.css">

    <?php if (!empty($inclureLeaflet)): ?>
        <!-- Leaflet + OpenStreetMap : carte interactive gratuite, sans clé API
             (contrairement à Google Maps), chargée seulement sur les pages
             qui en ont besoin pour ne pas alourdir le reste du site.
             Le script JS est chargé ICI (dans le head), et non en bas de
             page : le code de la carte s'exécute dès le contenu de la vue,
             donc Leaflet doit déjà être disponible AVANT — sinon "L is not
             defined" et rien ne fonctionne silencieusement. -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <?php endif; ?>
    <script>
        // Appliqué ici (dans le <head>, avant tout affichage) plutôt qu'en
        // bas de page : évite un "flash" de thème clair pendant une
        // fraction de seconde avant que le mode sombre ne se déclenche.
        if (localStorage.getItem('theme') === 'sombre') {
            document.documentElement.setAttribute('data-theme', 'sombre');
        }
    </script>
</head>
<body>

<!-- Lien d'évitement : invisible tant qu'il n'a pas le focus clavier,
     permet à un utilisateur naviguant au Tab (ou lecteur d'écran) de
     sauter directement au contenu sans devoir traverser tout le menu
     à chaque page — une bonne pratique d'accessibilité de base. -->
<a href="#contenu-principal" class="visually-hidden-focusable position-absolute top-0 start-0 m-2 p-2 bg-white rounded shadow"
   style="z-index: 1090;">Aller au contenu principal</a>

<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand logo-marque" href="<?= url('') ?>">TTM</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link <?= estPageActive('', true) ? 'nav-link-actif' : '' ?>" href="<?= url('') ?>">Accueil</a></li>
                <li class="nav-item"><a class="nav-link <?= estPageActive('biens/recherche') ? 'nav-link-actif' : '' ?>" href="<?= url('biens/recherche') ?>">Rechercher</a></li>

                <?php if (estConnecte() && $_SESSION['utilisateur_role'] === 'proprietaire'): ?>
                    <li class="nav-item"><a class="btn btn-accent btn-sm px-3" href="<?= url('biens/creer/infos') ?>">+ annonce</a></li>
                <?php endif; ?>

                <?php if (estConnecte()): ?>
                    <?php
                    /**
                     * Le compteur de messages non lus est calculé ici, directement
                     * dans le gabarit partagé, car il doit apparaître sur TOUTE
                     * page tant qu'on est connecté — pas seulement sur celles où
                     * un controller l'aurait déjà préparé.
                     */
                    require_once __DIR__ . '/../../models/Message.php';
                    $messagesNonLus = (new Message())->compterNonLus((int) $_SESSION['utilisateur_id']);

                    require_once __DIR__ . '/../../models/Notification.php';
                    $notificationsNonLues = (new Notification())->compterNonLues((int) $_SESSION['utilisateur_id']);
                    ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" id="clocheNotifications" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false"
                           aria-label="<?= $notificationsNonLues > 0 ? 'Notifications (' . $notificationsNonLues . ' non lues)' : 'Notifications' ?>">
                            <span aria-hidden="true">🔔</span>
                            <span id="badgeNotifications" class="badge rounded-pill <?= $notificationsNonLues > 0 ? '' : 'd-none' ?>"
                                  style="background-color: var(--couleur-accent);"><?= $notificationsNonLues ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2" id="listeNotifications" style="min-width:320px;max-height:400px;overflow-y:auto;" aria-live="polite">
                            <li class="text-muted small text-center py-3">Chargement...</li>
                        </ul>
                    </li>

                    <?php if ($_SESSION['utilisateur_role'] !== 'proprietaire'): ?>
                        <li class="nav-item"><a class="nav-link <?= estPageActive('mes-favoris') ? 'nav-link-actif' : '' ?>" href="<?= url('mes-favoris') ?>">Favoris</a></li>
                        <li class="nav-item"><a class="nav-link <?= estPageActive('mes-alertes') ? 'nav-link-actif' : '' ?>" href="<?= url('mes-alertes') ?>">Alertes</a></li>
                    <?php endif; ?>
                    <?php
                    // Le lien "Messages" doit aussi s'allumer sur une page de
                    // conversation individuelle (/biens/12/messages/5), pas
                    // seulement sur la boîte de réception elle-même.
                    $estSurMessagerie = estPageActive('messages', true) || str_contains($_SERVER['REQUEST_URI'], '/messages/');
                    ?>
                    <li class="nav-item">
                        <a class="nav-link position-relative <?= $estSurMessagerie ? 'nav-link-actif' : '' ?>" href="<?= url('messages') ?>">
                            Messages
                            <?php if ($messagesNonLus > 0): ?>
                                <span class="badge rounded-pill" style="background-color: var(--couleur-accent);"><?= $messagesNonLus ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php if ($_SESSION['utilisateur_role'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link <?= estPageActive('admin') ? 'nav-link-actif' : '' ?>" href="<?= url('admin') ?>"> Administration</a></li>
                    <?php endif; ?>
                    <?php if ($_SESSION['utilisateur_role'] === 'proprietaire'): ?>
                        <li class="nav-item"><a class="nav-link <?= estPageActive('tableau-bord', true) ? 'nav-link-actif' : '' ?>" href="<?= url('tableau-bord') ?>">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link <?= estPageActive('mes-annonces') ? 'nav-link-actif' : '' ?>" href="<?= url('mes-annonces') ?>">Gestion</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link <?= estPageActive('mes-visites', true) ? 'nav-link-actif' : '' ?>" href="<?= url('mes-visites') ?>">Mes visites</a></li>
                    <li class="nav-item"><a class="nav-link <?= estPageActive('mon-profil') ? 'nav-link-actif' : '' ?>" href="<?= url('mon-profil') ?>">Mon Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('deconnexion') ?>">Déconnexion</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link <?= estPageActive('connexion', true) ? 'nav-link-actif' : '' ?>" href="<?= url('connexion') ?>">Connexion</a></li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm px-3" href="<?= url('inscription') ?>">Inscription</a>
                    </li>
                <?php endif; ?>
               
            </ul>
        </div>
    </div>
</nav>

<main id="contenu-principal" class="container my-4">
