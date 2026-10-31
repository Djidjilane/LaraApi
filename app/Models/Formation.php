<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Formation extends Model
{
        use HasFactory;

    protected $fillable = [
        'nom',
        'niveau',
        'cycle',
        'désignation'
    ];
        public function UE()
    {
        return $this->hasMany(UE::class, 'idFormation');
    }
    public function inscription()
    {
        return $this->hasMany(Inscriptions::class, 'idFormation');
    }

    public function note()
    {
        return $this->hasMany(UE_UCUE::class, 'idFormation');
    }
    public function ecues()
    {
        return ECUE::whereHas('ues', function($query) {
            $query->where('idFormation', $this->id);
        })->distinct();
    }
    public function estSuperieureA(Formation $autreFormation)
    {
        // Comparer d'abord les cycles
        $cycles = ['Licence' => 1, 'Master' => 2, 'Doctorat' => 3];
        $cycleActuel = $cycles[$this->cycle] ?? 0;
        $cycleAutre = $cycles[$autreFormation->cycle] ?? 0;
        
        if ($cycleActuel > $cycleAutre) {
            return true;
        }
        
        // Même cycle, comparer les niveaux
        if ($cycleActuel == $cycleAutre) {
            return $this->niveau > $autreFormation->niveau;
        }
        
        return false;
    }
    
    // NOUVELLE : Méthode pour vérifier si c'est la même formation
    public function estMemeNiveau(Formation $autreFormation)
    {
        return $this->cycle === $autreFormation->cycle && $this->niveau == $autreFormation->niveau;
    }
    
    // NOUVELLE : Méthode pour vérifier si c'est la formation suivante (niveau +1 dans le même cycle)
    public function estFormationSuivante(Formation $autreFormation)
    {
        if ($this->cycle !== $autreFormation->cycle) {
            return false;
        }
        
        return $this->niveau == ($autreFormation->niveau + 1);
    }
    
    // NOUVELLE : Méthode pour obtenir l'ordre numérique total
    public function getOrdreTotalAttribute()
    {
        $cycles = ['Licence' => 0, 'Master' => 10, 'Doctorat' => 20];
        return ($cycles[$this->cycle] ?? 0) + $this->niveau;
    }


}
