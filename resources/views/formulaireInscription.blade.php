<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Inscription</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    </head>
    <body class="align-items-center w-100">
        @include('menuPrincipal')

        <main class="align-items-center w-100">
            <form method="POST" action="{{ route('validationFormulaireInscription') }}" class="card w-50 mx-auto mt-5 mb-5">
                @csrf
                <div class="card-body align-items-center text-center">
                    <h1 class="mb-3 card-title">
                        Inscription
                    </h1>
                    @include('messageErreur')
                    <div>
                        <div class="mb-3">
                            <i>Tous les champs sont obligatoires</i>

                        </div>
                        <!-- A FAIRE (fiche 2, partie 1, question 2) : création du formulaire d'inscription -->
                        <div class="container">
                            <div class="form-group">
                                <label for="nom">Nom :</label>
                                <input type="text" id="nom" name="nom" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="prenom">Prénom :</label>
                                <input type="text" id="prenom" name="prenom" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email :</label>
                                <input type="text" id="email" name="email" class="form-control" placeholder="votre_email@????.com" required>
                            </div>
                            <div class="form-group">
                                <label for="motdepasse1">Mot de passe :</label>
                                <input type="password" name="motdepasse1" id="motdepasse1" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="motdepasse2">Confirmer votre mot de passe :</label>
                                <input type="password" name="motdepasse2" id="motdepasse2" class="form-control" required>
                            </div>
                        </div>
                        <!-- FIN -->
                        <div class="card mb-3 text-start">
                            <div class="card-body">
                                <h5 class="card-title">Conditions d'utilisation des données à caractère personnel :</h5>
                                <p>
                                    [Le responsable de traitement] traite les données recueillies pour [finalités du traitement]. Pour en savoir plus sur la gestion de vos données personnelles et pour exercer vos droits, reportez-vous à la notice ci-jointe. N.B : distinguer dans le formulaire de collecte, par exemple via des astérisques, les données dont la fourniture est obligatoire de celles dont la fourniture est facultative  Les informations recueillies dans le questionnaire sont enregistrées dans un fichier informatisé par [coordonnées du responsable de traitement]. La base légale du traitement est [base légale du traitement].Les données marquées par un astérisque dans le questionnaire doivent obligatoirement être fournies. Dans le cas contraire, [préciser les conséquences éventuelles en cas de non-fourniture des données].Les données collectées seront communiquées aux seuls destinataires suivants : [destinataires des données].Elles sont conservées pendant [durée de conservation des données prévue par le responsable du traitement ou critères permettant de la déterminer]. Vous pouvez accéder aux données vous concernant, les rectifier, demander leur effacement ou exercer votre droit à la limitation du traitement de vos données. (en fonction de la base légale du traitement, mentionner également : Vous pouvez retirer à tout moment votre consentement au traitement de vos données ; Vous pouvez également vous opposer au traitement de vsdonnées ; Vous pouvez également exercer votre droit à la portabilité de vos données)Consultez le site cnil.fr pour plus d’informations sur vos droits.Pour exercer ces droits ou pour toute question sur le traitement de vos données dans ce dispositif, vous pouvez contacter (le cas échéant, notre délégué à la protection des données ou le service chargé de l’exercice de ces droits) : [adresse électronique, postale, coordonnées téléphoniques, etc.] Si vous estimez, après nous avoir contactés, que vos droits « Informatique et Libertés » ne sont pas respectés, vous pouvez adresser une réclamation à la CNIL.
                                </p>
                            </div>
                        </div>
                        <div class="input-group mb-3 form-check">
                            <input class="form-check-input me-3" type="checkbox" required id="acceptation">
                            <label class="form-check-label" for="acceptation">
                                J'accepte les conditions d'utilisation de mes données à caractère personnel
                            </label>                          
                        </div>
                    </div>
                    <div class="input-group d-grid gap-2">
                        <button class="btn btn-primary btn-lg" type="submit" name="boutonInscription">Valider</button>
                    </div>
                </div>
            </form>
        </main>
    </body>
</html>