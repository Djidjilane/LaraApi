<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class ECUE extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'référence'
    ];
    protected $table = 'e_c_u_e_s'; // ou le nom exact de votre table

    
     public function ue_ucue()
    {
        return $this->hasMany(UE_UCUE::class, 'idECUE');
    }
    public function ecue_inscriptions()
    {
        return $this->hasMany(Ecue_Inscription::class, 'idECUE');
    }
    // Dans ECUE.php
    public function ues()
    {
        return $this->belongsToMany(UE::class, 'u_e__u_c_u_e_s', 'idECUE', 'idUE')
                    ->withPivot('nbrHcours', 'nbrHTD', 'nbrHTPE', 'crédits_ecue');
    }

    // Méthode pour récupérer la première UE (utile si un ECUE n'appartient qu'à une UE)
    public function getUeAttribute()
    {
        return $this->ues->first();
    }
    public function ue_validations()
    {
        return $this->hasManyThrough(
            UEValidation::class,
            UE::class,
            'id', // Clé primaire de UE
            'idUE', // Clé étrangère dans UEValidation
            'id', // Clé primaire de ECUE
            'id' // Clé primaire de UE
        );
    }

    
}
