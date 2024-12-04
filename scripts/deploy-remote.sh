clear
echo "Deploying..."
cd /home/adriano/workspaces/clinabs-full-stack-php

export FTP_DEPLOY_SERVER=68.183.159.246
export FTP_DEPLOY_USER=clinabs_admin
export FTP_DEPLOY_PWD=LTjNBs8jKRngXmvR

lftp -u $FTP_DEPLOY_USER,$FTP_DEPLOY_PWD -e "set ftp:ssl-allow no; put .deploy -o public_html/.htaccess; put maintenance.html -o public_html/maintenance.html; quit"  ftp://$FTP_DEPLOY_SERVER

lftp -u $FTP_DEPLOY_USER,$FTP_DEPLOY_PWD -e "set ftp:ssl-allow no; mirror --reverse --only-newer --verbose --exclude-glob scripts/ --exclude-glob data/ --exclude-glob tmp/ --exclude-glob .vscode/ --exclude-glob .gitignore --exclude-glob config.inc.php --exclude-glob .gitlab-ci.yml --exclude-glob .htaccess --exclude-glob maintenance.html --exclude-glob errors.log ./ public_html/; quit" ftp://$FTP_DEPLOY_SERVER

lftp -u $FTP_DEPLOY_USER,$FTP_DEPLOY_PWD -e "set ftp:ssl-allow no; rm public_html/maintenance.html; put .rewrite -o public_html/.htaccess; quit" ftp://$FTP_DEPLOY_SERVER