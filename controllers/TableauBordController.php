<?php
/**
 * controllers/TableauBordController.php
 * -----------------------------------------
 */
require_once __DIR__ . '/../models/Bien.php';
require_once __DIR__ . '/../models/Avis.php';
require_once __DIR__ . '/../models/ReservationVisite.php';

class TableauBordController
{
    public function index(): void
    {
        exigerRole('proprietaire');

        $titrePage = 'Mon tableau de bord';
        $proprietaireId = (int) $_SESSION['utilisateur_id'];

        $bienModel = new Bien();
        $mesBiens = $bienModel->listerAvecStatistiquesParProprietaire($proprietaireId);
        $vuesParJour = $bienModel->vuesParJourPourProprietaire($proprietaireId, 30);

        $avisModel = new Avis();
        $avisRecents = $avisModel->recentsPourProprietaire($proprietaireId, 5);
        $noteMoyenne = $avisModel->moyennePourProprietaire($proprietaireId);

        $reservationModel = new ReservationVisite();
        $visitesEnAttente = array_filter(
            $reservationModel->listerPourProprietaire($proprietaireId),
            fn($v) => $v['statut'] === 'en_attente'
        );

        // Totaux agrégés pour les cartes de résumé en haut de page.
        $totalVues = array_sum(array_column($mesBiens, 'nb_vues'));
        $totalFavoris = array_sum(array_column($mesBiens, 'nb_favoris'));

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/tableau-bord/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
}
