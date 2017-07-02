#!/bin/bash

### Load configuration 
source ~/Scripts/config.sh

process_install () {
if [ $# -gt 0 ]
then

	echo "Processing $# installs"
	for (( i = 1; i <= $#; i++ ))
	do

		var="$i"
		website=${!var}

		### Load FTP credentials 
		source $path_scripts/logins.sh

		### Credentials found, start the backup
		if ! [ -z "$domain" ]
		then

			if [ "$homedir" == "" ]
			then
			   	homedir="/"
			fi

			if [[ "$OSTYPE" == "linux-gnu" ]]; then
			    ### Begin folder size in bytes without apparent-size flag
                folder_size=`du -s --block-size=1 $path/$domain/`
                folder_size=`echo $folder_size | cut -d' ' -f 1`

			elif [[ "$OSTYPE" == "darwin"* ]]; then
		        ### Calculate folder size in bytes http://superuser.com/questions/22460/how-do-i-get-the-size-of-a-linux-or-mac-os-x-directory-from-the-command-line
		        folder_size=`find $path/$domain/ -type f -print0 | xargs -0 stat -f%z | awk '{b+=$1} END {print b}'`

			fi

			### Views for yearly stats
			views=`php $path_scripts/Get/stats.php domain=$domain`

			# Post folder size bytes and yearly views to ACF field
			curl "https://anchor.host/anchor-api/$domain/?storage=$folder_size&views=$views&token=$token"
			
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

	cd ~

fi
}

### See if any specific sites are selected
if [ $# -gt 0 ]
then
	## Run selected installs
	process_install $*
else
	# Run all installs
	process_install ${websites[@]}
fi