#!/bin/bash

# Define variables
DB_USER="catalog"
DB_PASS="raspberry"
DB_NAME="inventory"
BACKUP_FILE="backup_$(date).sql"

# Dump the database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > SQL-Backups/"$BACKUP_FILE"



# Git operations
cd /home/pi/Garment-Catalog
git add SQL-Backups/"$BACKUP_FILE"
git commit -m "Automated backup $(date)"
git push origin main
