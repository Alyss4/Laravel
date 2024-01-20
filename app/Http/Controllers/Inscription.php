<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use App\Models\Log;
use App\Models\Reactivation;

/* A FAIRE (fiche 2, partie 2, question 2) : inclure ci-dessous les use PHP pour les librairies gérant l'A2F */

// CORRIGÉ
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class Inscription extends Controller
{
    public function afficherFormulaireInscription() {
        return view('formulaireInscription', []);
    }

    public function boutonInscription() {
        if(isset($_POST["boutonInscription"])) {
            $validationFormulaire = true; // Booléen qui indique si les données du formulaire sont valides
            $messagesErreur = array(); // Tableau contenant les messages d'erreur à afficher
            /* A FAIRE (fiche 2, partie 1, question 6) : vérification du formulaire d'inscription */
            //verif champ nom
            $nom = htmlspecialchars($_POST['nom']);
            if (empty($nom)){
                $validationFormulaire = false;
                $messagesErreur[] = " Veuillez saisir un nom ! ";
            }
            //verif champ  prenom
            $prenom = htmlspecialchars($_POST['prenom']);
            if (empty($prenom)){
                $validationFormulaire = false;
                $messagesErreur[] = " Veuillez saisir un prenom ! ";
            }
            //verif champ mail
            $email = htmlspecialchars($_POST['email']);
            if (empty($email)){
                $validationFormulaire = false;
                $messagesErreur[] = " Veuillez saisir un mail ! ";
            }
            //verif champ mot de passe
            $verifmdp = "/^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[!@#%^&*()\$_+÷%§€\-=\[\]{}|;':\",.\/<>?~`]).{12,}$/";
            $motdepasse1 = $_POST["motdepasse1"];
            $motdepasse2 = $_POST["motdepasse2"];
            /*if (empty($motdepasse1) || empty($motdepasse2)){
                $validationFormulaire = false;
                $messagesErreur = " Veuillez saisir un mot de passe ! ";
            }*/
            if ($motdepasse1 === $motdepasse2){
                if(preg_match($verifmdp, $motdepasse1)){
                    $validationFormulaire = true;
                }
            }else{
                $validationFormulaire = false;
                $messagesErreur[] = " Vos mots de passe de correspondent pas ! ";
            }

            if($validationFormulaire === false) {
                return view('formulaireInscription', ["messagesErreur" => $messagesErreur]);
            }
            else {
                $qrCode = null;
                /* A FAIRE (fiche 2, partie 2, question 2) : générer le secret A2F et le QR code */
                //secret a2f
                $google2fa = new Google2FA;
                $secretA2F = $google2fa->generateSecretKey();
                //codeqr
                $qrCodeURL = $google2fa->getQRCodeUrl('AUTH-APP',$_POST['email'],$secretA2F);
                $genereqr = new ImageRenderer(
                            new RendererStyle(400),
                            new ImagickImageBackEnd()
                );
                $writer = new Writer($genereqr);
                $qrCode = base64_encode($writer->writeString($qrCodeURL));

                /* A FAIRE (fiche 2, partie 1, question 7) : on inscrit l'utilisateur dans la base + écriture dans les logs */
                Utilisateur::inscription($email,$motdepasse1,$nom,$prenom,$secretA2F);
                Log::ecrireLog($email,"Inscription");


                return view('confirmationInscription', ["qrCode" => $qrCode]);
            }
        }
    }
}
