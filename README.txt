If using MAMP link php to the usr/bin directory with the following sym link
sudo ln -s /Applications/MAMP/bin/php/php5.3.6/bin/php /usr/bin/php	

To package doctrine-migrations need the following line in php.ini
;phar
phar.readonly=0

.settings, .hg*, .git*, .project

All commits will show up as new conversations in teambox
To link a commit to a task as well enter the task number in brackets

git commit -m "Corrected comments to link github and teambox and will show up with a link to the commit [573791]"

Also you can resolve defects with the tag REMEMBER to change the number to match the number of the task.
git commit -m "Finished readme entries on teambox integration [close-573791]"

