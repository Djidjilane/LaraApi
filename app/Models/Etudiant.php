<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Etudiant extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom','prénom','matricule','date_de_naissance',
        'photo','adresse','parent1_nom','parent1_tel','parent2_nom','parent2_tel',
        'nationalité','sexe','num'
    ];
    public function inscription()
    {
        return $this->hasMany(Inscriptions::class, 'idEtudiant');
    }
    
   
}