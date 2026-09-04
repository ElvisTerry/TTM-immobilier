</main>

<footer class="text-white-50 py-4 mt-5" style="background-color: var(--couleur-texte);">
    <div class="container text-center small">
        &copy; <?= date('Y') ?> TTM - Plateforme immobilière du Cameroun
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function () {
    const bouton = document.getElementById('boutonTheme');
    if (!bouton) return;

    function mettreAJourIcone() {
        bouton.textContent = document.documentElement.getAttribute('data-theme') === 'sombre' ? '☀️' : '🌙';
    }
    mettreAJourIcone();

    bouton.addEventListener('click', () => {
        const sombreActif = document.documentElement.getAttribute('data-theme') === 'sombre';
        if (sombreActif) {
            document.documentElement.removeAttribute('data-theme');
            localStorage.setItem('theme', 'clair');
        } else {
            document.documentElement.setAttribute('data-theme', 'sombre');
            localStorage.setItem('theme', 'sombre');
        }
        mettreAJourIcone();
    });
})();
</script>

<?php if (!empty($inclureLeaflet)): ?>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<?php endif; ?>

<?php if (estConnecte()): ?>
<script>
/**
 * Panneau de notifications (Jour 13) : chargé UNIQUEMENT à l'ouverture
 * de la cloche (événement Bootstrap "show.bs.dropdown"), pas au chargement
 * de la page — cohérent avec le principe déjà appliqué aux notifications
 * elles-mêmes : ne pas alourdir chaque page pour un panneau qu'on
 * n'ouvrira peut-être jamais.
 */
(function () {
    const cloche = document.getElementById('clocheNotifications');
    if (!cloche) return;

    const liste = document.getElementById('listeNotifications');
    const badge = document.getElementById('badgeNotifications');
    const csrfToken = <?= json_encode(genererTokenCSRF()) ?>;
    const urlListe = <?= json_encode(url('notifications/liste')) ?>;
    const urlMarquerLues = <?= json_encode(url('notifications/marquer-lues')) ?>;

    let dejaChargees = false;
    let decalageNotifications = 0;

    function texteRelatif(dateISO) {
        const diffMinutes = Math.round((Date.now() - new Date(dateISO.replace(' ', 'T'))) / 60000);
        if (diffMinutes < 1) return "à l'instant";
        if (diffMinutes < 60) return `il y a ${diffMinutes} min`;
        const diffHeures = Math.round(diffMinutes / 60);
        if (diffHeures < 24) return `il y a ${diffHeures} h`;
        return `il y a ${Math.round(diffHeures / 24)} j`;
    }

    function ajouterNotifications(notifications) {
        notifications.forEach((notification) => {
            const item = document.createElement('li');
            const lien = document.createElement('a');
            lien.className = 'dropdown-item small py-2 ' + (notification.lu === '0' || notification.lu === 0 ? 'fw-semibold' : 'text-muted');
            lien.href = notification.lien || '#';

            // textContent : le contenu de la notification peut inclure un
            // nom d'utilisateur ou un titre d'annonce saisi par quelqu'un
            // d'autre — jamais inséré comme HTML brut.
            const texte = document.createElement('div');
            texte.textContent = notification.contenu;
            const date = document.createElement('div');
            date.className = 'text-muted';
            date.style.fontSize = '0.75rem';
            date.textContent = texteRelatif(notification.date_creation);

            lien.appendChild(texte);
            lien.appendChild(date);
            item.appendChild(lien);
            liste.appendChild(item);
        });
    }

    async function chargerNotifications(decalage) {
        try {
            const reponse = await fetch(urlListe + '?decalage=' + decalage);
            const donnees = await reponse.json();
            const notifications = donnees.notifications || [];

            document.getElementById('boutonPlusNotifications')?.remove();

            if (decalage === 0) {
                liste.innerHTML = '';
                if (notifications.length === 0) {
                    liste.innerHTML = '<li class="text-muted small text-center py-3">Aucune notification.</li>';
                    return;
                }
            }

            ajouterNotifications(notifications);
            decalageNotifications = decalage + notifications.length;

            if (donnees.aPlus) {
                const itemBouton = document.createElement('li');
                itemBouton.innerHTML = '<button type="button" id="boutonPlusNotifications" class="dropdown-item small text-center text-primary">Charger plus</button>';
                liste.appendChild(itemBouton);
                itemBouton.querySelector('button').addEventListener('click', () => chargerNotifications(decalageNotifications));
            }
        } catch (erreur) {
            liste.innerHTML = '<li class="text-danger small text-center py-3">Erreur de chargement.</li>';
        }
    }

    async function marquerToutesLues() {
        badge.classList.add('d-none');
        badge.textContent = '0';

        const donnees = new FormData();
        donnees.append('csrf_token', csrfToken);
        try {
            await fetch(urlMarquerLues, { method: 'POST', body: donnees });
        } catch (erreur) {
            // Échec silencieux : au pire, le badge réapparaîtra au
            // prochain chargement de page — rien de grave.
        }
    }

    cloche.addEventListener('show.bs.dropdown', () => {
        if (!dejaChargees) {
            chargerNotifications(0);
            dejaChargees = true;
        }
        marquerToutesLues();
    });

    /**
     * Initialisation explicite du dropdown Bootstrap, plutôt que de
     * compter uniquement sur l'auto-détection par attribut data-bs-toggle.
     * Plus fiable : ça garantit que le menu réagit au clic même si
     * l'auto-initialisation de Bootstrap n'a pas encore eu lieu au
     * moment où ce script s'exécute.
     */
    if (window.bootstrap && window.bootstrap.Dropdown) {
        window.bootstrap.Dropdown.getOrCreateInstance(cloche);
    }
})();
</script>
<?php endif; ?>

<!-- ==================== Confirmation stylée (Jour 17) ====================
     Remplace window.confirm() — la popup native du navigateur casse
     l'identité visuelle du site et ne peut pas être stylée. Ce composant
     est posé une seule fois ici, réutilisable sur toute page via
     window.confirmerAction(message). -->
<div class="modal fade" id="modaleConfirmation" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-4 text-center">
                <p id="messageConfirmation" class="mb-4"></p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary" id="boutonAnnulerConfirmation">Annuler</button>
                    <button type="button" class="btn btn-accent" id="boutonValiderConfirmation">Confirmer</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
/**
 * confirmerAction(message)
 * Remplace window.confirm() par une modale cohérente avec le design du
 * site. Retourne une Promise<boolean> pour s'utiliser avec await, comme
 * le ferait confirm() de façon synchrone :
 *
 *   if (!(await confirmerAction("Supprimer ?"))) return;
 */
window.confirmerAction = function (message) {
    return new Promise((resoudre) => {
        const modaleElement = document.getElementById('modaleConfirmation');
        const modale = window.bootstrap.Modal.getOrCreateInstance(modaleElement);
        document.getElementById('messageConfirmation').textContent = message;

        const boutonValider = document.getElementById('boutonValiderConfirmation');
        const boutonAnnuler = document.getElementById('boutonAnnulerConfirmation');

        // On clone les boutons pour repartir d'écouteurs d'événements
        // "propres" à chaque appel — sinon les clics précédents
        // s'accumuleraient et la fonction résoudrait plusieurs fois.
        const nouveauValider = boutonValider.cloneNode(true);
        const nouveauAnnuler = boutonAnnuler.cloneNode(true);
        boutonValider.replaceWith(nouveauValider);
        boutonAnnuler.replaceWith(nouveauAnnuler);

        nouveauValider.addEventListener('click', () => { modale.hide(); resoudre(true); });
        nouveauAnnuler.addEventListener('click', () => { modale.hide(); resoudre(false); });
        modaleElement.addEventListener('hidden.bs.modal', () => resoudre(false), { once: true });

        modale.show();
    });
};
</script>

</body>
</html>

