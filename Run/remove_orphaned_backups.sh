#!/bin/bash

##
##      Locate and remove orphaned backup folders
##
##      Pass arguments from command line like this
##      Script/Run/remove-orphaned-backup.sh
##

# Load configuration
source ~/Scripts/config.sh

# Collect list of backup
websites=(`php $path_scripts/Get/domains.php`)

# Collect list of backup folders
backups=(`ls $path/`)

backup_count=${#backups[*]}
website_count=${#websites[*]}

echo "$backup_count websites found on server"
echo "$website_count websites found in daily backup script"

(( to_remove = backup_count - website_count ))

echo "Removing $to_remove"

# Check array for match
containsElement () {
  local e
  for e in "${@:2}"; do [[ "$e" == "$1" ]] && return 0; done
  return 1
}

count=0

# Loop through backups and check if found in websites
for (( i=0; i<${backup_count}; i++ ));
do

  website=${websites[@]}

  ### Load FTP credentials
  source $path_scripts/logins.sh

  containsElement "${backups[$i]}" "${websites[@]}"
  if [ $? == 1 ]
  then
    (( count=$count + 1 ))
    echo "Removing " ${backups[$i]}
    mv $path/${backups[$i]} $path_tmp/_orphaned/${backups[$i]}
  fi
done

echo $count " Removed"