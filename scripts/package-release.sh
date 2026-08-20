#!/usr/bin/env bash

set -Eeuo pipefail

export LC_ALL=C

usage() {
    printf 'Usage: %s <vX.Y.Z-tag> <output-directory>\n' "${0##*/}" >&2
}

die() {
    printf 'package-release: %s\n' "$*" >&2
    exit 1
}

if [[ $# -ne 2 ]]; then
    usage
    exit 2
fi

ref=$1
output_arg=$2
release_tag=${ref#refs/tags/}

if ! printf '%s\n' "$release_tag" | grep -Eq '^v(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)$'; then
    die "release ref must be an exact semantic-version tag such as v2.10.4"
fi

required_commands=(awk chmod composer cp date dirname find git grep mkdir mktemp mv npm php rm sort tar touch unzip zip)
for required_command in "${required_commands[@]}"; do
    command -v "$required_command" >/dev/null 2>&1 || die "required command not found: $required_command"
done

script_dir=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd -P)
repo_root=$(git -C "$script_dir/.." rev-parse --show-toplevel 2>/dev/null) || die "script must run from a Git checkout"

git -C "$repo_root" show-ref --verify --quiet "refs/tags/$release_tag" || die "tag does not exist: $release_tag"
commit=$(git -C "$repo_root" rev-parse --verify "refs/tags/${release_tag}^{commit}") || die "cannot resolve tag: $release_tag"
commit_epoch=$(git -C "$repo_root" show -s --format=%ct "$commit") || die "cannot read tag commit time"

mkdir -p "$output_arg"
output_dir=$(CDPATH= cd -- "$output_arg" && pwd -P)
archive_name="Puock-V${release_tag#v}.zip"
checksum_name="${archive_name}.sha256"
output_archive="$output_dir/$archive_name"
output_checksum="$output_dir/$checksum_name"

[[ ! -e "$output_archive" && ! -L "$output_archive" ]] || die "output already exists: $output_archive"
[[ ! -e "$output_checksum" && ! -L "$output_checksum" ]] || die "output already exists: $output_checksum"

temp_base=${TMPDIR:-/tmp}
[[ -d "$temp_base" ]] || die "temporary directory base does not exist: $temp_base"
release_tmp=$(mktemp -d "${temp_base%/}/puock-release.XXXXXX")
[[ -n "$release_tmp" && -d "$release_tmp" ]] || die "failed to create temporary directory"

cleanup() {
    if [[ -n "${release_tmp:-}" && -d "$release_tmp" ]]; then
        rm -rf -- "$release_tmp"
    fi
}
trap cleanup EXIT
trap 'exit 130' INT
trap 'exit 143' TERM

source_dir="$release_tmp/source"
stage_root="$release_tmp/stage"
package_dir="$stage_root/puock"
verify_root="$release_tmp/verify"
archive_tmp="$release_tmp/$archive_name"
checksum_tmp="$release_tmp/$checksum_name"

mkdir -p "$source_dir" "$package_dir" "$verify_root"
git -C "$repo_root" archive --format=tar "$commit" | tar -xf - -C "$source_dir"

theme_version=$(awk '
    /^[[:space:]]*Version:[[:space:]]*/ {
        sub(/^[[:space:]]*Version:[[:space:]]*/, "")
        sub(/[[:space:]]*$/, "")
        print
        exit
    }
' "$source_dir/style.css")
[[ -n "$theme_version" ]] || die "style.css does not contain a Version header"
[[ "v$theme_version" == "$release_tag" ]] || die "tag $release_tag does not match style.css version $theme_version"

printf 'Validating Composer metadata...\n'
COMPOSER_HOME="$release_tmp/composer-home" \
COMPOSER_CACHE_DIR="$release_tmp/composer-cache" \
    composer --working-dir="$source_dir" validate --strict --no-check-publish

printf 'Building frontend assets from %s...\n' "$release_tag"
[[ "$source_dir" == "$release_tmp/source" ]] || die "refusing to clean build outputs outside the temporary source"
rm -rf -- "$source_dir/assets/dist/js" "$source_dir/assets/dist/style"
(
    cd "$source_dir"
    npm_config_cache="$release_tmp/npm-cache" npm ci --ignore-scripts --no-audit --no-fund
    npm run build
)

[[ "$source_dir" == "$release_tmp/source" ]] || die "refusing to replace vendor outside the temporary source"
rm -rf -- "$source_dir/vendor"

printf 'Installing fresh production Composer dependencies...\n'
COMPOSER_HOME="$release_tmp/composer-home" \
COMPOSER_CACHE_DIR="$release_tmp/composer-cache" \
    composer --working-dir="$source_dir" install \
        --no-dev \
        --prefer-dist \
        --optimize-autoloader \
        --no-interaction \
        --no-progress \
        --no-scripts \
        --no-plugins

COMPOSER_HOME="$release_tmp/composer-home" \
COMPOSER_CACHE_DIR="$release_tmp/composer-cache" \
    composer --working-dir="$source_dir" check-platform-reqs --no-dev

copy_required() {
    local relative_path source_path target_path
    for relative_path in "$@"; do
        source_path="$source_dir/$relative_path"
        target_path="$package_dir/$relative_path"
        [[ -e "$source_path" || -L "$source_path" ]] || die "required release input is missing: $relative_path"
        mkdir -p "$(dirname -- "$target_path")"
        cp -R "$source_path" "$target_path"
    done
}

copy_required \
    404.php author.php category.php comments.php date.php error.php footer.php \
    fun-custom.php functions.php header.php index.php page.php search.php sidebar.php \
    single.php tag.php timthumb.php style.css screenshot.png LICENSE \
    ad inc pages templates

# This legacy template has no runtime reference and is intentionally not shipped.
rm -f -- "$package_dir/templates/page-company-old.php"

copy_required \
    gutenberg/index.php \
    gutenberg/components/alert/block.json \
    gutenberg/components/alert/index.asset.php \
    gutenberg/components/alert/index.js \
    gutenberg/components/alert/style-index.css \
    assets/dist/js/admin.min.js \
    assets/dist/js/libs.min.js \
    assets/dist/js/page-ai.min.js \
    assets/dist/js/puock.min.js \
    assets/dist/style/libs.min.css \
    assets/dist/style/style.min.css \
    assets/dist/setting/index.css \
    assets/dist/setting/index.js \
    assets/dist/setting/chunks \
    assets/dist/setting/language \
    assets/dist/webfonts \
    assets/fonts \
    assets/img \
    assets/libs/dplayer \
    assets/libs/layer \
    assets/libs/gt4.js \
    assets/libs/html2canvas.min.js \
    assets/libs/jquery.min.js \
    assets/libs/marked.js \
    assets/libs/spark-md5.min.js \
    assets/libs/strawberry-icon.css \
    cache/index.html

setting_entry="$package_dir/assets/dist/setting/index.js"
setting_chunks=$(grep -Eo 'chunks/[0-9A-Za-z._-]+\.js' "$setting_entry" | sort -u || true)
[[ -n "$setting_chunks" ]] || die "setting bundle does not reference any runtime chunks"
while IFS= read -r setting_chunk; do
    [[ -f "$package_dir/assets/dist/setting/$setting_chunk" ]] \
        || die "setting bundle references a missing chunk: $setting_chunk"
done <<<"$setting_chunks"

mkdir -p "$package_dir/languages"
language_count=0
while IFS= read -r -d '' language_file; do
    cp "$language_file" "$package_dir/languages/"
    ((language_count += 1))
done < <(find "$source_dir/languages" -maxdepth 1 -type f -name '*.mo' -print0)
((language_count > 0)) || die "no compiled theme translations were found"

copy_required \
    vendor/autoload.php \
    vendor/composer \
    vendor/orhanerday/open-ai/src \
    vendor/orhanerday/open-ai/LICENSE.md \
    vendor/orhanerday/open-ai/composer.json \
    vendor/psr/http-message/src \
    vendor/psr/http-message/LICENSE \
    vendor/psr/http-message/composer.json \
    vendor/psr/log/LICENSE \
    vendor/psr/log/composer.json \
    vendor/rahul900day/gpt-3-encoder/src \
    vendor/rahul900day/gpt-3-encoder/data \
    vendor/rahul900day/gpt-3-encoder/composer.json \
    vendor/yurunsoft/yurun-http/src \
    vendor/yurunsoft/yurun-http/LICENSE \
    vendor/yurunsoft/yurun-http/composer.json \
    vendor/yurunsoft/yurun-oauth-login/src \
    vendor/yurunsoft/yurun-oauth-login/LICENSE \
    vendor/yurunsoft/yurun-oauth-login/composer.json \
    vendor/zoujingli/ip2region/Ip2Region.php \
    vendor/zoujingli/ip2region/XdbSearcher.php \
    vendor/zoujingli/ip2region/ip2region.xdb \
    vendor/zoujingli/ip2region/LICENSE.md \
    vendor/zoujingli/ip2region/composer.json

if [[ -d "$source_dir/vendor/psr/log/src" ]]; then
    copy_required vendor/psr/log/src
elif [[ -d "$source_dir/vendor/psr/log/Psr" ]]; then
    copy_required vendor/psr/log/Psr
else
    die "PSR Log runtime sources were not found"
fi

psr_log_test_dir="$package_dir/vendor/psr/log/Psr/Log/Test"
if [[ -d "$psr_log_test_dir" ]]; then
    [[ "$psr_log_test_dir" == "$package_dir/vendor/psr/log/Psr/Log/Test" ]] \
        || die "unexpected PSR Log test path: $psr_log_test_dir"
    rm -rf -- "$psr_log_test_dir"
fi

puc_base=vendor/yahnis-elsts/plugin-update-checker
copy_required \
    "$puc_base/plugin-update-checker.php" \
    "$puc_base/Puc" \
    "$puc_base/vendor" \
    "$puc_base/license.txt"

shopt -s nullglob
puc_loader_files=("$source_dir/$puc_base"/load-v*.php)
shopt -u nullglob
((${#puc_loader_files[@]} > 0)) || die "Plugin Update Checker loader was not found"
for puc_loader_file in "${puc_loader_files[@]}"; do
    copy_required "${puc_loader_file#"$source_dir/"}"
done

mkdir -p "$package_dir/$puc_base/languages"
while IFS= read -r -d '' puc_language_file; do
    cp "$puc_language_file" "$package_dir/$puc_base/languages/"
done < <(find "$source_dir/$puc_base/languages" -maxdepth 1 -type f -name '*.mo' -print0)

while IFS= read -r -d '' debug_bar_dir; do
    [[ "$debug_bar_dir" == "$package_dir/$puc_base/Puc/"*/DebugBar ]] || die "unexpected DebugBar path: $debug_bar_dir"
    rm -rf -- "$debug_bar_dir"
done < <(find "$package_dir/$puc_base/Puc" -type d -name DebugBar -print0)

required_release_paths=(
    style.css
    functions.php
    vendor/autoload.php
    vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php
    assets/dist/js/puock.min.js
    assets/dist/style/style.min.css
    assets/dist/setting/index.css
    assets/dist/setting/index.js
    assets/dist/setting/language/en_US.js
    assets/dist/setting/language/zh_CN.js
    assets/dist/webfonts/fa-solid-900.woff2
    assets/libs/jquery.min.js
    assets/fonts/G8321-Bold.ttf
    languages/en_US.mo
    gutenberg/components/alert/block.json
    vendor/zoujingli/ip2region/ip2region.xdb
    cache/index.html
    LICENSE
)
for required_release_path in "${required_release_paths[@]}"; do
    [[ -e "$package_dir/$required_release_path" ]] || die "required packaged file is missing: $required_release_path"
done

if [[ -n "$(find "$package_dir" -type l -print -quit)" ]]; then
    die "release package must not contain symbolic links"
fi

deny_pattern='(^|/)([Tt]ests?|node_modules|update-checker|\.github|\.git|\.vscode|\.idea|\.spec-workflow|scripts)(/|$)|(^|/)assets/(js|style)(/|$)|(^|/)assets/libs/basic(/|$)|(^|/)vendor/orhanerday/open-ai/files(/|$)|(^|/)vendor/zoujingli/ip2region/_test\.php$|(^|/)vendor/yahnis-elsts/plugin-update-checker/(css|js|examples)(/|$)|(^|/)vendor/yahnis-elsts/plugin-update-checker/Puc/[^/]+/DebugBar(/|$)|^puock/(AGENTS\.md|README(_EN)?\.md|USAGE(_EN)?\.md|package(-lock)?\.json|pnpm-lock\.yaml|yarn\.lock|composer\.(json|lock)|gulpfile\.js|\.babelrc)$|(^|/)(\.DS_Store|\.env([^/]*)?)$|(^|/)[^/]*(\.map|\.po|\.pot|\.log|\.test\.php|-dev\.php)$'
package_entries=$(cd "$stage_root" && find puock -print | sort)
denied_entries=$(printf '%s\n' "$package_entries" | grep -E "$deny_pattern" || true)
[[ -z "$denied_entries" ]] || die "denied release entries found:\n$denied_entries"

printf 'Linting packaged PHP files...\n'
while IFS= read -r -d '' php_file; do
    php -n -l "$php_file" >/dev/null || die "PHP lint failed: ${php_file#"$package_dir/"}"
done < <(find "$package_dir" -type f -name '*.php' -print0)

php -n -r '
    require $argv[1];
    $classes = array(
        "Ip2Region",
        "Orhanerday\\OpenAi\\OpenAi",
        "Rahul900day\\Gpt3Encoder\\Encoder",
        "Yurun\\OAuthLogin\\Github\\OAuth2",
        "YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory",
    );
    foreach ($classes as $class) {
        if (!class_exists($class)) {
            fwrite(STDERR, "Missing autoloaded class: " . $class . PHP_EOL);
            exit(1);
        }
    }
    if (!interface_exists("Psr\\Log\\LoggerInterface")) {
        fwrite(STDERR, "Missing autoloaded interface: Psr\\Log\\LoggerInterface" . PHP_EOL);
        exit(1);
    }
' "$package_dir/vendor/autoload.php"

if normalized_time=$(date -u -d "@$commit_epoch" '+%Y%m%d%H%M.%S' 2>/dev/null); then
    :
elif normalized_time=$(date -u -r "$commit_epoch" '+%Y%m%d%H%M.%S' 2>/dev/null); then
    :
else
    die "cannot convert commit timestamp"
fi

find "$package_dir" -type d -exec chmod 0755 {} +
find "$package_dir" -type f -exec chmod 0644 {} +
find "$package_dir" -exec touch -t "$normalized_time" {} +

printf 'Creating deterministic archive %s...\n' "$archive_name"
(
    cd "$stage_root"
    find puock -print | sort | zip -X -q "$archive_tmp" -@
)

unzip -tqq "$archive_tmp"
zip_entries=$(unzip -Z1 "$archive_tmp")
unsafe_entries=$(printf '%s\n' "$zip_entries" | grep -Ev '^puock(/|$)' || true)
[[ -z "$unsafe_entries" ]] || die "archive contains entries outside puock/:\n$unsafe_entries"
traversal_entries=$(printf '%s\n' "$zip_entries" | grep -E '(^|/)\.\.(/|$)' || true)
[[ -z "$traversal_entries" ]] || die "archive contains path traversal entries:\n$traversal_entries"
denied_entries=$(printf '%s\n' "$zip_entries" | grep -E "$deny_pattern" || true)
[[ -z "$denied_entries" ]] || die "archive contains denied entries:\n$denied_entries"

unzip -q "$archive_tmp" -d "$verify_root"
[[ -d "$verify_root/puock" ]] || die "archive does not contain the puock/ root directory"
[[ -z "$(find "$verify_root/puock" -type l -print -quit)" ]] || die "extracted archive contains symbolic links"
for required_release_path in "${required_release_paths[@]}"; do
    [[ -e "$verify_root/puock/$required_release_path" ]] || die "archive is missing required file: $required_release_path"
done

if command -v sha256sum >/dev/null 2>&1; then
    (cd "$release_tmp" && sha256sum "$archive_name" >"$checksum_name")
elif command -v shasum >/dev/null 2>&1; then
    (cd "$release_tmp" && shasum -a 256 "$archive_name" >"$checksum_name")
else
    die "sha256sum or shasum is required"
fi

mv "$archive_tmp" "$output_archive"
mv "$checksum_tmp" "$output_checksum"

printf 'Created %s\n' "$output_archive"
printf 'Created %s\n' "$output_checksum"
