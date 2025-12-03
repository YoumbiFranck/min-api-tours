#!/usr/bin/env bash

###############################################
# Script: export_tree.sh
# Description:
#   Recursively scans an input directory,
#   ignores specific folders and file extensions,
#   and writes a Markdown file showing
#   the full file tree with file contents.
###############################################

# ---------- CONFIGURABLE VARIABLES ---------- #

# Root folder to scan
ROOT_DIR="./config"

# Output markdown file
OUTPUT_FILE="project_context.md"

# Extensions to ignore (space-separated, must include dot)
IGNORED_EXTENSIONS=(".png" ".jpg" ".jpeg" ".gif" ".svg" ".ico")

# Folder names to ignore
IGNORED_FOLDERS=("vendor" "var" "node_modules" "dist")

# --------------------------------------------- #

# Prepare output file
ROOT_NAME=$(basename "$ROOT_DIR")
echo "# Arborescence $ROOT_NAME" > "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

# Build find command with ignored folders
FIND_CMD=(find "$ROOT_DIR" -type f)

for folder in "${IGNORED_FOLDERS[@]}"; do
  FIND_CMD+=( -not -path "*/$folder/*" )
done

# Process all files found
while IFS= read -r file; do

  # Extract extension
  ext=".${file##*.}"

  # Check ignored extensions
  for igext in "${IGNORED_EXTENSIONS[@]}"; do
    if [[ "$ext" == "$igext" ]]; then
      continue 2  # Skip this file
    fi
  done

  {
    echo "## Path: $file"
    echo "\`\`\`${file##*.}"
    cat "$file"
    echo
    echo "\`\`\`"
    echo "---"
    echo
  } >> "$OUTPUT_FILE"

done < <("${FIND_CMD[@]}")

echo "✔ Sortie générée dans le fichier : $OUTPUT_FILE"
