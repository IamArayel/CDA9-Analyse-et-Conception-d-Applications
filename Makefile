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

.PHONY: aide presentation demarrer attendre port identite qui-ecoute bases verifier test tracabilite schema demo arreter

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

# Docker publie-t-il bien 3306 ? Jusqu'au 2026-08-20 le port était tiré au hasard
# à chaque démarrage, et le projet visait en réalité un autre MySQL.
#
# Ce contrôle ne dit pas **qui répond** : c'est la cible `identite` qui le
# prouve, plus bas, et elle le prouve par un fait plutôt que par le nom du
# processus qui écoute. Filtrer sur « docker » excluait OrbStack, Colima et
# tous les autres moteurs : une liste de noms est toujours en retard d'un outil.
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

# **Le serveur qui répond sur 3306 est-il bien notre conteneur ?**
#
# MySQL rend dans @@hostname l'identifiant du conteneur qui l'héberge. Le
# comparer à celui que rend le moteur est une preuve, valable quel que soit le
# moteur : Docker Desktop, OrbStack, Colima ou autre.
#
# Ce contrôle existe parce qu'un MySQL du poste lié à 127.0.0.1:3306 cohabite
# sans conflit avec un conteneur lié à 0.0.0.0:3306 : le moteur démarre,
# annonce son port, et l'autre serveur capte les connexions.
identite:
	@attendu=$$($(COMPOSE) ps -q database 2>/dev/null | cut -c1-12); \
	reel=$$($(CONSOLE) --env=test dbal:run-sql "SELECT @@hostname" 2>/dev/null \
	        | sed -n '4p' | tr -d ' |'); \
	if [ -z "$$reel" ]; then \
		echo "  (identité non vérifiable : la base n'a pas répondu)"; \
	elif [ "$$reel" = "$$attendu" ]; then \
		echo "  le serveur qui répond est bien le conteneur ($$reel)"; \
	else \
		echo ""; \
		echo "  ERREUR : ce n'est pas notre conteneur qui répond sur 3306."; \
		echo "    attendu : $$attendu   obtenu : $$reel"; \
		$(MAKE) --no-print-directory qui-ecoute; \
		exit 1; \
	fi

# Affiché en cas d'échec seulement : la liste brute, sans interprétation.
qui-ecoute:
	@echo ""
	@echo "  Ce qui écoute sur 3306 :"
	@lsof -nP -iTCP:3306 -sTCP:LISTEN 2>/dev/null | sed 's/^/    /' || echo "    (lsof indisponible)"
	@echo ""
	@echo "  Un MySQL installé sur le poste capte probablement les connexions."
	@echo "  Selon l'installation :"
	@echo "    brew services stop mysql"
	@echo "    launchctl disable gui/\$$(id -u)/homebrew.mxcl.mysql"
	@echo "    sudo /usr/local/mysql/support-files/mysql.server stop   # paquet .dmg"
	@echo "  Puis : $(COMPOSE) up -d --force-recreate && make presentation"
	@echo ""

# Idempotent des deux côtés : --if-not-exists ne recrée rien, et une migration
# déjà passée ne se rejoue pas.
bases:
	@echo ""
	@echo "→ Schémas de développement et de test"
	@if ! $(CONSOLE) doctrine:database:create --if-not-exists; then \
		$(MAKE) --no-print-directory qui-ecoute; \
		exit 1; \
	fi
	@$(CONSOLE) doctrine:database:create --env=test --if-not-exists
	@$(MAKE) --no-print-directory identite
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
