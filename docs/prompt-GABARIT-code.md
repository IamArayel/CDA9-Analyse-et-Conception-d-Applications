# Gabarit de prompt — faire écrire le code

À recopier dans l'agent, **une fois par tâche**. Ce fichier ne se remplit pas : il
se recopie.

Une tâche = un test qui passe du rouge au vert. Si vous ne savez pas quel test va
changer d'état, la tâche est mal découpée — revenez à `delegation-<SPEC>.md`.

> Le pendant de ce gabarit est `prompt-GABARIT-test.md`, qui sert à faire écrire le
> test. La direction s'inverse : là-bas le cas de test est le contrat et l'agent
> produit le test ; ici le test est le contrat et l'agent produit le code.

---

## Le gabarit

```text
CONTEXTE
Stack : <ADR-001 §5>
Commande de test : <ADR-001 §1>
Commande pour ce seul test : <…>

CE QUE JE TE DONNE
- Le test à faire passer : <chemin>::<nom du test>
- Le cas de test : tests/cases/CASE-<DOM>-nn.md
- La spécification : specs/<domaine>.md, section SPEC-<DOM>-nn
- Les fichiers du domaine que tu peux modifier : src/<…>

TA TÂCHE
Fais passer au vert le test <nom>, et lui seul.

CONTRAINTES
1. Ne modifie aucun fichier sous tests/. Le test est le contrat, pas une
   proposition. Si tu le crois faux, arrête-toi et dis-le-moi.
2. Ne le contourne pas : pas de skip, pas de xfail, pas de tolérance élargie,
   pas d'assertion neutralisée.
3. Implémente la règle, pas la valeur attendue. Les nombres écrits dans le cas
   de test ne doivent apparaître nulle part dans le code de production. Le
   calcul part du pourcentage écrit dans la spécification.
4. La règle vit dans le domaine : <chemin>. Pas dans un contrôleur, pas dans un
   écran, pas dans une requête.
5. N'implémente que ce que ce test exige. Ne traite pas les autres cas, ne
   généralise pas, n'anticipe aucune évolution. Les autres tests rouges restent
   rouges.
6. Ne modifie aucun fichier en dehors de : <liste explicite>.
7. Aucune dépendance nouvelle.

RENDS
- Le diff.
- La liste des fichiers créés ou modifiés.
- La commande exacte pour lancer ce seul test, et son résultat.
- Où vit la règle, en une ligne : fichier et fonction.
- Ce que tu as dû supposer et qui n'était ni dans la spécification ni dans le
  test.
```

---

## Comment le remplir

| Champ | Où le trouver |
|---|---|
| Stack | `docs/adr/ADR-001-stack.md` §5 |
| Commande de test | `ADR-001` §1, contrainte « runner exécutable en une commande » |
| Test à faire passer | le nom écrit dans la section « Test automatisé » du fichier `CASE` |
| Cas de test | `tests/cases/CASE-<DOM>-nn.md` |
| Spécification | `specs/<domaine>.md`, la section que le `CASE` cite |
| Fichiers modifiables | colonne *ce que l'agent reçoit* de votre plan de délégation |
| Liste de la contrainte 6 | colonne *ce qu'il ne touche pas* de votre plan de délégation |

Les deux dernières lignes ne s'improvisent pas au moment de lancer l'agent : elles
sont déjà écrites dans `docs/delegation-<SPEC>.md`. Si elles y sont vides, la tâche
n'est pas prête.

D'une tâche à la suivante, *ce que l'agent reçoit* grossit — la tâche 3 reçoit ce
que la tâche 2 a produit. C'est normal. Ce qui ne doit pas grossir, c'est *ce qu'il
ne touche pas*.

---

## Ce qu'on ne met pas dedans

- « Fais passer tous les tests. »
- Le dépôt entier en contexte.
- « Améliore ce que tu peux au passage. »
- « Ajoute la gestion d'erreur qui te semble utile. »
- « Prépare le code à évoluer. »

Chacune produit un diff que personne ne relit. Le troisième signal de reprise en
main est : *on ne comprend plus ce que l'agent produit*.

---

## Avant de commiter

- [ ] `git diff --stat tests/` est vide : l'agent n'a pas touché aux tests.
- [ ] La suite entière tourne. Le test visé est vert, et aucun test qui était vert
      n'est passé au rouge.
- [ ] Cassez la règle dans le code, lancez le test, vérifiez qu'il rougit, remettez
      la règle. C'est la case correspondante du fichier `CASE`.
- [ ] Quelqu'un dans l'équipe sait expliquer chaque partie du diff.
- [ ] Le message de commit porte l'identifiant `SPEC`.
- [ ] Le trailer `Generated-by:` est présent, le contenu étant généré.
- [ ] La ligne « Après » du plan de délégation est renseignée le soir même.

---

## Pourquoi ces contraintes

Trois d'entre elles portent le reste.

**1 et 2 — ne pas toucher au test, ne pas le contourner.** C'est le deuxième des
trois signaux de reprise en main. Un test modifié pour passer est un échec, et il
se voit dans `git log`.

**3 — implémenter la règle, pas la valeur attendue.** Sans elle, l'agent écrit
`return 130` et le test est vert. La case « le test échoue si la règle est
supprimée du code » devient alors incochable, et la vérification en séance de J10 —
casser une règle, lancer la suite — le montre.

**5 — n'implémenter que ce que ce test exige.** Un agent laissé libre fait passer
quatre tests d'un coup : le plan de délégation perd sa granularité et vous ne savez
plus quelle tâche a produit quoi.
