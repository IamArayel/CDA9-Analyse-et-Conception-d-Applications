# Routes, contrôleurs, services

Toutes les routes publiques et de gestion sont préfixées par `/{_locale}`, avec `requirements: {_locale: 'fr|en'}`.

## Site public — `src/Interface/Web/`

| Route | Méthode | Contrôleur | Service applicatif | Gabarit |
| --- | --- | --- | --- | --- |
| `/{_locale}/` | GET | `AccueilController::index` | — (éventuellement le prochain départ) | `public/accueil.html.twig` |
| `/{_locale}/sorties` | GET | `EditorialController::sorties` | `ConsulterLaSaison` pour la frise | `public/sorties.html.twig` |
| `/{_locale}/bateaux` | GET | `EditorialController::flotte` | `ConsulterLaFlotte` | `public/flotte.html.twig` |
| `/{_locale}/tarifs` | GET | `EditorialController::tarifs` | `ConsulterLaGrilleTarifaire` | `public/tarifs.html.twig` |
| `/{_locale}/reserver` | GET | `CalendrierController::semaine` | `ConsulterLeCalendrier` → `VueDeJournee[]` | `reservation/calendrier.html.twig` |
| `/{_locale}/reserver/{sortie}` | GET, POST | `ReservationController::formulaire` | `CreerReservation` | `reservation/formulaire.html.twig` |
| `/{_locale}/reserver/{reference}/payer` | POST | `PaiementController::demarrer` | `EncaisserLAcompte` | redirection Stripe |
| `/{_locale}/reservation/{reference}` | GET | `ReservationController::confirmation` | `ConsulterUneReservation` → `VueDeReservation` | `reservation/confirmation.html.twig` |
| `/{_locale}/reservation/{reference}.ics` | GET | `ReservationController::agenda` | idem | réponse `text/calendar` |
| `/{_locale}/bon-cadeau` | GET, POST | `BonCadeauController::acheter` | `AcheterUnBonCadeau` | `bon-cadeau/acheter.html.twig` |
| `/{_locale}/code/verifier` | POST | `CodeController::verifier` | `ApplicationDunCode` → `VueDeCode` | fragment Turbo / JSON |

## Espace de gestion — `src/Interface/Web/Gestion/`

Pare-feu `gestion` sur `^/(fr|en)/gestion`, hors `/connexion`.

| Route | Méthode | Contrôleur | Service applicatif | Gabarit |
| --- | --- | --- | --- | --- |
| `/{_locale}/gestion/connexion` | GET, POST | `SecuriteController::connexion` | pare-feu Symfony | `gestion/connexion.html.twig` |
| `/{_locale}/gestion` | GET | `JourneeController::index` | `ConsulterLaJournee` → `VueDeJournee` | `gestion/journee.html.twig` |
| `/{_locale}/gestion/creneau/{id}` | GET | `CreneauController::detail` | `ConsulterUnCreneau` → `VueDeCreneau` | `gestion/creneau.html.twig` |
| `/{_locale}/gestion/creneau/{id}/maintenir` | POST | `CreneauController::maintenir` | `MaintenirLaSortie` | redirection |
| `/{_locale}/gestion/creneau/{id}/alerte` | GET, POST | `AlerteController::mettreEnAlerte` | `MettreEnAlerte` | `gestion/alerte.html.twig` |
| `/{_locale}/gestion/creneau/{id}/annuler` | GET, POST | `AnnulationController::creneau` | `AnnulerCreneau` | `gestion/annulation.html.twig` |
| `/{_locale}/gestion/reservation/{id}/issue` | POST | `AnnulationController::issue` | `EnregistrerLIssue` | redirection |
| `/{_locale}/gestion/reglages` | GET, POST | `ReglagesController::index` | `ConsulterLesParametres` | `gestion/reglages.html.twig` |
| `/{_locale}/gestion/reglages/tarifs` | POST | `ReglagesController::tarifs` | `ModifierLaGrilleTarifaire` | redirection |
| `/{_locale}/gestion/reglages/horaires` | POST | `ReglagesController::horaires` | `ModifierLesHoraires` | redirection |
| `/{_locale}/gestion/reglages/fermeture` | POST | `ReglagesController::fermeture` | `DeclarerUnJourDeFermeture` | redirection |
| `/{_locale}/gestion/reglages/bateau` | POST | `ReglagesController::bateau` | `CreerUnBateau` | redirection |

## Services applicatifs à écrire

`src/Application/` ne couvre aujourd'hui que ce que les tests appellent. Les lectures manquent presque toutes : `ConsulterLeCalendrier`, `ConsulterLaJournee`, `ConsulterUnCreneau`, `ConsulterUneReservation`, `ConsulterLaGrilleTarifaire`, `ConsulterLaFlotte`, `ConsulterLesParametres`. Chacune retourne une **Vue** — jamais une entité — et n'ouvre pas de transaction.
