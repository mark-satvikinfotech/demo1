<?php

namespace InfusionCrafting;

use Infusionsoft\Http\HttpException;

use GuzzleHttp\Exception\ClientException;

class AdminPage {
  /**
   * @var Infusionsoft\Infusionsoft
   */
  protected $client;

  /**
   * @var array
   */
  protected $request;

  public static function init(Client $client) {
    $admin = new static($client, $_REQUEST);
    add_menu_page(
      __('InfusionSoft Settings', Plugin::TEXT_DOMAIN),
      __('InfusionSoft', Plugin::TEXT_DOMAIN),
      'manage_options',
      'infusioncrafting',
      [$admin, 'render']
    );
  }

  public function __construct(Client $client, array $request) {
    $this->client   = $client;
    $this->request  = $request;
  }

  public function render() {
    if (!current_user_can('manage_options')) {
      wp_die($this->__('Unauthorized!'));
    }

    $data = [];

    if ($this->action()) {
      $this->do_action();
    }

    $data['client_id']     = $this->get_setting('client_id');
    $data['client_secret'] = $this->get_setting('client_secret');

    $authorized = $this->client->authorized();
    $data['authorized'] = $authorized;

    if (!$authorized) {
      $data['auth_url'] = $this->get_auth_url($data);

      $this->error = __(
        'You must authorize this site to complete'
        . ' your InfusionSoft integration!'
      );
    }

    $this->render_view('settings', $data);
  }

  protected function render_view(string $name, array $data = []) {
    include Plugin::view($name);
  }

  protected function update_settings(array $settings) {
    $this->update_setting('client_id', $settings['client_id'] ?? '');
    $this->update_setting('client_secret', $settings['client_secret'] ?? '');

    $this->message = $this->__('Settings updated.');
  }

  protected function get_auth_url(array $params) {
    return $this->client->getAuthorizationUrl();
  }

  protected function request_auth_token(string $code) {
    $token = $this->client->request_token($code);
    if ($token) {
      return $this->update_setting('token', serialize($token));
    }

    return false;
  }

  protected function attr__($text) : string {
    return esc_attr__($text, Plugin::TEXT_DOMAIN);
  }

  protected function __($text) : string {
    return __($text, Plugin::TEXT_DOMAIN);
  }

  protected function get_setting(string $name) {
    return get_option("infusioncrafting_$name");
  }

  protected function update_setting(string $name, string $value) {
    return update_option("infusioncrafting_$name", $value);
  }

  protected function delete_setting(string $name) {
    return delete_option("infusioncrafting_$name");
  }

  protected function confirm_auth() {
    $this->message = $this->__('Authorized successfully!');
  }

  protected function revoke_access_token() {
    $this->delete_setting('token');
    $this->client->revoke_token();
  }

  protected function action() {
    return $this->request['action'] ?? null;
  }

  protected function do_action() {
    switch ($this->action()) {
    case 'confirm_auth':
      $this->confirm_auth();
      break;
    case 'revoke_access_token':
      $this->revoke_access_token();
      break;
    case 'update_infusioncrafting_settings':
    default:
      $this->update_settings($this->request);
      break;
    }
  }
}
