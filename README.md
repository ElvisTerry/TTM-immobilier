TTM - Trouve Ton Milieu

Plateforme immobilière camerounaise permettant à des propriétaires de publier des annonces (location ou vente) et à des locataires/acheteurs de rechercher, contacter et visiter des biens, le tout avec modération, messagerie intégrée, avis, favoris, alertes et un espace d'administration complet.

Sommaire

   _Présentation
   _Rôles et fonctionnalités 
   _Stack technique
   _Architecture du projet
   _Sécurité
   _Design system
   _Installation en local (Laragon)
   _Déploiement en production 
   _Base de données
   _Limites connues
   _Auteur


1. Présentation

    TTM (Trouve Ton Milieu) est une application web PHP "maison" (sans framework externe type Laravel/Symfony) construite pour répondre à un besoin concret : permettre à n'importe qui au Cameroun de publier ou trouver un logement (chambre, studio, appartement, maison) à louer ou à vendre, avec un système de confiance intégré (modération des annonces, avis, signalements) et une expérience mobile-first.
    Le projet a été développé de façon incrémentale, "jour par jour", chaque fonctionnalité s'appuyant sur les précédentes sans jamais casser l'existant.


2. Rôles et fonctionnalités

    L'application distingue 3 rôles : locataire, proprietaire et administrateur.

a) Visiteur (non connecté)
    - Recherche de biens avec filtres (ville, quartier, type de bien, type de transaction, budget, rayon géographique autour d'un point sur la carte)
    - Consultation d'une fiche bien complète (photos, équipements, localisation, avis)
    - Consultation du profil public d'un propriétaire (annonces, note moyenne, temps de réponse moyen)
    - Inscription / connexion

b) Locataire
    Tout ce qui précède, plus :
    - Contact du propriétaire par messagerie intégrée (avec accusé de lecture, mise à jour en temps quasi-réel par sondage)
    - Réservation d'une visite sur un créneau disponible
    - Annulation d'une demande de visite en attente ou déjà acceptée
    - Ajout de biens en favoris
    - Création d'alertes de recherche sauvegardées (notifiées automatiquement dès qu'une nouvelle annonce correspondante est approuvée)
    - Dépôt d'un avis noté (1 à 5 étoiles) après une visite acceptée
    - Signalement d'une annonce suspecte (motif + description)
    - Suppression définitive de son compte (voir [Sécurité](#-sécurité))

c) Propriétaire
    Tout ce qui précède (sauf réserver une visite sur son propre bien), plus :
    - Assistant de publication en 5 étapes (infos - équipements - localisation - photos - récapitulatif), avec sauvegarde de session à chaque étape pour ne rien perdre en cas d'abandon
    - Upload de photos par glisser-déposer, réordonnancement, compression automatique
    - Gestion des photos après publication (ajout/suppression sans repasser par l'assistant complet)
    - Modification d'une annonce déjà publiée (repasse automatiquement en modération, voir Sécurité)
    - Gestion des disponibilités (calendrier de créneaux bloqués, avec détection de chevauchement)
    - Acceptation/refus des demandes de visite (l'acceptation bloque automatiquement le créneau et refuse les autres demandes concurrentes sur la même date)
    - Réponse publique aux avis laissés sur ses biens
    - Tableau de bord avec statistiques (vues, biens actifs, taux d'approbation...)
    - Badge "temps de réponse moyen" affiché automatiquement sur son profil public dès qu'il a un historique de conversation suffisant

d) Administrateur
    - Modération des annonces (approuver / rejeter, avec notification automatique au propriétaire et déclenchement des alertes de recherche correspondantes)
    - Gestion des signalements (traiter / rejeter)
    - Gestion des comptes utilisateurs (suspendre / réactiver)
    - Journal des accès refusés : trace chaque tentative d'accès à une page admin par un compte non autorisé (IP, route visée, utilisateur si connu)
    - Export SQL complet de la base en un clic (sauvegarde manuelle, utile sur un hébergement mutualisé sans accès `cron`/shell)
    - Nettoyage des fichiers photo orphelins (uploads interrompus, non rattachés à une annonce)

3. Stack technique

    Langage serveur : PHP 8 (natif, POO, sans framework) car Contrôle total, hébergement mutualisé compatible 
    Base de données : MySQL / MariaDB via PDO (requêtes préparées partout) car Standard, disponible sur tout hébergement 
    Frontend : Bootstrap 5.3 + CSS custom (design system maison) car Rapide à mettre en œuvre, personnalisable 
    Cartes : Leaflet.js + OpenStreetMap car Gratuit, sans clé API (contrairement à Google Maps) 
    JavaScript : Vanilla JS (aucune librairie de framework) car Pas de build step, simplicité de déploiement 
    Emails : "mail()" PHP natif (simulation en fichier local en développement) car Compatible hébergement mutualisé gratuit 
    Hébergement : Laragon (local) puis InfinityFree (production) car Gratuit, accessible sans budget serveur 


4. Architecture du projet

Le projet suit un pattern MVC fait maison, avec un routeur minimaliste basé sur des expressions régulières.


htdocs/
├── config/
│   ├── environnement.php      # ENVIRONNEMENT = 'developpement' | 'production'
│   └── database.php           # Connexion PDO
├── controllers/                # Un controller par domaine fonctionnel
│   ├── AuthController.php
│   ├── BienController.php
│   ├── AdminController.php
│   ├── MessageController.php
│   ├── ProfilController.php
│   ├── ReservationController.php
│   ├── DisponibiliteController.php
│   ├── AlerteController.php
│   ├── FavoriController.php
│   ├── AvisController.php
│   ├── SignalementController.php
│   └── NotificationController.php
        etc...
├── models/                     # Un modèle par table/entité (PDO direct, pas d'ORM)
│   ├── Utilisateur.php
│   ├── Bien.php
│   ├── Message.php
│   ├── Notification.php
│   ├── Disponibilite.php
│   ├── ReservationVisite.php
│   ├── Avis.php
│   ├── Favori.php
│   ├── RechercheSauvegardee.php
│   ├── Signalement.php
│   ├── AccesAdminRefuse.php
│   └── TentativeIp.php
         etc...
├── views/                      # Templates PHP, découpés par domaine
│   ├── layouts/                # header.php / footer.php communs
│   ├── auth/
│   ├── biens/
│   │   └── creation/           # Les 5 étapes de l'assistant de publication
│   ├── admin/
│   ├── profil/
│   ├── messages/
│   └── erreurs/ 
         etc...
├── includes/
│   ├── Router.php              # Routeur maison (regex sur les URLs)
│   ├── helpers.php             # url(), nettoyer(), CSRF, permissions, compression image...
│   └── Mailer.php              # Envoi (ou simulation) d'emails transactionnels
├── uploads/                    # Fichiers uploadés (frère de public/, PAS dedans)
│   ├── biens/
│   └── avatars/
├── logs/
│   └── emails-simules/         # Emails "envoyés" en développement (fichiers .txt)
└── index.php                # Point d'entrée UNIQUE de l'application
└── css/design-system.css

Le routeur

    Toutes les requêtes passent par `public/index.php`, qui déclare les routes puis délègue à `Router::dispatch()` :
    $router->get('biens/detail/{id}', 'BienController', 'detail');
    $router->post('biens/{id}/modifier', 'BienController', 'traiterModification');

    Les `{id}` sont automatiquement convertis en `(\d+)` (entiers uniquement), les autres `{paramètre}` en `([a-zA-Z0-9]+)` (utile pour les tokens de réinitialisation de mot de passe par exemple).

La fonction `url()`

Jamais d'URL écrite en dur dans les vues. La fonction `url('biens/detail', [12])` détecte automatiquement si le site est servi depuis la racine ou un sous-dossier, et construit l'URL correspondante. `urlAbsolue()` fait la même chose avec le domaine complet (nécessaire dans les emails).


 5. Sécurité

Liste des protections mises en place, par catégorie :

Authentification
    - Mots de passe hachés avec `password_hash()` (bcrypt)
    - Verrouillage de compte après 5 échecs de connexion (15 minutes)
    - Blocage anti-brute-force par adresse IP en complément (20 tentatives, 15 minutes) - protège contre un attaquant qui viserait plusieurs comptes différents sans jamais déclencher le verrou individuel
    - Régénération de l'identifiant de session à la connexion (anti fixation de session)
    - Réinitialisation de mot de passe par token à usage unique, expirant après 1 heure
    - Messages d'erreur volontairement génériques ("email ou mot de passe incorrect") pour empêcher l'énumération de comptes existants

Autorisation
    - Vérification systématique de la propriété d'une ressource avant modification/suppression (impossible de modifier l'annonce, le profil ou la conversation de quelqu'un d'autre en changeant un ID dans l'URL)
    - Journal des tentatives d'accès admin refusées, consultable par les administrateurs

Contenu
    - Protection CSRF sur tous les formulaires (token à usage par session, vérifié avec `hash_equals()`)
    - Échappement systématique en sortie (`htmlspecialchars`) pour prévenir les failles XSS
    - Vérification du type MIME réel des fichiers uploadés (pas seulement l'extension déclarée), noms de fichiers régénérés aléatoirement
    - Toute modification d'une annonce (texte ou photos) la repasse automatiquement en modération, empêche un propriétaire de faire approuver une annonce propre puis d'en changer le contenu sans contrôle

Infrastructure
    - En-têtes HTTP de sécurité (`X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`)
    - Cookies de session `httponly` + `samesite=Lax` (+ `secure` en production)
    - Requêtes SQL exclusivement préparées (PDO), aucune concaténation de valeurs utilisateur

Confidentialité (RGPD-friendly)
    - Suppression de compte = anonymisation (pas de suppression brutale) : les données personnelles sont effacées, mais les messages/avis échangés avec d'autres utilisateurs restent visibles pour eux (affichant "Compte supprimé"), pour ne pas casser leur historique
    - Les annonces d'un compte supprimé sont réellement supprimées, avec leurs photos


6. Design system

Palette inspirée d'une identité visuelle chaleureuse et locale plutôt que des couleurs "startup tech" génériques :

| Rôle | Couleur | Usage |
|---|---|---|
| Primaire | `#0B5D3B` (vert forêt) | Confiance, actions principales |
| Accent | `#C1440E` (rouille/latérite) | Actions secondaires, alertes visuelles |
| Or | `#E3A72E` (or savane) | Badges, notes, éléments secondaires |
| Fond | `#FAF8F3` (blanc chaud) | Arrière-plan par défaut |

- **Typographies** : Fraunces (titres, empattée) + Work Sans (corps de texte, sans-serif)
- **Mode sombre** intégré (bascule via `localStorage`, sans flash au chargement)
- Composants signature : rail de catégories défilant en page d'accueil, squelettes de chargement animés pendant les recherches AJAX, lightbox photo maison, indicateur de menu actif qui "glisse" sous le lien

---

## 💻 Installation en local (Laragon)

1. Cloner/copier le projet dans le dossier `www` de Laragon.
2. Créer la base de données dans HeidiSQL (ou phpMyAdmin), puis exécuter **dans l'ordre** tous les fichiers `jourX-schema.sql` / `jourX-migration.sql`.
3. Vérifier `config/database.php` (identifiants MySQL locaux).
4. Vérifier que `ENVIRONNEMENT` vaut `'developpement'` dans `config/environnement.php` — les emails transactionnels s'écrivent alors dans `logs/emails-simules/` au lieu d'être réellement envoyés (Laragon n'a pas de serveur SMTP configuré par défaut).
5. Démarrer Apache/MySQL depuis Laragon, accéder au site via `http://immo-app.test/` (ou l'URL configurée).
6. Vérifier les droits d'écriture sur `uploads/biens/`, `uploads/avatars/` et `logs/`.

---

## 🚀 Déploiement en production

Hébergement actuel : **InfinityFree** (mutualisé gratuit).

- `ENVIRONNEMENT` doit valoir `'production'` → active `mail()` réel, cookies `secure`.
- **Rappel d'architecture** : `uploads/` est un dossier frère de `public/`, pas un sous-dossier — vérifier les permissions d'écriture (755/775) sur `uploads/biens/` et `uploads/avatars/` directement à la racine `htdocs/`.
- Limite connue : `mail()` sur un hébergement mutualisé gratuit part fréquemment en spam ou est bloqué par les gros fournisseurs (Gmail, Outlook). Pour un vrai lancement, migrer vers un service transactionnel dédié (Brevo, Mailjet...) est recommandé.
- Aucune tâche planifiée (`cron`) disponible sur ce plan → la sauvegarde de base de données se fait manuellement via le bouton export SQL de l'espace admin.

---

## 🗄 Base de données

Tables principales (MySQL/InnoDB, clés étrangères actives) :

| Table | Rôle |
|---|---|
| `utilisateurs` | Comptes (locataire/propriétaire/admin), sécurité de connexion, anonymisation |
| `biens` | Annonces, avec statut de modération ET statut commercial distincts |
| `photos_biens` | Photos liées à une annonce, avec ordre d'affichage |
| `disponibilites` | Périodes bloquées par un propriétaire (vérification anti-chevauchement) |
| `reservations_visites` | Demandes de visite, liées à une disponibilité auto-créée si acceptées |
| `messages` | Messagerie, un fil = (bien, interlocuteur) |
| `notifications` | Notifications in-app (cloche) |
| `avis` | Avis notés + réponse du propriétaire |
| `favoris` | Biens mis en favoris par un locataire |
| `recherches_sauvegardees` | Alertes de recherche |
| `signalements` | Signalements d'annonces |
| `tentatives_ip` | Anti-brute-force par IP |
| `acces_admin_refuses` | Journal de sécurité (accès admin refusés) |

> 💡 Le détail complet des colonnes se trouve dans les fichiers `jourX-schema.sql` / `jourX-migration.sql`, qui font office d'historique de migration daté.

---

## 🔭 Limites connues / pistes d'amélioration

- Pas de notifications par **email** pour les événements in-app (message reçu, visite acceptée...) — uniquement la cloche de notification, donc silence total pour un utilisateur déconnecté.
- Pas de filtre de recherche sur le **nombre de chambres** ni la **superficie**, bien que ces données existent en base.
- Pas de vérification d'identité (**KYC**) pour les propriétaires.
- Aucun **tableau de bord dédié** pour le rôle locataire (contrairement au propriétaire).
- Pas de gestion de **paiement en ligne** ni de **contrat/bail** téléchargeable.
- Pas de **pièces jointes** dans la messagerie (texte seul).

---

## ✍️ Auteur

Projet développé et maintenu de façon incrémentale ("jour par jour"), avec une attention particulière portée à la sécurité (voir la section dédiée) et à l'expérience utilisateur sur mobile.

---

*Ce README est un document vivant — à mettre à jour à chaque fonctionnalité majeure ajoutée ou retirée.*
