mkdir -p /home/$USER/Releases
RELEASES=$(ls /home/$USER/Releases/ | grep '.zip' | wc -l)
APP_VERSION=$(echo $RELEASES+1 | bc)


echo "Building Release v1.${APP_VERSION}"
zip -r /home/$USER/Releases/CLINABS-FULL-STACK-PHP_V1.${APP_VERSION}.zip ./ > /dev/null
git config --global user.email "$USER@clinabs.com"
git config --global user.name "$USER"
git checkout -b v1.${APP_VERSION}
git add .
git commit -m "Release v1.${APP_VERSION}"
git tag -a v1.${APP_VERSION}-m "Release version 1.${APP_VERSION}"
git push origin v1.${APP_VERSION}
open "https://gitlab.com/clinabs.com/clinabs-full-stack-php/-/merge_requests/new?merge_request%5Bsource_branch%5D=v1.${APP_VERSION}&delete_source_branch=0&description=Release%20v1.${APP_VERSION}&target_branch=master&wants_human_review=0"