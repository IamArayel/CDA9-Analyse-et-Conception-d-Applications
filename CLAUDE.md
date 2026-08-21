# Ti Baleine — règles de travail

Projet Symfony 8 en architecture hexagonale. Avant d'écrire une ligne, lire `docs/architecture.md` §2 et §4.

## Couches — ce qui est interdit où

- `src/Interface/` : reçoit, valide la forme, affiche. Pas de calcul métier, pas de SQL, pas d'appel à Stripe, pas de `new \DateTimeImmutable()`.
- `src/Application/` : un service par cas d'usage, ouvre et ferme la transaction. Pas de règle métier, pas de HTML.
- `src/Domaine/` : les règles, et rien d'autre. Ni Symfony, ni Doctrine, ni heure système (`ADR-005` — l'horloge est injectée).
- `src/Infrastructure/` : persistance et systèmes extérieurs. Aucune décision de cas d'usage.

Un contrôleur appelle un service applicatif et passe la **Vue** retournée au gabarit. Il ne manipule jamais une entité.

## Traductions

Aucune chaîne en dur dans un contrôleur ou un gabarit. Tout passe par `translations/messages.{fr,en}.yaml`, qui doivent porter **exactement les mêmes clés** — un test le vérifie. Domaines de clés : `site.*` (éditorial), `parcours.*` (réservation), `gestion.*`, `message.*` (notifications, déjà en place), `erreur.*`.

Montants, dates et heures : `format_currency`, `format_date`, `format_time` avec la locale. Jamais un format écrit à la main.

Routes préfixées : `/{_locale}` avec `_locale: fr|en`.

## Style

Une seule feuille `assets/styles/tokens.css` porte les variables ; les gabarits n'écrivent aucune valeur en dur. Source Serif 4 partout, aucun arrondi, aucune ombre, séparations au filet `1px`. Palette : voir `tokens.css`.

## Tests

Chaque écran livré s'accompagne d'un `WebTestCase` sur son parcours nominal et d'un cas d'échec (place prise, code invalide, session expirée selon l'écran).
