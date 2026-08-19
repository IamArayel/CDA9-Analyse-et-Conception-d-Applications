CONTEXTE
Stack : <ADR-001 §5>
Commande de test : <ADR-001 §1>
Emplacement des tests : <chemin>
Convention : le nom du test contient l'identifiant CASE.

CE QUE JE TE DONNE
- Le cas de test : tests/cases/CASE-CANCEL-01.md
- La spécification : specs/annulation.md, section SPEC-CANCEL-03
- Les signatures existantes du domaine : src/<...>

TA TÂCHE
Écris UN test automatisé, et un seul, qui traduit CASE-CANCEL-01.

CONTRAINTES
1. Nom du test : exactement celui écrit dans la section « Test automatisé » du
   fichier CASE. Ne le reformule pas.
2. N'écris aucune règle métier. Si une classe ou une méthode nécessaire au test
   n'existe pas, crée sa signature avec un corps vide ou une exception « non
   implémenté ». Rien d'autre.
3. Les valeurs sont celles de la section « Données ». N'en invente aucune.
4. Les assertions correspondent exactement aux lignes « Alors » et « Et » du
   Gherkin : une assertion par ligne, aucune de plus.
5. Ne modifie aucun fichier en dehors de : <liste explicite>. Ne touche à aucun
   test existant.
6. Ne simule pas le calcul qui est l'objet du cas. Tu peux simuler ce qui
   l'entoure — horloge, envoi de message, persistance.
7. Le test doit échouer sur son assertion, pas à l'import ni à la collecte.

RENDS
- Le fichier de test.
- La liste des fichiers créés ou modifiés.
- La commande exacte pour lancer ce seul test.
- Ce que tu as dû supposer et qui n'était pas dans ce que je t'ai donné.