<?php

/** 
 * NHLS API
 * v0.1
 *
 * This API class will work for PHP 5+. Requires CURL. Is not particularily
 * smart yet -- no exception handling. 
**/

class NHLS_Api {

  private $api_key;
  private $environment;

  public function __construct($api_key, $test = false) {
    $this->api_key = $api_key;
    $this->environment = $test ? 'development' : 'production';
  }

  public function getListings($active = false, $includes = '') {
    $ch = curl_init();

    $active_str = $active ? 'true' : 'false';

    curl_setopt($ch, CURLOPT_URL, $this->endpoint() . "listings?api_key={$this->api_key}&active={$active_str}&include={$includes}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if ($response && array_key_exists('listings', $response)) {
      return $response['listings'];
    } else {
      return $response;
    }
  }

  public function getListing($id) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->endpoint() . "listings/{$id}?api_key={$this->api_key}&include=custom_identifier");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if ($response && array_key_exists('listing', $response)) {
      return $response['listing'];
    } else {
      return $response;
    }
  }

  /**
   * Finds a single listing based by searching any attribute. Based on exact
   * match right now.
   */
  public function find($key, $value) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->endpoint() . "listings/find?api_key={$this->api_key}&key={$key}&value={$value}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if ($response && array_key_exists('listing', $response)) {
      return $response['listing'];
    } else {
      return $response;
    }
  }

  public function createListing($params, $active = true) {
    $ch = curl_init();
    $active_value = $active ? 'true' : 'false'; 

    curl_setopt($ch, CURLOPT_URL, $this->endpoint() . "listings?api_key={$this->api_key}&active={$active_value}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    curl_setopt($ch, CURLOPT_POST, TRUE);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('listing' => $params)));

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json; charset=utf-8"
      ));

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (isset($response['listing'])) {
      return $response['listing'];
    } else {
      return $response;
    }
  }

  public function updateListing($id, $params, $include = '') {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->endpoint() . "listings/{$id}?api_key={$this->api_key}&include={$include}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

    $post_fields = json_encode(array('listing' => $params));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      "Content-Type: application/json; charset=utf-8"
    ));

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (isset($response['listing'])) {
      return $response['listing'];
    } else {
      return $response;
    }
  }

  public function deleteListing($id) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->endpoint() . "listings/{$id}?api_key={$this->api_key}&include=custom_identifier");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

    $response = curl_exec($ch);

    curl_close($ch);

    return $response;
  }

  public function deleteListingByKey($key, $value) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->endpoint() . "listings/delete_by_key?api_key={$this->api_key}&key={$key}&value=${value}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
  }

  /** 
   * Updates listing using a custom key to find the listing.
   *
   * @param $key    String key to use to look up, ex: 'custom_identifier'
   * @param $value  String value of key to use, ex: 'my-custom-id-1'
   * @param $params String params to update. 
   *
   * @return mixed Returns the listing if successful, false if failure
   *
   */ 
  public function updateListingByKey($key, $value, $params) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->endpoint() . "listings/update_by_key?api_key={$this->api_key}&key={$key}&value={$value}&include=custom_identifier");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      "Content-Type: application/json; charset=utf-8"
    ));

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (isset($response['listing'])) {
      return $response['listing'];
    } else {
      return $response;
    }
  }

  private function endpoint() {
    return "http://localhost:3000/api/v1/";
    //return $this->environment == 'production' ? 'https://newhomelistingservice.com/api/v1/' : 'https://demo.newhomelistingservice.com/api/v1/';
  }

}
