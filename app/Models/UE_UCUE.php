<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UE_UCUE extends Model
 {
    use HasFactory;
    protected $fillable = [
        'idUE',
        'idECUE',
    
        'nbrHcours',
        'nbrHTD',
        'nbrHTPE',
        'crédits_ecue',
        
    ];

    public function ue()
    {
        return $this->belongsTo(UE::class, 'idUE');
    }
    public function ecue()
    {
        return $this->belongsTo(ECUE::class, 'idECUE');
    }

    
}
