<?php
/**
* Plugin Name: Accessgpt
* Plugin URI: 
* Description: A simple wordpress plugin that enables chat GPT,The AI Plugin for WordPress is a powerful tool that integrates artificial intelligence capabilities into your website. With this plugin, you can enhance your website's functionality and create a personalized user experience for your visitors.
* Version: 2.1
* Author: Arunbalvicky
* Author URI: https://www.instagram.com/arunbalvicky/
* License: GPL-2.0+
* License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

// Define the shortcode
function connect_chatgpt($atts) {
  // Extract the shortcode attributes
  $atts = shortcode_atts( array(
  'api' => esc_attr(get_option('accessgpt_setting')),
  'prompt' => ''
  ), $atts );

  // Generate the output
  $api_key= $atts['api'];
  $prompt=$atts['prompt'];



  // Set the API endpoint URL
  $url = 'https://api.openai.com/v1/completions';

  // Set the API key

  // Set the request headers
  $headers = [
  'Content-Type: application/json',
  'Authorization: Bearer '.$api_key,
  ];

  // Set the request data
  $data = [
  'prompt' => $prompt,
  'model' => 'text-davinci-002',
  'temperature' => 0.95,
  'max_tokens' => 4000,
  ];

  // Convert the data to JSON format
  $data_json = json_encode($data);

  // Initialize the cURL session
  $ch = curl_init();

  // Set the cURL options
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  // Execute the cURL request
  $response_json = curl_exec($ch);

  // Close the cURL session
  curl_close($ch);

  // Decode the JSON response
  $response = json_decode($response_json);

  // Extract the text response from the API response
  $text_response = $response->choices[0]->text;

  // Return the output
  return $text_response;
}

add_shortcode( 'connect_chatgpt', 'connect_chatgpt' );


function add_custom_menu_item() {
  add_menu_page(
  'ChatGPT', // page title
  'ChatGPT Settings', // menu title
  'manage_options', // capability
  'gpt_menu_setting', // menu slug
  'gpt_menu_setting_page', // function
  'dashicons-admin-site' // icon url
  );
}
add_action( 'admin_menu', 'add_custom_menu_item' );

// Create gpt menu setting page
function gpt_menu_setting_page() {
  ?>
  <div class="wrap">
  <h2>Chat GPT Setting</h2>
  <p>Shortcode [connect_chatgpt api="YOUR CHAT GPT API KEY" prompt="ENTER THE INFO YOU WANT"]</p>
  </div>
  <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
  <?php wp_nonce_field('save_accessgpt_settings'); ?>
  <input type="hidden" name="action" value="save_accessgpt_settings">
  <label for="accessgpt_setting">CHAT GPT API<a href="https://platform.openai.com/account/api-keys" target="_blank" > Get API</a>:</label>
  <input type="text" id="accessgpt_setting" name="accessgpt_setting" value="<?php echo esc_attr(get_option('accessgpt_setting')); ?>" placeholder="ENTER API KEY">
  <?php submit_button(); ?>
</form>
  <style>
  h2,p{
    font-size: 20px;
    color: #000;
  }
  </style>
  <?php
}

add_action('admin_post_save_accessgpt_settings', 'save_accessgpt_settings');

function save_accessgpt_settings() {
  $accessgpt_setting = $_POST['accessgpt_setting'];
  update_option('accessgpt_setting', $accessgpt_setting);
  wp_redirect(admin_url('admin.php?page=gpt_menu_setting'));
  exit;
}














?>
