#!/usr/bin/env bash

# The PHP version to use
PHP_BINARY=${PHP_BINARY:=/usr/bin/php}

# Dependencies
deps=("$PHP_BINARY" 'composer' 'grep' 'npm' 'sed')

# Default settings
no_db=0

# Initialize variables
artisan_down=0
db_cache=0

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
            (( (x + 1) < ${#missing_deps[@]} )) && printf '%s' ', '
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
    $PHP_BINARY artisan down
}

msg "Running: ${c_m}composer installl --no-dev"
$PHP_BINARY "$(type -P composer)" install --no-interaction --no-dev || error "${c_m}composer install --no-interaction --no-dev$c_w exited with an error status"

while read -r; do
    if [[ "$REPLY" =~ ^APP_KEY=(.*)$ && -z "${BASH_REMATCH[1]}" ]]; then
        msg 'Generating Encryption Key' 'php artisan key:generate'
        $PHP_BINARY artisan key:generate
        break
    elif [[ "$REPLY" =~ CACHE_STORE=([^ ]*) && "${BASH_REMATCH[1]}" = 'database' ]]; then
        db_cache=1
    fi
done < .env

if (( ! no_db )); then
    msg "Running: ${c_m}php artisan migrate --force"
    $PHP_BINARY artisan migrate --force || error "${c_m}php artisan migrate --force$c_w exited with an error status"
fi

if (( ! no_db )) || (( ! db_cache )); then
    msg "Running: ${c_m}php artisan cache:clear"
    $PHP_BINARY artisan cache:clear
fi

msg "Running: ${c_m}php artisan route:clear"
$PHP_BINARY artisan route:clear

msg "Running: ${c_m}php artisan view:clear"
$PHP_BINARY artisan view:clear

[[ -d node_modules ]] && {
    msg "Running: ${c_m}npm prune --production"
    npm prune --production || error "${c_m}npm prune --production$c_w exited with an error status"
}

msg "Running: ${c_m}npm install --production"
npm install --production || error "${c_m}npm install --production$c_w exited with an error status"

msg "Running: ${c_m}gulp --production"
npx gulp --production || error "${c_m}gulp --production$c_w exited with an error status"

if (( artisan_down )); then
    msg "Running: ${c_m}php artisan up"
    $PHP_BINARY artisan up
fi
