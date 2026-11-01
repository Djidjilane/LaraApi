<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UE extends Model
{
    use HasFactory;
    protected $table = 'ues';

    protected $fillable = [
        'idFormation',
    'code',
        'nom',
        'crédits',
        'semestre',
        'typeUE',
        'est_valide'
    ];

    public function ue_ucue()
    {
        return $this->hasMany(UE_UCUE::class, 'idUE');
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class, 'idFormation');
    }

    // Relation many-to-many avec ECUE
    public function ecues()
    {
        return $this->belongsToMany(ECUE::class, 'u_e__u_c_u_e_s', 'idUE', 'idECUE')
                    ->withPivot('nbrHcours', 'nbrHTD', 'nbrHTPE', 'crédits_ecue')
                    ->withTimestamps();
    }
    public function validations()
    {
        return $this->hasMany(UEValidation::class, 'idUE');
    }

    // NOUVELLE : Méthode pour calculer la moyenne UE pour une inscription
    public function calculerMoyenne($idInscription)
    {
        $totalPoints = 0;
        $totalCredits = 0;
        $hasNotes = false;
    
        foreach ($this->ecues as $ecue) {
            $note = Ecue_Inscription::where('idECUE', $ecue->id)
                        ->where('idInscription', $idInscription)
                        ->first();
    
            if ($note) {
                $moyenneECUE = $note->moyenneFinale; // Utilisation de l'accesseur
                $credits = $ecue->pivot->crédits_ecue ?? 1;
    
                if ($moyenneECUE !== null) {
                    $totalPoints += $moyenneECUE * $credits;
                    $totalCredits += $credits;
                    $hasNotes = true;
                }
            }
        }
    
        return $hasNotes && $totalCredits > 0 ? $totalPoints / $totalCredits : null;
    }

}