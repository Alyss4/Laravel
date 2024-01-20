<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utilisateur;
use App\Models\Log;
use App\Models\Reactivation;
use App\Http\Controllers\Email;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Cookie;

/* A FAIRE (fiche 3, partie 2, question 1) : inclure ci-dessous le use PHP pour la libriairie gérant l'A2F */

// A FAIRE (fiche 3, partie 3, question 4) : inclure ci-dessous le use PHP pour la libriairie gérant le JWT

class Connexion extends Controller
{
    public function afficherFormulaireConnexion() {
        return view('formulaireConnexion', []);
    }

    public function afficherFormulaireVerificationA2F() {
        if(session()->has('connexion')) {
            if(Utilisateur::where("idUtilisateur", session()->get('connexion'))->count() > 0) {
                return view('formulaireA2F', []);
            }
            else {
                session()->forget('connexion');
                return view('formulaireConnexion', []);
            }
        }
        else {
            return view('formulaireConnexion', []);
        }
    }

    public function reactivationCompte() {
        $validation = false; // Booléen vrai/faux si les conditions de vérification sont remplies pour réactiver le compte
        $messageAAfficher = null; // Contient le message d'erreur ou de succès à afficher 
        if(isset($_GET['code'])){
            $code = $_GET['code'];
            if (Reactivation::estValide($code)){
                $utilisateur = Reactivation::getUtilisateurConcerne($code);
                $validation = true;
                $email=$utilisateur->emailUtilisateur;
                Log::ecrireLog($email,"Compte réactivé".$email);
                $messageAAfficher = "Compte réactivé";
                $utilisateur->reactiverCompte();
                $lignebdd = Reactivation::where('codeReactivation', $code)->first();
                $lignebdd->supprimerReactivation();
            }else{
                $messageAAfficher = "Le compte n'a pas été reactivé!";
            }
            /* A FAIRE (fiche 3, partie 1, question 4) : vérification du code dans l'URL ainsi que de l'expiration du lien + réactivation du compte */       
        }
        if($validation === false) {
            return view("pageErreur", ["messageErreur" => $messageAAfficher]);
        }
        else {
            $messageAAfficher="Compte réactivé";
            return view('confirmation', ["messageConfirmation" => $messageAAfficher]);
        }
    }

    public function boutonVerificationCodeA2F() {
        $validationFormulaire = false; // Booléen qui indique si les données du formulaire sont valides
        $messagesErreur = array(); // Tableau contenant les messages d'erreur à afficher
        /* A FAIRE (fiche 3, partie 2, question 1) : vérification du code A2F */
        if (isset($_POST['codeA2F'])){
            $codeA2F = $_POST['codeA2F'];
            $idUtilisateur = session()->get('connexion');
            $utilisateur = Utilisateur::where('idUtilisateur',$idUtilisateur)->first();
            $email=$utilisateur->emailUtilisateur;

            if ($utilisateur){
                $google2fa = new Google2FA();
                $secretA2FUtilisateur = $utilisateur->secretA2FUtilisateur;
                if ($google2fa->verifyKey($secretA2FUtilisateur, $codeA2F)) {
                    /* A FAIRE (fiche 3, partie 3, question 4) : générer un JWT une fois le code A2F validé + création du cookie + redirection vers la page de profil */
                    $validationFormulaire = true;
                    $keycode='T3mUjGjhC6WuxyNGR2rkUt2uQgrlFUHx';
                    $payload = [
                        'name' => $email,
                        'sub' => $utilisateur->idUtilisateur,
                        'iat' => time()
                    ];
                    $jwt = JWT::encode($payload,$keycode,'HS256');
                    Log::ecrireLog($email,"Connexion réussie");
                    setcookie('auth',$jwt,(time()+(60*60*24*30)), "/");
                    //Cookie::queue('auth',$jwt,(time()+(60*60*24*30)), "/");
                    session()->forget('connexion');
                    return redirect()->to('profil')->send();

                }else{
                    $messageAAfficher = "Erreur dans la saisie du code a2f";
                    Log::ecrireLog($email,"Erreur dans la saisie du code a2f !");
                    return view("pageErreur", ["messageErreur" => $messageAAfficher]);

                }
            } else {
                $messageAAfficher = "Utilisateur non trouvé";
                Log::ecrireLog($email,"Utilisateur non trouvé");
                return view("pageErreur", ["messageErreur" => $messageAAfficher]);
            }
        }else {
            $messageAAfficher = "Pb Isset Post a2f non trouvé";
            Log::ecrireLog($email,"Pb Isset Post a2f non trouvé");
            return view("pageErreur", ["messageErreur" => $messageAAfficher]);
        }

    }
    
    public function boutonConnexion() {
        $validationFormulaire = false; // Booléen qui indique si les données du formulaire sont valides
        $messagesErreur = array(); // Tableau contenant les messages d'erreur à afficher
        /* A FAIRE (fiche 3, partie 1, question 3) : vérification du couple login/mot de passe */
        $email = $_POST['email'];
        $motdepasse = $_POST['motdepasse'];
        $utilisateur = Utilisateur::where('emailUtilisateur', $email)->first();
        if (!$utilisateur) {
            $validationFormulaire = false;
            $messagesErreur[]= "l'utilisateur est inexistant";
        } elseif (!Hash::check($motdepasse, $utilisateur->motDePasseUtilisateur)){
            $utilisateur->tentativesEchoueesUtilisateur();
            Log::ecrireLog($email,"Le mot de passe ne correspond pas ! Tentative : ".$utilisateur->tentativesEchoueesUtilisateur."/5");
            $messagesErreur[]= "Le mot de passe ne correspond pas ! Tentative : ".$utilisateur->tentativesEchoueesUtilisateur."/5";
            if ($utilisateur->tentativesEchoueesUtilisateur === 5){
                $messagesErreur[]= "Compte désactiver 5 tentatives de connexion !";
                Log::ecrireLog($email,"Compte désactiver 5 tentatives de connexion !".$email);
            }
        }else if($utilisateur->estDesactiveUtilisateur ===1){
            $messagesErreur[]= "Compte désactivé";
        }else{
           session(['connexion' => $utilisateur->idUtilisateur]);
           Log::ecrireLog($email,"Connexion réussie !");
           return view('formulaireA2F');
        } 
        if($validationFormulaire === false) {
            return view('formulaireConnexion', ["messagesErreur" => $messagesErreur]);
        }else {
            return view('formulaireA2F', []);
        }
    }

    public function deconnexion() {
        if(session()->has('connexion')) {
            session()->forget('connexion');
        }
        if(isset($_COOKIE["auth"])) {
            setcookie("auth", "", time()-3600);
        }

        return redirect()->to('connexion')->send();
    }

    public function validationFormulaire() {
        if(isset($_POST["boutonVerificationCodeA2F"])) {
            return $this->boutonVerificationCodeA2F();
        }
        else {
            if(isset($_POST["boutonConnexion"])) {
                return $this->boutonConnexion();
            }
            else {
                return redirect()->to('connexion')->send();
            }
        }
    }


}