<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ecue_Inscription extends Model
{
    use HasFactory;
    protected $table = 'ecue_inscription'; // Nom corrigé de la table

    protected $fillable = [
        'idECUE',
        'idInscription',
        'note1_controle_continu',
        'note2_controle_continu',
        'note_examen_terminal',
        'note_rattrapage',
        'date_evaluation', 
        'moyenne',
        'note_reprise', // NOUVEAU

    ];


public function inscription()
{
    return $this->belongsTo(Inscriptions::class, 'idInscription');
}
    public function ecue()
    {
        return $this->belongsTo(ECUE::class, 'idECUE');
    }
    public function ue()
    {
        return $this->hasOneThrough(
            UE::class,
            ECUE::class,
            'id', // Clé primaire de ECUE
            'id', // Clé primaire de UE
            'idECUE', // Clé étrangère dans ecue_etudiant
            'idUE' // Clé étrangère dans ECUE
        );
    }
    public function getMoyenneFinaleAttribute()
    {
        // Si reprise, utiliser la note de reprise
        if ($this->est_reprise && $this->note_reprise !== null) {
            return $this->note_reprise;
        }
    
        // Sinon, utiliser la moyenne calculée (ou calculer si nécessaire)
        if ($this->moyenne !== null) {
            return $this->moyenne;
        }
    
        // Calculer la moyenne à partir des notes de contrôle continu et d'examen
        $moyenneCC = ($this->note1_controle_continu + $this->note2_controle_continu) / 2;
        $noteExamen = $this->note_examen_terminal ?? 0;
        
        if ($this->note_rattrapage !== null) {
            $noteExamen = max($noteExamen, $this->note_rattrapage);
        }
        
        return ($moyenneCC * 0.4) + ($noteExamen * 0.6);
    }
    // NOUVELLE : Méthode pour savoir si l'ECUE est validé
    public function getEstValideAttribute()
    {
        return $this->moyenneFinale !== null && $this->moyenneFinale >= 10;
    }

    protected static function booted()
    {
        // Après chaque sauvegarde, mettre à jour les UE concernées
     // Dans Ecue_Inscription.php
        static::saved(function ($ecueInscription) {
            try {
                $ecue = $ecueInscription->ecue()->with('ues')->first();
                if ($ecue) {
                    foreach ($ecue->ues as $ue) {
                        app()->make(\App\Http\Controllers\NoteController::class)
                        ->mettreAJourUEValidation($ecueInscription->idInscription, $ue->id);
                    }
                }
            } catch (\Exception $e) {
                 Log::error(...);
            }
        });

        // Après chaque suppression, mettre à jour aussi
        static::deleted(function ($ecueInscription) {
            try {
                $ecue = $ecueInscription->ecue()->with('ues')->first();
                
                if ($ecue) {
                    foreach ($ecue->ues as $ue) {
                        app()->make(\App\Http\Controllers\NoteController::class)
                            ->mettreAJourUEValidation($ecueInscription->idInscription, $ue->id);
                    }
                }
            } catch (\Exception $e) {
                Log::error("Erreur lors de la mise à jour UEValidation après suppression Ecue_Inscription: " . $e->getMessage());
            }
        });
    }


}