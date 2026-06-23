#!/bin/bash

TARGET_DIR="."

if [ ! -d "$TARGET_DIR" ]; then
    echo "Error: Directory '$TARGET_DIR' not found."
    exit 1
fi

echo "🔍 Scanning all Markdown files in '$TARGET_DIR' for multi-line tables..."

# Loop through all .md files recursively
find "$TARGET_DIR" -type f -name "*.md" | while read -r file; do
    echo "Processing: $file"

    # Run the Python table-fixing logic and save to a temporary file
    python3 -c '
import sys

with open(sys.argv[1], "r", encoding="utf-8") as f:
    lines = f.read().splitlines()

inside_table = False
output = []
current_row = []

for line in lines:
    # Detect markdown table line
    if line.strip().startswith("|") and line.strip().endswith("|"):
        cols = [c.strip() for c in line.split("|")[1:-1]]

        # Detect header separator | --- | --- |
        if all(set(c) <= {"-", " ", ":"} for c in cols if c):
            if current_row:
                output.append("| " + " | ".join(current_row) + " |")
                current_row = []
            output.append("| " + " | ".join(cols) + " |")
            inside_table = True
            continue

        # If we are already inside a table structure
        if inside_table:
            # New row starts if the first column has content (like a number)
            if cols[0]:
                if current_row:
                    output.append("| " + " | ".join(current_row) + " |")
                current_row = cols
            else:
                # Append multi-line data to the active row
                for i in range(1, len(cols)):
                    if cols[i]:
                        sep = "<br>" if cols[i].startswith("-") else " "
                        current_row[i] = f"{current_row[i]}{sep}{cols[i]}" if current_row[i] else cols[i]
        else:
            # If it is just a standalone header line before the separator
            current_row = cols
            inside_table = True
    else:
        # If we hit a non-table line, flush out any accumulated row data first
        if inside_table:
            if current_row:
                output.append("| " + " | ".join(current_row) + " |")
                current_row = []
            inside_table = False
        output.append(line)

if current_row:
    output.append("| " + " | ".join(current_row) + " |")

# Write the fixed content back
with open(sys.argv[1], "w", encoding="utf-8") as f:
    f.write("\n".join(output) + "\n")
' "$file"

done

echo "All tables fixed directly in place!"
