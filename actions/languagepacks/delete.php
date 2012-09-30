<?php
require_once elgg_get_plugins_path() . 'languagepacks/lib/elgg_language_packs/elgg_language_packs.php';

$release = get_version(true);

$filters = array();

$langstring = get_input('locales-selection');
if ( $langstring ) {
	$filters['langs'] = explode('|', $langstring);
} else {
	$filters['langs'] = null;
}
unset($langstring);

$projstring = get_input('plugins-selection');
if ( $projstring ) {
	$filters['projs'] = explode('|', $projstring);
} else {
	$filters['projs'] = null;
}
unset($projstring);

$filters['ignore_en'] = true;
$filters['elgg_release'] = $release;
$filters['needs_manifest'] = true;

$olddir = elgg_get_root_path();

$callback = 'elgglp_delete_languages';

// this adds the plugin version automatically and the Elgg version from $filters['elgg_release']
elgglp_create_languagepack_meta(null, $filters);

switch ( elgglp_recurse_language_pack($olddir, $filters, $callback) ) {
	case ELGGLP_ERR_STRUCTURE:
		register_error(elgg_echo('languagepacks:error:structure'));
		forward(REFERER);
	case ELGGLP_ERR_VERSION:
		register_error(elgg_echo('languagepacks:error:version'));
		forward(REFERER);
    case ELGGLP_OK:
        $ts = time();
        $token = generate_action_token($ts);
        $flush_link = elgg_get_site_url() . "action/admin/site/flush_cache?__elgg_ts=$ts&__elgg_token=$token";
        system_message(sprintf(elgg_echo('languagepacks:delete:success'), $flush_link));
}
