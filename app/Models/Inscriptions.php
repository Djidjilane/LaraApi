<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inscriptions extends Model
{
    use HasFactory;
    protected $fillable = [
        'date_inscription', 'modalité_paiement','etat','année_scolaire','idFormation','idEtudiant',

    ];
    public function formation()
    {
        return $this->belongsTo(Formation::class, 'idFormation');
    }
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'idEtudiant');
    }
    public function note()
    {
        return $this->hasMany(Ecue_Inscription::class, 'idInscription'); // Spécifier la clé étrangère
    }
    //
    public function ue_validations()
    {
        return $this->hasMany(UEValidation::class, 'idInscription');
    }

    // NOUVELLE : Relation avec les reprises
    public function reprises()
    {
        return $this->hasManyThrough(
            Ecue_Inscription::class,
            UE::class,
            'idFormation', // Clé étrangère dans UE
            'idECUE', // Clé étrangère dans Ecue_Inscription
            'idFormation', // Clé locale dans Inscriptions
            'id' // Clé primaire dans UE
        )->where('est_reprise', true);
    }
    public function calculerPourcentageUEValidees()
    {
        // Récupérer toutes les UE de la formation
        $totalUE = $this->formation->UE()->count();
        
        if ($totalUE === 0) {
            return 100; // Aucune UE à valider
        }
        
        // Compter les UE validées pour cette inscription
        $UEValidees = $this->UEValidation()
                          ->where('est_valide', true)
                          ->count();
        
        return ($UEValidees / $totalUE) * 100;
    }
    
    // NOUVELLE : Méthode pour vérifier si l'étudiant peut passer en classe supérieure (70% d'UE)
    public function peutPasserClasseSuperieure()
    {
        return $this->calculerPourcentageUEValidees() >= 70;
    }
    
    // NOUVELLE : Méthode pour vérifier l'éligibilité à l'inscription
    public static function verifierEligibiliteInscription($idEtudiant, $idFormationCible, $typeInscription = 'nouveau')
    {
        // Trouver la dernière inscription de l'étudiant
        $derniereInscription = self::with('formation')
            ->where('idEtudiant', $idEtudiant)
            ->orderBy('année_scolaire', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
        
        // Si pas d'inscription précédente, première inscription
        if (!$derniereInscription) {
            return [
                'peut_s_inscrire' => true,
                'raison' => 'Première inscription',
                'type' => 'premiere_inscription'
            ];
        }
        
        $formationActuelle = $derniereInscription->formation;
        $formationCible = Formation::find($idFormationCible);
        
        if (!$formationCible) {
            return [
                'peut_s_inscrire' => false,
                'raison' => 'Formation cible introuvable',
                'type' => 'erreur'
            ];
        }
        
        // Vérifier le type d'inscription
        switch ($typeInscription) {
            case 'redoublement':
                return self::verifierRedoublement($derniereInscription, $formationActuelle, $formationCible);
                
            case 'reprise':
                return self::verifierReprise($derniereInscription, $formationActuelle, $formationCible);
                
            case 'nouveau':
            default:
                return self::verifierPassageClasseSuperieure($derniereInscription, $formationActuelle, $formationCible);
        }
    }
    
    // NOUVELLE : Vérification pour redoublement
    protected static function verifierRedoublement($inscription, $formationActuelle, $formationCible)
    {
        // Pour redoublement, vérifier que c'est la même formation
        if (!$formationCible->estMemeNiveau($formationActuelle)) {
            return [
                'peut_s_inscrire' => false,
                'raison' => 'Redoublement impossible : la formation cible est différente de la formation actuelle',
                'type' => 'redoublement_impossible',
                'details' => [
                    'formation_actuelle' => $formationActuelle->nom . ' (' . $formationActuelle->cycle . ' ' . $formationActuelle->niveau . ')',
                    'formation_cible' => $formationCible->nom . ' (' . $formationCible->cycle . ' ' . $formationCible->niveau . ')'
                ]
            ];
        }
        
        return [
            'peut_s_inscrire' => true,
            'raison' => 'Redoublement autorisé',
            'type' => 'redoublement',
            'pourcentage_valide' => $inscription->calculerPourcentageUEValidees()
        ];
    }
    
    // NOUVELLE : Vérification pour reprise d'études
    protected static function verifierReprise($inscription, $formationActuelle, $formationCible)
    {
        // Pour reprise, on autorise même si la formation n'est pas supérieure
        // Mais on vérifie quand même que ce n'est pas une formation antérieure
        if ($formationCible->getOrdreTotalAttribute() < $formationActuelle->getOrdreTotalAttribute()) {
            return [
                'peut_s_inscrire' => false,
                'raison' => 'Reprise impossible : la formation cible est antérieure à la formation actuelle',
                'type' => 'reprise_impossible'
            ];
        }
        
        return [
            'peut_s_inscrire' => true,
            'raison' => 'Reprise d\'études autorisée',
            'type' => 'reprise'
        ];
    }
    
    // NOUVELLE : Vérification pour passage en classe supérieure
    protected static function verifierPassageClasseSuperieure($inscription, $formationActuelle, $formationCible)
    {
        // Vérifier si la formation cible est supérieure
        if (!$formationCible->estSuperieureA($formationActuelle)) {
            return [
                'peut_s_inscrire' => false,
                'raison' => 'La formation cible n\'est pas supérieure à la formation actuelle',
                'type' => 'formation_non_superieure',
                'details' => [
                    'formation_actuelle' => $formationActuelle->nom . ' (' . $formationActuelle->cycle . ' ' . $formationActuelle->niveau . ')',
                    'formation_cible' => $formationCible->nom . ' (' . $formationCible->cycle . ' ' . $formationCible->niveau . ')',
                    'ordre_actuel' => $formationActuelle->getOrdreTotalAttribute(),
                    'ordre_cible' => $formationCible->getOrdreTotalAttribute()
                ]
            ];
        }
        
        // Vérifier si c'est directement la formation suivante
        $estFormationSuivante = $formationCible->estFormationSuivante($formationActuelle);
        
        // Si c'est la formation suivante, vérifier les 70%
        if ($estFormationSuivante) {
            if ($inscription->peutPasserClasseSuperieure()) {
                return [
                    'peut_s_inscrire' => true,
                    'raison' => 'UE validées ≥ 70% pour passage en classe supérieure',
                    'type' => 'passage_classe_superieure',
                    'pourcentage_valide' => $inscription->calculerPourcentageUEValidees()
                ];
            } else {
                return [
                    'peut_s_inscrire' => false,
                    'raison' => 'Moins de 70% des UE validées pour passage en classe supérieure',
                    'type' => 'ue_insuffisantes',
                    'pourcentage_valide' => $inscription->calculerPourcentageUEValidees()
                ];
            }
        } 
        else {
            // Si ce n'est pas la formation suivante directe (saut de niveau),
            // appliquer des règles plus strictes
            if ($inscription->calculerPourcentageUEValidees() >= 90) {
                return [
                    'peut_s_inscrire' => true,
                    'raison' => 'Excellent dossier (≥90% UE validées) - passage exceptionnel autorisé',
                    'type' => 'passage_exceptionnel',
                    'pourcentage_valide' => $inscription->calculerPourcentageUEValidees()
                ];
            } else {
                return [
                    'peut_s_inscrire' => false,
                    'raison' => 'Saut de niveau non autorisé (requiert ≥90% UE validées)',
                    'type' => 'saut_niveau_interdit',
                    'pourcentage_valide' => $inscription->calculerPourcentageUEValidees(),
                    'details' => [
                        'pourcentage_requis' => 90,
                        'pourcentage_actuel' => $inscription->calculerPourcentageUEValidees()
                    ]
                ];
            }
        }
    }
    
    // NOUVELLE : Méthode pour obtenir l'historique des formations
    public static function getHistoriqueFormations($idEtudiant)
    {
        return self::with('formation')
            ->where('idEtudiant', $idEtudiant)
            ->orderBy('année_scolaire', 'asc')
            ->get()
            ->map(function($inscription) {
                return [
                    'annee_scolaire' => $inscription->année_scolaire,
                    'formation' => $inscription->formation->nom,
                    'cycle' => $inscription->formation->cycle,
                    'niveau' => $inscription->formation->niveau,
                    'etat' => $inscription->etat,
                    'pourcentage_ue_validees' => $inscription->calculerPourcentageUEValidees(),
                    'ordre_total' => $inscription->formation->getOrdreTotalAttribute()
                ];
            });
    }

}
