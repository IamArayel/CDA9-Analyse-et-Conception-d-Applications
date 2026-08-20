#!/usr/bin/env bash
#
# Génère docs/traceability.md et vérifie la chaîne de traçabilité.
#
#   ./tools/traceability.sh           régénère la matrice
#   ./tools/traceability.sh --check   régénère et sort en erreur s'il y a une rupture
#
# Le fichier produit suit docs/traceability-template.md : six colonnes, une
# section pour les exigences non couvertes, une section pour les trous connus.
# Les deux parties que personne ne peut deviner à notre place, le motif d'une
# exigence non spécifiée et la liste des trous, sont tenues à la main dans
# docs/traceability-trous.md et recopiées ici.
#
# Conventions attendues (voir README §4) :
#   docs/compte-rendu-entretien-nn.md  questions numérotées Qnn
#   docs/cahier-des-charges.md         REQ-nnn en début de ligne de tableau,
#                                      chacune citant CR-nn/Qnn ou « déduit »
#   specs/<domaine>.md                 sections titrées SPEC-<DOM>-nn, citant au moins un REQ
#   tests/cases/CASE-<DOM>-nn.md       un fichier par cas, citant au moins un SPEC
#   tests/**                           le nom du test contient l'ID CASE, et un
#                                      fichier marqué @socle-absent est écrit
#                                      mais pas exécuté : il ne compte pas
#   git log                            le message de commit contient l'ID SPEC
#
# Les identifiants sont reconnus avec « - » ou « _ » comme séparateur, parce que
# la plupart des langages n'acceptent pas le tiret dans un nom de fonction :
# CASE-CANCEL-11 et test_CASE_CANCEL_11_... désignent le même cas.

set -u

cd "$(dirname "$0")/.." || exit 1

EQUIPE="Le Trio"
CDC="docs/cahier-des-charges.md"
NOTES="docs/traceability-trous.md"
OUT="docs/traceability.md"
CHECK=0
[ "${1:-}" = "--check" ] && CHECK=1

RX_REQ='REQ-[0-9][0-9][0-9]'
RX_SPEC='SPEC-[A-Z0-9]+-[0-9][0-9]'
RX_CASE_ANY='CASE[-_][A-Z0-9]+[-_][0-9][0-9]'
RX_SRC='CR-[0-9][0-9]/Q[0-9][0-9]'
RX_DEDUIT='d[ée]duit'
# Une exigence se *définit* sur une ligne de tableau qui commence par son
# identifiant. Partout ailleurs (§11, §14, glossaire), un REQ n'est qu'une
# mention en prose : la contrôler comme une définition produirait des ruptures
# fantômes.
RX_REQ_ROW='^\| *REQ-[0-9][0-9][0-9] *\|'

ruptures=0
nmanuels=0
warn() { printf 'RUPTURE  %s\n' "$1" >&2; ruptures=$((ruptures + 1)); }

# Joint des lignes en une cellule de tableau : « a, b, c », ou « — » si vide.
join_cell() {
  local v
  v=$(paste -sd',' - | sed 's/,/, /g')
  [ -z "$v" ] && v="—"
  printf '%s' "$v"
}

# Fichiers de tests automatisés : tout tests/ sauf les cas de test et les gabarits.
#
# Un scénario Behat marqué @socle-absent est **écrit mais pas exécuté** : il n'a
# aucune implémentation d'étapes, faute de couche HTTP à piloter. Son fichier
# porte pourtant l'identifiant de son cas, ce qui suffirait à le faire passer
# pour automatisé et à éteindre une rupture que rien ne couvre. On l'écarte donc
# ici, et on le compte à part, comme les cas « manuel assumé ».
tous_les_fichiers=$(find tests -type f ! -path 'tests/cases/*' ! -name 'TEMPLATE.md' 2>/dev/null)

test_files=""
nnonexecutes=0
while IFS= read -r f; do
  [ -n "$f" ] || continue
  if grep -q '@socle-absent' "$f" 2>/dev/null; then
    nnonexecutes=$((nnonexecutes + 1))
  else
    test_files="${test_files}${f}
"
  fi
done <<EOF
$tous_les_fichiers
EOF

# --- REQ -> source et priorité ----------------------------------------------
# Lues une fois pour toutes sur les lignes de définition du cahier des charges.
req_meta=""
if [ -f "$CDC" ]; then
  req_meta=$(
    grep -E "$RX_REQ_ROW" "$CDC" 2>/dev/null | awk -F'|' -v rxsrc="$RX_SRC" '
      {
        req = $2; gsub(/[ \t`]/, "", req)
        prio = (NF >= 4) ? $4 : ""
        gsub(/^[ \t]+|[ \t]+$/, "", prio)
        gsub(/\*/, "", prio)
        if (prio !~ /Must|Should|Could|Won/) prio = "—"
        else sub(/ *\(.*/, "", prio)
        src = ""
        if (match($0, rxsrc)) src = substr($0, RSTART, RLENGTH)
        else if (tolower($0) ~ /d[eé]duit/) src = "déduit"
        print req "\t" src "\t" prio
      }'
  )
fi

meta_of() { printf '%s\n' "$req_meta" | awk -F'\t' -v r="$1" -v c="$2" '$1 == r { print $c; exit }'; }

# --- SPEC -> REQ ------------------------------------------------------------
# Un REQ est rattaché au dernier SPEC rencontré au-dessus de lui, dans le même fichier.
pairs_spec_req=$(
  awk '
    FNR == 1 { cur = "" }
    { if (match($0, /SPEC-[A-Z0-9]+-[0-9][0-9]/)) cur = substr($0, RSTART, RLENGTH) }
    { if (cur != "" && match($0, /REQ-[0-9][0-9][0-9]/)) print cur "\t" substr($0, RSTART, RLENGTH) }
  ' specs/*.md 2>/dev/null | sort -u
)

# Noms des tests automatisés portant un identifiant de cas donné.
tests_of_case() {
  local rx
  rx=$(printf '%s' "$1" | sed 's/-/[-_]/g')
  printf '%s\n' "$test_files" | tr '\n' '\0' \
    | xargs -0 grep -hoIE "[A-Za-z0-9_]*${rx}[A-Za-z0-9_]*" 2>/dev/null | sort -u
}

# --- SPEC -> CASE -----------------------------------------------------------
pairs_spec_case=""
for f in tests/cases/CASE-*.md; do
  [ -e "$f" ] || continue
  cid=$(basename "$f" .md)
  # Un cas se rattache aux spécifications de sa ligne « Spécification : »
  # uniquement. Les identifiants cités ailleurs, notamment sous « Ce que ce
  # cas ne vérifie pas », désignent des voisins qu'il ne couvre justement pas.
  refs=$(grep -E '^\*\*Sp' "$f" 2>/dev/null | grep -ohE "$RX_SPEC" | sort -u)
  if [ -z "$refs" ]; then
    warn "$cid ne cite aucune spécification sur sa ligne « Spécification : »"
    continue
  fi
  for sp in $refs; do
    pairs_spec_case="${pairs_spec_case}${sp}	${cid}
"
  done
  # Un cas sans test automatisé se signale une fois, ici, et non une fois par
  # spécification qui le cite. Un cas déclaré « manuel assumé » n'est pas une
  # rupture : c'est une décision motivée dans docs/strategie-de-test.md §4,
  # comptée à part comme les exigences « déduit ».
  if grep -qE '^\*\*Statut :\*\* *manuel assumé' "$f" 2>/dev/null; then
    nmanuels=$((nmanuels + 1))
  elif [ -z "$(tests_of_case "$cid")" ]; then
    warn "$cid n'a aucun test automatisé"
  fi
done

# --- Matrice ----------------------------------------------------------------
specs=$(grep -rhoE "$RX_SPEC" specs 2>/dev/null | sort -u)
[ -z "$specs" ] && warn "aucune spécification trouvée dans specs/"

{
  echo "<!-- Généré par tools/traceability.sh — ne pas éditer à la main."
  echo "     Les sections « Exigences non couvertes » et « Trous connus » sont"
  echo "     alimentées par docs/traceability-trous.md, lui tenu à la main. -->"
  echo
  echo "# Matrice de traçabilité — équipe \`$EQUIPE\`"
  echo
  echo "Reprise au créneau 16h15, avec le journal. C'est le seul endroit où l'état de la"
  echo "chaîne se lit d'un coup d'œil."
  echo
  echo '```text'
  echo "CR-01/Q07 → REQ-012 → SPEC-BOOKING-04 → CASE-BOOKING-17 → test → code → commit"
  echo '```'
  echo
  echo "Une ligne par spécification. **Ce document ne se reconstitue pas la veille du"
  echo "rendu** : \`git log -- $OUT\` montre les jours où il a été tenu."
  echo
  echo "---"
  echo
  echo "## Comment la lire"
  echo
  echo "| Colonne | Ce qu'on y met | Où le trouver |"
  echo "|---|---|---|"
  echo "| SPEC | l'identifiant de la spécification | titre de section dans \`specs/<domaine>.md\` |"
  echo "| REQ | la ou les exigences qu'elle réalise | \`$CDC\` |"
  echo "| Source | l'échange dont l'exigence est issue, ou \`déduit\` | \`docs/compte-rendu-entretien-nn.md\` |"
  echo "| Cas de test | le ou les cas qui la couvrent | \`tests/cases/CASE-*.md\` |"
  echo "| Tests | le nom du test automatisé | \`tests/\` |"
  echo "| Commits | le ou les sha courts | \`git log --grep=<SPEC-ID>\` |"
  echo
  echo "Un maillon qui n'existe pas encore se note \`—\`. Plusieurs valeurs dans une case se"
  echo "séparent par une virgule."
  echo
  echo "**Les six ruptures surveillées** par \`tools/traceability.sh --check\` : une exigence"
  echo "sans source · une source citée qui n'existe pas dans nos comptes rendus · une"
  echo "spécification qu'aucun cas de test ne couvre · un cas de test sans test automatisé ·"
  echo "une exigence que plus aucune spécification ne reprend · un cas de test utilisé dans"
  echo "\`tests/\` mais défini nulle part."
  echo
  echo "---"
  echo
  echo "## Matrice"
  echo
  echo "| SPEC | REQ | Source | Cas de test | Tests | Commits |"
  echo "|---|---|---|---|---|---|"

  for spec in $specs; do
    reqs=$(printf '%s\n' "$pairs_spec_req" | awk -F'\t' -v s="$spec" '$1 == s { print $2 }' | sort -u)
    cases=$(printf '%s\n' "$pairs_spec_case" | awk -F'\t' -v s="$spec" '$1 == s { print $2 }' | sort -u)

    [ -z "$reqs" ] && warn "$spec ne cite aucune exigence"
    [ -z "$cases" ] && warn "$spec n'est couverte par aucun cas de test"

    reqs_cell=$(printf '%s\n' "$reqs" | sed '/^$/d;s/.*/`&`/' | join_cell)

    sources=""
    for r in $reqs; do
      s=$(meta_of "$r" 2)
      [ -z "$s" ] && s="—"
      sources="${sources}${s}
"
    done
    src_cell=$(printf '%s' "$sources" | sed '/^$/d' | sort -u | sed 's/.*/`&`/' | join_cell)

    tests_all=""
    for cid in $cases; do
      tests_all="${tests_all}$(tests_of_case "$cid")
"
    done
    tests_cell=$(printf '%s' "$tests_all" | sed '/^$/d' | sort -u | sed 's/.*/`&`/' | join_cell)

    commits_cell="—"
    if [ -d .git ]; then
      commits_cell=$(git log --format='%h' --grep="$spec" 2>/dev/null | sed 's/.*/`&`/' | join_cell)
    fi

    cases_cell=$(printf '%s\n' "$cases" | sed '/^$/d;s/.*/`&`/' | join_cell)

    printf '| `%s` | %s | %s | %s | %s | %s |\n' \
      "$spec" "$reqs_cell" "$src_cell" "$cases_cell" "$tests_cell" "$commits_cell"
  done

  # --- Exigences non couvertes ----------------------------------------------
  echo
  echo "---"
  echo
  echo "## Exigences non couvertes"
  echo
  echo "Une exigence qu'aucune spécification ne reprend n'apparaît nulle part dans le"
  echo "tableau ci-dessus. C'est la rupture la plus facile à ne pas voir, et elle se"
  echo "crée toute seule quand le client change d'avis."
  echo
  echo "| REQ | Priorité | Pourquoi elle n'est pas encore spécifiée |"
  echo "|---|---|---|"

  nb_non_couvertes=0
  if [ -f "$CDC" ]; then
    for req in $(grep -ohE "$RX_REQ" "$CDC" 2>/dev/null | sort -u); do
      if ! grep -rqE "$req" specs 2>/dev/null; then
        warn "$req n'est couverte par aucune spécification"
        nb_non_couvertes=$((nb_non_couvertes + 1))
        prio=$(meta_of "$req" 3)
        [ -z "$prio" ] && prio="—"
        motif=$(grep -E "^$req *\|" "$NOTES" 2>/dev/null | head -1 | cut -d'|' -f2- | sed 's/^ *//;s/ *$//')
        if [ -z "$motif" ]; then
          motif="**motif à écrire dans \`$NOTES\`**"
          warn "$req est non couverte et sans motif dans $NOTES"
        fi
        printf '| `%s` | %s | %s |\n' "$req" "$prio" "$motif"
      fi
    done
  fi
  [ "$nb_non_couvertes" -eq 0 ] && echo "| — | — | Aucune : toutes les exigences du cahier des charges sont reprises par au moins une spécification. |"

  # --- Trous connus ---------------------------------------------------------
  echo
  echo "---"
  echo
  echo "## Trous connus"
  echo
  echo "Ce que nous savons incomplet, et ce que nous comptons en faire. **Un trou déclaré"
  echo "n'est pas une faute. Un trou qu'on découvre à notre place en est une.**"
  echo
  if [ -f "$NOTES" ]; then
    # Le bloc « trous » du fichier tenu à la main, commentaires HTML retirés
    # et lignes vides de tête supprimées.
    sed -n '/^## trous/,$p' "$NOTES" | sed '1d;/<!--/,/-->/d' | awk 'NF { p = 1 } p'
  else
    warn "$NOTES est absent : la section « Trous connus » ne peut pas être remplie"
    echo "> \`$NOTES\` est absent."
  fi
} > "$OUT"

# --- REQ -> échange consigné ------------------------------------------------
# Chaque exigence cite soit un échange d'entretien (CR-nn/Qnn), soit « déduit ».
# Une règle métier qui ne vient d'aucun échange consigné ne vient de nulle part.
# Contrôlé sur les lignes de définition uniquement, cf. RX_REQ_ROW.
ndeduits=0
if [ -f "$CDC" ]; then
  while IFS= read -r line; do
    req=$(printf '%s\n' "$line" | grep -oE "$RX_REQ" | head -1)
    [ -z "$req" ] && continue
    src=$(printf '%s\n' "$line" | grep -oE "$RX_SRC" | head -1)
    if [ -n "$src" ]; then
      cr=${src%%/*}
      q=${src##*/}
      f="docs/compte-rendu-entretien-${cr#CR-}.md"
      if [ ! -f "$f" ]; then
        warn "$req cite $src, mais $f n'existe pas"
      elif ! grep -q "$q" "$f"; then
        warn "$req cite $src, mais $q est absent de $f"
      fi
    elif printf '%s\n' "$line" | grep -qiE "$RX_DEDUIT"; then
      ndeduits=$((ndeduits + 1))
    else
      warn "$req ne cite aucune source (ni CR-nn/Qnn, ni « déduit »)"
    fi
  done < <(grep -E "$RX_REQ_ROW" "$CDC")
fi

# --- Cas de test référencés dans les tests mais non définis -----------------
used=$(printf '%s\n' "$test_files" | tr '\n' '\0' | xargs -0 grep -hoIE "$RX_CASE_ANY" 2>/dev/null | tr '_' '-' | sort -u)
for cid in $used; do
  [ -f "tests/cases/$cid.md" ] || warn "$cid est utilisé dans les tests mais n'est défini nulle part"
done

# --- Sortie -----------------------------------------------------------------
echo "$OUT régénéré."
if [ "$ndeduits" -gt 0 ]; then
  echo "$ndeduits exigence(s) marquée(s) « déduit » — à justifier, ce n'est pas une rupture."
fi
if [ "$nmanuels" -gt 0 ]; then
  echo "$nmanuels cas de test déclaré(s) « manuel assumé » — à justifier, ce n'est pas une rupture."
fi
if [ "$nnonexecutes" -gt 0 ]; then
  echo "$nnonexecutes scénario(s) marqué(s) @socle-absent — écrits, pas exécutés : ils ne comptent pas comme test et leur rupture reste."
fi
if [ "$ruptures" -gt 0 ]; then
  echo "$ruptures rupture(s) de traçabilité." >&2
  [ "$CHECK" -eq 1 ] && exit 1
fi
exit 0
