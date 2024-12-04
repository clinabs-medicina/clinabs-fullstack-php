mkdir -p /var/www/backups/clinabs
clear
echo "Backupping..."
cd /var/www/homolog
zip -r "/var/www/backups/clinabs/${USER}.data.zip" ./data > /dev/null
clear
echo "Deploying..."
rm -rf /var/www/homolog
cp -r /home/adriano/workspaces/clinabs-full-stack-php /var/www/homolog
cd /var/www/homolog
clear
echo "Restoring backup..."
unzip /var/www/backups/clinabs/${USER}.data.zip -d /var/www/homolog > /dev/null
rm /var/www/backups/clinabs/${USER}.data.zip
mkdir -p /var/www/homolog/tmp
chmod 777 -R /var/www/homolog
clear
echo "Done!"