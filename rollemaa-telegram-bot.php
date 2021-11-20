<?php
/**
 * Plugin Name: Telegram bot notifications
 *
 * Plugin URI: https://github.com/ronilaukkarinen/minimalistmadness
 * Description: Notify Telegram group about new blog posts.
 * Version: 1.0.0
 * Author: Roni Laukkarinen
 * Author URI: https://github.com/ronilaukkarinen
 * Requires at least: 5.0
 * Tested up to: 5.8.2
 * License: GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package rollemaa-telegram-bot
 */

/**
 * Notify when post gets published.
 * @param string $postid Post ID
 */
function rollemaa_telegram_send_notification( $postid ) {

  // Unhook
  remove_action( 'post_updated', 'rollemaa_telegram_send_notification' );

  // Remove revisions to prevent double save
  remove_action( 'pre_post_update', 'wp_save_post_revision' );

  // Bail if conditions not met
  if (
    defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ||
    wp_is_post_revision( $post_id ) ||
    get_post_status( $postid ) !== 'publish' ||
    get_the_title( $postid ) === null ||
    get_the_permalink( $postid ) === null ||
    get_post_status( $postid ) === 'draft' ||
    get_post_status( $postid ) === 'auto-draft' ||
    get_post_status( $postid ) === 'private' ||
    get_post_status( $postid ) === 'future' ||
    get_post_status( $postid ) === 'pending' ||
    get_post_status( $postid ) === 'trash'
  ) return;

  // Settings
  $telegram_bot_api_key = getenv( 'TELEGRAM_BOT_API_KEY' );
  $method = 'sendMessage';
  $chat_id = getenv( 'TELEGRAM_CHAT_ID' );
  $text = 'Uusi kirjoitus julkaistu: ' . get_the_title( $postid ) . '. Linkki: ' . get_the_permalink( $postid ) . '';

  // API call
  $url = 'https://api.telegram.org/bot' . $telegram_bot_api_key . '/' . $method . '?chat_id=' . $chat_id . '&text=' . $text;

  // Send a message
  wp_remote_get( $url );

  // Re-hook function
  add_action( 'post_updated', 'rollemaa_telegram_send_notification' );
}
add_action( 'post_updated', 'rollemaa_telegram_send_notification' );
