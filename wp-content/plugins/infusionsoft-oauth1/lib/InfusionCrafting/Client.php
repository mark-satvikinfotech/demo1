<?php

namespace InfusionCrafting;

use Infusionsoft\Http\HttpException;
use Infusionsoft\TokenExpiredException;
use Infusionsoft\Infusionsoft;
use Infusionsoft\Token;

class Client {
  /**
   * How far out we should consider token expiration to be "soon"
   *
   * @var int
   */
  const EXPIRY_WINDOW = 12 * HOUR_IN_SECONDS;

  protected static $instance;

  /**
   * Whether the token was refreshed during this request.
   *
   * @var bool
   */
  protected static $refreshed = false;

  /**
   * The internal InfusionSoft client instance
   *
   * @var Infusionsoft\Infusionsoft
   */
  protected $_client;

  /**
   * Initialize and return a valid InfusionCrafting\Client instance,
   * refreshing the token if necessary
   *
   * @return Client
   */
  public static function get() {
    return apply_filters('infusioncrafting/client', false);
  }

  /**
   * Initialize and return a valid InfusionCrafting\Client instance,
   * refreshing the token if necessary
   *
   * @return Client
   */
  public static function init(array $config) {
    if (!isset(self::$instance)) {
      $internalClient = new Infusionsoft($config);

      if ($tokenData = get_option('infusioncrafting_token')) {
        $token = unserialize($tokenData);

        // check for valid stored token
        if ($token instanceof Token) {
          // we have a valid token, no need for a whole new OAuth cycle
          $internalClient->setToken($token);

          // does the token expire soon?
          $tokenExpiresSoon =
            // soon as in several hours from now...
            ((int) $token->endOfLife < time() + static::EXPIRY_WINDOW) &&
            // ...and not already expired, in which case it's too late :(
            !$token->isExpired();

          if ($tokenExpiresSoon) {
            try {
              $internalClient->refreshAccessToken();
              static::$refreshed = true;
            } catch (TokenExpiredException | HttpException $e) {
              // unset token
              $internalClient->setToken(null);

              error_log(sprintf(
                'Encountered exception refreshing token: %s',
                $e->getMessage()
              ));
            }
          }
        }
        // if we don't find a valid token, the plugin will prompt the user
        // to authorize this application in InfusionSoft
      }

      self::$instance = new static($internalClient);
    }

    return self::$instance;
  }

  /**
   * Constructor
   *
   * @param Infusionsoft\Infusionsoft $client the internal API client instance
   */
  public function __construct(Infusionsoft $client) {
    $this->_client = $client;
  }

  /**
   * Magic getter; delegates to the internal client instance
   *
   * @param string $prop the property to get
   * @return mixed the $prop value
   */
  public function __get($prop) {
    return $this->_client->{$prop};
  }

  /**
   * Magic call; delegates to the internal client instance
   *
   * @see http://php.net/manual/en/language.oop5.magic.php
   */
  public function __call($method, $args) {
    return $this->_client->$method(...$args);
  }

  /**
   * Request the access token, refreshing if necessary
   *
   * @param string $code the one-time OAuth access code
   */
  public function request_token(string $code) {
    if ($this->authorized()) {
      // no further action needed
      return;
    }

    return $this->_client->requestAccessToken($code);
  }

  public function revoke_token() {
    $this->_client->setToken(null);
  }

  /**
   * Whether this Client instance is authorized at InfusionSoft
   *
   * @return bool
   */
  public function authorized() {
    $token = $this->_client->getToken();
    return $token && !$token->isExpired();
  }

  /**
   * Whether the access token was refreshed during this HTTP request.
   *
   * @return bool
   */
  public function refreshed() {
    return static::$refreshed;
  }


  /*
   * API endpoint methods
   */

  /**
   * Fetch a Contact record, with any optional properties/custom fields
   * returned as key-value pairs.
   *
   * @param int $id the Contact ID
   * @param array $props [optional] optional_properties to request, e.g.
   * "custom_fields"
   * @param array $customFieldMap Field ID => Label map, for converting
   * complex custom field arrays into key-value pairs
   * @return array
   */
  public function fetch_contact(
    int $id,
    array $props = [],
    array $customFieldMap = []
  ) {
    try {
      $contact = $this->_client->contacts()->with($props)->find($id);
    } catch (HttpException $e) {
      throw new Exception\NotFoundException($e->getMessage());
    }

    if (isset($contact['custom_fields']) && $customFieldMap) {
      $contact['custom_fields'] = $this->simplify_custom_fields(
        $contact['custom_fields'],
        $customFieldMap
      );
    }

    return $contact;
  }

  /**
   * Get the Contact model defined on the InfusionSoft side, including custom
   * field definitions
   *
   * @return array
   */
  public function fetch_contact_model() {
    return $this->_client->contacts()->model();
  }

  /**
   * Fetch the custom field definitions, as an array of key-value pairs,
   * where keys are field IDs and values are field Labels.
   *
   * For example, if the Contact model contains this custom field schema:
   *
   * [
   *   "custom_fields" => [
   *     [
   *       "content" => "some value",
   *       "id"      => 123
   *     ]
   *   ]
   * ]
   *
   * and the field with ID=123 has the label "Example Field",
   * then this method will return:
   *
   * [
   *   123 => "Example Field"
   * ]
   * @return array
   */
  public function fetch_contact_field_map() {
    $model = $this->fetch_contact_model();

    if (!isset($model['custom_fields'])) {
      throw new \RuntimeException(
        'Contact model does not contain custom field definitions'
      );
    }

    return array_reduce($model['custom_fields'], function(
      array $map,
      array $field
    ) {
      $map[$field['id']] = $field['label'];
      return $map;
    }, []);
  }


  protected function simplify_custom_fields(array $customFields, array $map) {
    return array_reduce($customFields, function(
      array $simplifiedFields,
      array $complexField
    ) use($map) {
      $key = $map[$complexField['id']] ?? '';

      if ($key) {
        $simplifiedFields[$key] = $complexField['content'];
      }

      return $simplifiedFields;
    }, []);
  }
}
