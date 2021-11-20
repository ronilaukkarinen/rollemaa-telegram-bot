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

  // Settings
  $telegram_bot_api_key = getenv( 'TELEGRAM_BOT_API_KEY' );
  $method = 'sendMessage';
  $chat_id = getenv( 'TELEGRAM_CHAT_ID' );
  $text = 'Uusi kirjoitus julkaistu: ' . get_the_title( $postid ) . '. Linkki: ' . get_the_permalink( $postid ) . '';

  // API call
  $url = 'https://api.telegram.org/bot' . $telegram_bot_api_key . '/' . $method . '?chat_id=' . $chat_id . '&text=' . $text;

  // Unhook
  remove_action( 'save_post', 'rollemaa_telegram_send_notification' );

  // Remove revisions to prevent double save
  remove_action( 'pre_post_update', 'wp_save_post_revision' );

  // Get postdata
  $postdata = get_post( $postid );

  // Get time difference
  $time_differ = round( abs( strtotime( $postdata->post_modified ) - strtotime( $postdata->post_date ) ) / 60, 2 );

  if ( 'movie' === get_post_type( $postid ) ) {

    // Bail if conditions not met
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( wp_is_post_revision( $post_id ) ) return;
    if ( wp_is_post_autosave( $post_id ) ) return;
    if ( did_action( 'save_post' ) > 1 ) return;
    if ( get_post_status( $postid ) === 'draft' ) return;
    if ( get_post_status( $postid ) === 'auto-draft' ) return;
    if ( get_post_status( $postid ) === 'private' ) return;
    if ( get_post_status( $postid ) === 'future'  ) return;
    if ( get_post_status( $postid ) === 'pending' ) return;
    if ( get_post_status( $postid ) === 'trash' ) return;

    // Send a message
    if ( $time_differ < 0.10 ) {
      wp_remote_get( $url );
    }
  }

  // Bail if other conditions not met
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
  if ( empty( get_the_title( $postid ) ) ) return;
  if ( empty( get_the_content( $postid ) ) ) return;
  if ( empty( get_the_permalink( $postid ) ) ) return;
  if ( wp_is_post_revision( $post_id ) ) return;
  if ( wp_is_post_autosave( $post_id ) ) return;
  if ( did_action( 'save_post' ) > 1 ) return;
  if ( get_post_status( $postid ) === 'draft' ) return;
  if ( get_post_status( $postid ) === 'auto-draft' ) return;
  if ( get_post_status( $postid ) === 'private' ) return;
  if ( get_post_status( $postid ) === 'future'  ) return;
  if ( get_post_status( $postid ) === 'pending' ) return;
  if ( get_post_status( $postid ) === 'trash' ) return;

  // Send a message
  if ( $time_differ < 0.10 ) {
    wp_remote_get( $url );
  }

  // Re-hook function
  add_action( 'save_post', 'rollemaa_telegram_send_notification' );

}
add_action( 'save_post', 'rollemaa_telegram_send_notification' );
