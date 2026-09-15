<?php
namespace ElementPack\Modules\SocialShare;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {


	private static $medias = [
		'baidu' => [
			'title' => 'Baidu',
		],
		'blogger' => [
			'title' => 'Blogger',
		],
		'buffer' => [
			'title'       => 'Buffer',
			'has_counter' => true,
		],
		'delicious' => [
			'title' => 'Delicious',
		],
		'digg' => [
			'title' => 'Digg',
		],
		'evernote' => [
			'title' => 'Evernote',
		],
		'facebook' => [
			'title'       => 'Facebook',
			'has_counter' => true,
		],
		'flipboard' => [
			'title' => 'Flipboard',
		],
		'instapaper' => [
			'title' => 'Instapaper',
		],
		'linkedin' => [
			'title'       => 'Linkedin',
			'has_counter' => true,
		],
		'liveinternet' => [
			'title' => 'LiveInternet',
		],
		'livejournal' => [
			'title' => 'LiveJournal',
		],
		'mix' => [
			'title' => 'Mix',
		],
		'moimir' => [
			'title'       => 'Mail.Ru',
			'has_counter' => true,
		],
		'meneame' => [
			'title' => 'meneame',
		],
		'odnoklassniki' => [
			'title'       => 'OK',
			'has_counter' => true,
		],
		'pocket' => [
			'title'       => 'Pocket',
			'has_counter' => true,
		],
		'pinterest' => [
			'title'       => 'Pinterest',
			'has_counter' => true,
		],
		'reddit' => [
			'title' => 'Reddit',
		],
		'renren' => [
			'title' => 'Renren',
		],
		'tumblr' => [
			'title'       => 'Tumblr',
			'has_counter' => true,
		],
		'surfingbird' => [
			'title' => 'Surfingbird',
		],
		'twitter' => [
			'title' => 'X',
		],
		'vkontakte' => [
			'title'       => 'Vkontakte',
			'has_counter' => true,
		],
		'weibo' => [
			'title' => 'Weibo',
		],
		'wordpress' => [
			'title' => 'Wordpress',
		],
		'xing' => [
			'title' => 'Xing',
		],
		// Mobile Device Sharing
		'line' => [
			'title' => 'LINE',
		],
		'skype' => [
			'title' => 'Skype',
		],
		'telegram' => [
			'title' => 'Telegram',
		],
		'viber' => [
			'title' => 'Viber',
		],
		'wechat' => [
			'title' => 'WeChat',
		],
		'whatsapp' => [
			'title' => 'WhatsApp',
		],
		'link' => [
			'title'       => 'Copy Link',
		],
	];

	/**
	 * Whether a name is one of the registered social media keys.
	 *
	 * This is the single allow-list every caller must validate against before a
	 * submitted value is used to build markup.
	 *
	 * @param mixed $media_name Value submitted through the repeater control.
	 * @return bool
	 */
	public static function is_social_media( $media_name ) {
		return is_string( $media_name ) && isset( self::$medias[ $media_name ] );
	}

	/**
	 * Look a social media entry up, or return the whole map when called with no
	 * argument.
	 *
	 * The lookup is keyed on an explicit null check rather than on truthiness:
	 * a falsy-but-present key ('' or '0', both reachable from a repeater row
	 * saved with an empty Social Media select) used to fall through to the
	 * "return everything" branch, so callers testing `null === get_social_media( $x )`
	 * as an allow-list saw the full array and treated the value as valid.
	 *
	 * @param string|null $media_name Registered key, or null for the full map.
	 * @return array|null Entry, the full map, or null when the key is unknown.
	 */
	public static function get_social_media( $media_name = null ) {
		if ( null === $media_name ) {
			return self::$medias;
		}

		return self::is_social_media( $media_name ) ? self::$medias[ $media_name ] : null;
	}

	public function get_name() {
		return 'social';
	}

	public function get_widgets() {

		$widgets = [
			'Social_Share',
		];

		return $widgets;
	}
}
