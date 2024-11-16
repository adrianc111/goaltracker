#!/bin/bash

# Function to print a separator
separator() {
  echo "--------------------------------------------"
}

# Backup function
backup_db() {
  separator
  echo "Starting MySQL backup process..."

  # Start the proxy (adjust the port numbers and proxy command as needed)
  echo "Opening proxy..."
  fly proxy 13306:3306 -a oneway-db &
  PROXY_PID=$!
  sleep 5  # Wait for proxy to establish

  # Generate a timestamp for the backup file
  TIMESTAMP=$(date +"%Y_%m_%d_%H_%M_%S")
  BACKUP_FILE="storage/backups/$TIMESTAMP.sql"

  # Perform the mysqldump
  echo "Creating backup..."
  mysqldump --no-tablespaces -h 127.0.0.1 -P 13306 -u oneway -p oneway-db > "$BACKUP_FILE"
  if [ $? -eq 0 ]; then
    echo "Backup completed successfully."
  else
    echo "Backup failed."
  fi

  # Stop the proxy
  echo "Closing proxy..."
  kill $PROXY_PID

  separator
}

# Automatically run the backup function
backup_db

# to import locally: mysql -u laravel -p'laravel' -h 127.0.0.1 -P 3316 oneway < mysql_backup_20240906_142038.sql