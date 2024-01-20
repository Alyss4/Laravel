<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Utilisateur;

class Log extends Model
{
    protected $table = 'log';
    protected $primaryKey = 'idLog';
    public $timestamps = false;

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public static function ecrireLog($emailUtilisateur, $typeAction) {
        // A FAIRE (fiche 2, partie 1, question 6) : écriture dans les logs
        $utilisateur = Utilisateur::where("emailUtilisateur", $emailUtilisateur)->first();
         if ($utilisateur) {
        $adresseIP = request()->ip();
        $log = new Log();
        $log->typeActionLog = $typeAction;
        $log->adresseIPLog = $adresseIP;
        $log->idUtilisateur = $utilisateur->idUtilisateur;
        $log->save();
        }
    }
}