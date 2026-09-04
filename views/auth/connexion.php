<div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="mb-4 text-center">Connexion</h2>

                <?php if (!empty($messageSucces)): ?>
                    <div class="alert alert-success"><?= nettoyer($messageSucces) ?></div>
                <?php endif; ?>

                <?php if (!empty($erreurs)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($erreurs as $erreur): ?>
                            <div><?= nettoyer($erreur) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('connexion') ?>">
                    <input type="hidden" name="csrf_token" value="<?= genererTokenCSRF() ?>">

                    <div class="mb-3">
                        <label class="form-label">Adresse email</label>
                        <input type="email" name="email" class="form-control" value="<?= nettoyer($emailPrefill) ?>"
                               autocomplete="username" required <?= $emailPrefill ? '' : 'autofocus' ?>>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Mot de passe</label>
                        <!-- autocomplete="current-password" : c'est ce qui permet
                             au navigateur de proposer d'enregistrer/remplir ce
                             mot de passe via SON PROPRE gestionnaire sécurisé —
                             la bonne façon de retrouver un mot de passe déjà
                             utilisé, sans jamais le stocker nous-mêmes côté
                             serveur ou dans un cookie. -->
                        <input type="password" name="mot_de_passe" class="form-control"
                               value="<?= nettoyer($motDePassePrefill) ?>" autocomplete="current-password" required
                               <?= $emailPrefill && !$motDePassePrefill ? 'autofocus' : '' ?>> <br>
                        <a href="<?= url('mot-de-passe-oublie') ?>" class="small d-block mt-1">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>

                <p class="text-center mt-3 mb-0 small">
                    Pas encore de compte ? <a href="<?= url('inscription') ?>">Inscrivez-vous</a>
                </p>
            </div>
        </div>
    </div>
</div>
