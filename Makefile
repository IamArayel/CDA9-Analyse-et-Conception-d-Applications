# Ti Baleine - raccourcis de mise en route et de vérification.
#
# `make presentation` est la seule cible à connaître le matin du rendu : elle
# démarre la base, attend qu'elle réponde, vérifie qu'elle est bien sur 3306,
# crée et migre les deux schémas si la machine est vierge, puis passe les trois
# contrôles. Elle est **idempotente** : la rejouer sur une machine déjà prête ne
# fait rien de plus que revérifier.
#
# Compatible GNU Make 3.81, celui livré avec macOS : ni .ONESHELL, ni les
# fonctions apparues en 4.x.

SHELL := /bin/bash

CONSOLE  := php bin/console
COMPOSE  := docker compose
PHPUNIT  := vendor/bin/phpunit
ATTENTE  := 60

# Attendus, à tenir à jour avec docs/README_J10.md §3.
TESTS_ATTENDUS    := 87
RUPTURES_ATTENDUES := 5

.DEFAULT_GOAL := aide

.PHONY: aide presentation demarrer attendre port bases verifier test tracabilite schema demo arreter

aide:
	@echo ""
	@echo "  Ti Baleine - cibles disponibles"
	@echo ""
	@echo "  make presentation   tout : base, schémas, et les trois contrôles"
	@echo "  make demo           rejoue « réserver, verser l'acompte, solder »"
	@echo ""
	@echo "  make demarrer       démarre la base et attend qu'elle réponde"
	@echo "  make bases          crée et migre ti_baleine et ti_baleine_test"
	@echo "  make verifier       les trois contrôles, sans toucher à la base"
	@echo "  make test           les $(TESTS_ATTENDUS) tests"
	@echo "  make tracabilite    régénère docs/traceability.md"
	@echo "  make schema         le mapping colle-t-il au MLD"
	@echo "  make arreter        arrête la base"
	@echo ""

# ---------------------------------------------------------------------------
# La cible du matin du rendu
# ---------------------------------------------------------------------------

presentation: demarrer bases verifier
	@echo ""
	@echo "  ✓ Prêt. La démonstration se lance avec : make demo"
	@echo "    La procédure complète est dans README_J10.md"
	@echo ""

# ---------------------------------------------------------------------------
# La base
# ---------------------------------------------------------------------------

demarrer: attendre port

attendre:
	@echo ""
	@echo "→ Démarrage de la base"
	@$(COMPOSE) up -d
	@printf "  attente"
	@for i in $$(seq 1 $(ATTENTE)); do \
		if $(COMPOSE) ps --format '{{.Status}}' database 2>/dev/null | grep -q healthy; then \
			echo " ok"; exit 0; \
		fi; \
		printf "."; sleep 1; \
	done; \
	echo ""; \
	echo "  ERREUR : la base n'a pas répondu en $(ATTENTE) s."; \
	echo "  Voir ce qu'elle dit : $(COMPOSE) logs database"; \
	exit 1

# Le port n'est vérifié qu'ici, et c'est volontaire : jusqu'au 2026-08-20 il
# était tiré au hasard à chaque démarrage, et le projet visait en réalité un
# autre MySQL sans que rien ne le dise.
port:
	@port=$$($(COMPOSE) port database 3306 2>/dev/null); \
	if [ -z "$$port" ]; then \
		echo "  ERREUR : le conteneur ne publie aucun port pour la base."; \
		exit 1; \
	fi; \
	case "$$port" in \
		*:3306) echo "  base publiée sur $$port" ;; \
		*) echo "  ERREUR : la base est publiée sur $$port et non sur 3306."; \
		   echo "  compose.override.yaml doit porter \"3306:3306\"."; \
		   exit 1 ;; \
	esac

# Idempotent des deux côtés : --if-not-exists ne recrée rien, et une migration
# déjà passée ne se rejoue pas.
bases:
	@echo ""
	@echo "→ Schémas de développement et de test"
	@$(CONSOLE) doctrine:database:create --if-not-exists
	@$(CONSOLE) doctrine:database:create --env=test --if-not-exists
	@$(CONSOLE) doctrine:migrations:migrate --no-interaction
	@$(CONSOLE) doctrine:migrations:migrate --env=test --no-interaction
	@echo ""
	@echo "→ Confirmation de la base réellement interrogée"
	@$(CONSOLE) --env=test dbal:run-sql "SELECT DATABASE() AS base, @@port AS port"

# ---------------------------------------------------------------------------
# Les contrôles
# ---------------------------------------------------------------------------

verifier: test tracabilite schema

test:
	@echo ""
	@echo "→ Tests (attendu : $(TESTS_ATTENDUS) verts)"
	@$(PHPUNIT)

tracabilite:
	@echo ""
	@echo "→ Traçabilité (attendu : $(RUPTURES_ATTENDUES) ruptures, pas une de plus)"
	@./tools/traceability.sh

schema:
	@echo ""
	@echo "→ Mapping Doctrine contre le MLD (attendu : deux [OK])"
	@$(CONSOLE) doctrine:schema:validate

# ---------------------------------------------------------------------------
# La démonstration
# ---------------------------------------------------------------------------

demo:
	@$(CONSOLE) --env=demo ti-baleine:demontrer-le-parcours

arreter:
	@$(COMPOSE) down
