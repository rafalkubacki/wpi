#!/usr/bin/env sh

# Install WordPress.
wp core install \
  --path="/var/www/html" \
  --url="http://localhost:${WORDPRESS_PORT}" \
  --title="${WORDPRESS_TITLE}" \
  --admin_user="${WORDPRESS_ADMIN_USER}" \
  --admin_password="${WORDPRESS_ADMIN_PASSWORD}" \
  --admin_email="${WORDPRESS_ADMIN_EMAIL}"

wp language core install "${WORDPRESS_LANGUAGE}"
wp site switch-language "${WORDPRESS_LANGUAGE}"

# Update WordPress options.
wp option update \
  permalink_structure "${WORDPRESS_PERMALINK_STRUCTURE}" \
  --skip-themes \
  --skip-plugins

wp option update \
  blogdescription "${WORDPRESS_TAGLINE}" \
  --skip-themes \
  --skip-plugins

wp option update \
  timezone_string "${WORDPRESS_TIMEZONE}" \
  --skip-themes \
  --skip-plugins

wp option update \
  date_format "${WORDPRESS_DATEFORMAT}" \
  --skip-themes \
  --skip-plugins

wp option update \
  time_format "${WORDPRESS_HOURFORMAT}" \
  --skip-themes \
  --skip-plugins

# Theme installation
sed -i "s/THEME_NAME/$WORDPRESS_THEME_TO_INSTALL/g" ./wp-content/themes/$WORDPRESS_THEME_TO_INSTALL/style.css
wp theme activate "${WORDPRESS_THEME_TO_INSTALL}"
wp theme delete --all

# Plugin installation
wp plugin uninstall --all

if [[ "$(ls -A ./plugins)" ]];
then
  for plugin in ./plugins/*.zip;
  do
    wp plugin install "$plugin" --activate
  done
fi

wp plugin install ${WORDPRESS_PLUGINS_TO_INSTALL} --activate

# Clear installation
wp post delete $(wp post list --post_type='page' --format=ids) --force --defer-term-counting
wp post delete $(wp post list --post_type='post' --format=ids) --force --defer-term-counting
wp post delete $(wp post list --post_type='wpcf7' --format=ids) --force --defer-term-counting
wp comment delete $(wp comment list --format=ids) --force

# Setup basic data
HOME_ID=$(wp post create --post_type=page --post_title='Home' --post_status=publish --porcelain)
wp menu create "Nav"
wp menu item add-post nav ${HOME_ID}

wp option update show_on_front page
wp option update page_on_front ${HOME_ID}

echo -e "\nREPORT\n"

# List users
echo "== User List =="
wp user list
echo ""

# Show installed plugin
echo "== Theme List =="
wp theme list
echo ""

# Show installed plugin
echo "== Plugin List =="
wp plugin list
echo ""