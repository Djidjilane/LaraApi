// app/Models/ReglePassage.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReglePassage extends Model
{
    protected $fillable = [
        'idFormation',
        'pourcentage_minimal',
        'credits_minimaux',
        'est_actif'
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class, 'idFormation');
    }
}