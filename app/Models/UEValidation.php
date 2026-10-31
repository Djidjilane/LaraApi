<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UEValidation extends Model
{
    use HasFactory;

    protected $table = 'ue_validations';

    protected $fillable = [
        'idUE',
        'idInscription',
        'moyenne',
        'est_reprise'
    ];

    public function ue()
    {
        return $this->belongsTo(UE::class, 'idUE');
    }

    public function inscription()
    {
        return $this->belongsTo(Inscriptions::class, 'idInscription');
    }

    // Méthode pour vérifier si l'UE est validée
    public function getEstValideeAttribute()
    {
        // L'UE est validée si elle n'est pas en reprise ET que la moyenne est >= 10
        return !$this->est_reprise && $this->moyenne >= 10;
    }

    public function getStatutAttribute()
    {
        if ($this->est_reprise) {
            return $this->moyenne < 10 ? 'En reprise' : 'Validé après reprise';
        }
        return $this->moyenne >= 10 ? 'Validé' : 'Non validé';
    }
}