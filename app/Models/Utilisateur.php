<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Email;
class Utilisateur extends Model
{
    protected $table = 'utilisateur';
    protected $primaryKey = 'idUtilisateur';
    public $timestamps = false;

    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }
    public static function existeEmail($email) {
        $nb = self::where("emailUtilisateur", $email)->count();

        if($nb > 0) {
            return true;
        }
        else {
            return false;
        }
    }
    public function tentativesEchoueesUtilisateur(){
        $this->tentativesEchoueesUtilisateur++;
        if ($this->tentativesEchoueesUtilisateur >=5){
            $this->tentativesEchoueesUtilisateur = 5;
            $this->desactiverCompte();
        }
        $this->save();
    }
    public function desactiverCompte() {
        $this->estDesactiveUtilisateur = 1;
        $this->save();

        $destinataire = $this->emailUtilisateur;
        $sujet = "Désactivation du comptes !";
        $codeUnique = Reactivation::creerCodeReactivation($this);
        $lien = 'http://5.135.160.83:8888/reactivation?code='.$codeUnique;
        $corpsMessage =  "Le compte à été désactiver en raison de 5 tentatives cliquez ici pour le réactiver :".$lien;
        Email::envoyerEmail($destinataire, $sujet,$corpsMessage);
        $this->save();
    }

    public function reactiverCompte() {
        $this->estDesactiveUtilisateur = 0;
        $this->tentativesEchoueesUtilisateur = 0;
        $this->save();
    }

    public static function inscription($email, $motDePasseHache, $nom, $prenom, $secretA2F) {
        $nouvelUtilisateur = new Utilisateur();
        $nouvelUtilisateur->emailUtilisateur = $email;
        $nouvelUtilisateur->motDePasseUtilisateur = Hash::make($motDePasseHache);
        $nouvelUtilisateur->nomUtilisateur = $nom;
        $nouvelUtilisateur->prenomUtilisateur = $prenom;
        $nouvelUtilisateur->secretA2FUtilisateur = $secretA2F;
        $nouvelUtilisateur->save();
    }
}