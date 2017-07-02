#!/bin/bash

### Loops through backup and compares with folders under ~/Backup/
### Returns list of folders not linked to a backup (orphaned folders)

# Load configuration 
source ~/Scripts/config.sh

# Loop through arguments and seperate regular arguments from flags (--flag)
for var in "$@"
do
	# If starts with "--" then assign it to a flag array
    if [[ $var == --* ]]
    then
    	count=1+${#flags[*]}
    	flags[$count]=$var
    # Else assign to an arguments array
    else 
    	count=1+${#arguments[*]}
    	arguments[$count]=$var
    fi
done

# Loop through flags and assign to varible. A flag "--skip-dropbox" becomes $flag_skip_dropbox
for i in "${!flags[@]}"
do   

	# replace "-" with "_" and remove leading "--"
	flag_name=`echo ${flags[$i]} | tr - _`
	flag_name=`echo $flag_name | cut -c 3-`

	# assigns to $flag_flagname
	declare "flag_$flag_name"=true

done

# List of backup folders
backup_dirs=(`cd ~/Backup/; echo *`)


backup_cleanup () {
if [ $# -gt 0 ]; then

	for website in "$@"
	do

		### Load FTP credentials 
		source $path_scripts/logins.sh

		### Credentials found, start the backup
		if ! [ -z "$domain" ]
		then

			delete=($domain)
			for target in "${delete[@]}"; do
			  for i in "${!backup_dirs[@]}"; do
			    if [[ ${backup_dirs[i]} = "${delete[0]}" ]]; then
			      unset 'backup_dirs[i]'
			    fi
			  done
			done

		fi

		### Clear out variables
		domain=''
		username=''
		password=''
		ipAddress=''
		protocol=''
		port=''
		homedir=''
		remoteserver=''

	done

fi
}

backup_cleanup ${websites[@]}

echo ${backup_dirs[@]}