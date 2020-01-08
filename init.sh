#!/usr/bin/env bash

# Dependencies
deps=('bower' 'composer' 'egrep' 'npm' 'php' 'sed')

# Default settings
no_db=0

# Initialize variables
artisan_down=0

# Colour scheme
[[ -t 1 ]] && {
    c_d=$'\e[1;30m' # DARK GREY
    c_w=$'\e[1;37m' # WHITE
    c_b=$'\e[1;34m' # BLUE
    c_g=$'\e[1;32m' # GREEN
    c_m=$'\e[1;35m' # MAGENTA
    c_r=$'\e[1;31m' # RED
    c_t=$'\e[1;36m' # TEAL
    c_y=$'\e[1;33m' # YELLOW
    c_c=$'\e[0m'    # CLEAR
}

# Display a formatted message
function msg {
    printf '%s %s\n' "$c_b==>" "$c_w$1$c_c"
}

function error {
    printf '%s\n' "${c_r}ERROR${c_w}: $1$c_c" >&2
    exit 1
}

# Check for missing dependencies
declare -a missing_deps=()

for dep in "${deps[@]}"; do
    type -P "$dep" >/dev/null \
        || missing_deps=( "${missing_deps[@]}" "$dep" )
done

[[ -n "${missing_deps[*]}" ]] && {
    error "${c_w}missing dependencies ($(
        for (( x=0; x < ${#missing_deps[@]}; x++ )); do
            printf '%s' "$c_m${missing_deps[$x]}$c_c"
            (( (( x + 1 )) < ${#missing_deps[@]} )) && printf '%s' ', '
        done
    )$c_w)"
}

# Exit with an error if the .env file does not exist
[[ -f '.env' ]] || error 'The .env file does not exist'

# Exit with an error on ctrl-c
trap 'error "script killed"' SIGINT SIGQUIT

# Check for the --no-db argument and set a flag that prevents database operations if present
[[ -n "$1" && "$1" = '--no-db' ]] && no_db=1

[[ -d vendor ]] && {
    artisan_down=1
    msg "Running: ${c_m}php artisan down"
    php artisan down
}

msg "Running: ${c_m}composer installl --no-dev"
composer install --no-interaction --no-dev || error "${c_m}composer install --no-interaction --no-dev$c_w exited with an error status"

while read -r; do
    [[ "$REPLY" =~ ^APP_KEY=(.*)$ && -z "${BASH_REMATCH[1]}" ]] && {
        msg 'Generating Encryption Key' 'php artisan key:generate'
        php artisan key:generate
        break
    }
done < .env

msg "Running: ${c_m}php artisan route:clear"
php artisan route:clear

msg "Running: ${c_m}php artisan view:clear"
php artisan view:clear

grep -qe '^CACHE_BUST=' .env || {
    msg "Adding the ${c_y}CACHE_BUST$c_w variable"
    printf '\n%s\n' 'CACHE_BUST=' >> .env
}

msg "Updating ${c_y}CACHE_BUST$c_w variable"
sed -i 's|^CACHE_BUST=.*|CACHE_BUST='"$(LC_CTYPE=C LANG=C tr -dc A-Za-z0-9 </dev/urandom | head -c 32)"'|' .env

(( ! no_db )) && {
    msg "Running: ${c_m}php artisan migrate --force"
    php artisan migrate --force || error "${c_m}php artisan migrate --force$c_w exited with an error status"
}

[[ -d node_modules ]] && {
    msg "Running: ${c_m}npm prune --production"
    npm prune --production || error "${c_m}npm prune --production$c_w exited with an error status"
}

msg "Running: ${c_m}npm install --production"
npm install --production || error "${c_m}npm install --production$c_w exited with an error status"

msg "Running: ${c_m}bower prune && bower install"
bower prune && bower install || error "${c_m}bower prune && bower install$c_w exited with an error status"

msg "Running: ${c_m}gulp --production"
"$(npm bin)/gulp" --production || error "${c_m}gulp --production$c_w exited with an error status"

(( artisan_down )) && {
    msg "Running: ${c_m}php artisan up"
    php artisan up
}
