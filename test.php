<?php

require 'api.php';

/* test script for api lib */

class NHLS_Api_Test {
  private $test_api_key = '123456';
  private $api;
  private $listing_test_id = 642142; // this will need to be updated to match actual
  private $last_custom_identifier;

  public function process() {
    echo '--- Testing getListings()' . "\n";
    $this->api = new NHLS_Api($this->test_api_key);

    $this->test_get_listings();
    //$this->test_get_listing();
    //$this->test_create_listing();
    //$this->test_update_listing_by_key();
    //$this->test_update_listing();
    //$this->test_delete_listing();
    //$this->test_delete_listing_by_key();
  }

  private function test_get_listings() {
    $listings = $this->api->getListings(false, 'custom_identifier');
    if (count($listings)) {
      echo "Found " . count($listings) . " listings." . "\n";
      $ids = array_map(function($listing) {
        return $listing['id'];
      }, $listings);

      echo "IDs: " . implode(',', $ids) . "\n";
    } else {
      var_dump($listings);
    } 
  }

  private function test_get_listing() {
    echo '--- Testing getListing()' . "\n";
    $id = $this->listing_test_id;

    $listing = $this->api->getListing($id);

    if (isset($listing['title'])) {
      echo "Found listing {$listing['title']}" . "\n";
    }
  }

  private function test_create_listing() {
    echo "--- Testing createListing()" . "\n";

    $this->last_custom_identifier = 'x_' . rand(10000, 99999);

    $listing = array(
      'title' => 'My Listing #' . rand(0, 100),
      'custom_identifier' => $this->last_custom_identifier,
      'active' => true,
      'bathrooms_full' => 2,
      'bathrooms_half' => 1,
      'bedrooms'=> 3,
      'floor_area_square_feet' => 1545,
      'listing_class' => 'quick-possession',
      'price' => 450000,
      'style' => '2-storey',
      'website' => 'http://www.google.com',
      'addresses' => array(
        array(
          'address_1' => '121 Springbank Pl SW',
          'city' => 'Calgary',
          'province' => 'Alberta',
          'country' => 'Canada',
          'postal_code' => 'T3H 3S5',
          'address_type' => 'listing-address',
          'community' => 'Springbank Hill'
        )
      )
    );

    $response = $this->api->createListing($listing);
    echo "Created listing #{$response['id']}: {$response['title']}" . "\n";
  }

  private function test_update_listing_by_key() {
    echo "--- Testing updateListingByKey()\n";
    $listing = array(
      'title' => 'Randomized Title ' . rand(10000,99999)
    );

    $response = $this->api->updateListingByKey('custom_identifier', $this->last_custom_identifier, $listing);

    echo "Listing updated. New title: {$response['title']}\n";
  }

  private function test_update_listing() {
    echo "--- Testing updateListing()\n";

    $listings = $this->api->getListings();
    $id = $listings[0]['id'];

    echo "Original title: {$listings[0]['title']} \n";

    $listing = array(
      'title' => 'Randomized Title ' . rand(10000,99999)
    );

    $response = $this->api->updateListing($id, $listing);

    echo "New title: {$response['title']} \n";
  } 

  private function test_delete_listing() {
    echo "--- Testing deleteListing()\n";

    $listings = $this->api->getListings();
    $id = $listings[0]['id'];

    echo "Found " . count($listings) . " listings. \n";

    $response = $this->api->deleteListing($id);

    $remaining_listings = $this->api->getListings();
    echo "After deletion, there are now " . count($remaining_listings) . " listings.\n";
  }

  private function test_delete_listing_by_key() {
    echo "--- Testing deleteListingByKey()\n";

    $listings = $this->api->getListings();
    echo "Before deletion, there are " . count($listings) . " listings. \n";
    $response = $this->api->deleteListingByKey('custom_identifier', $this->last_custom_identifier);

    $remaining_listings = $this->api->getListings();
    echo "After deletion, there are " . count($remaining_listings) . " listings. \n";
  }
}


$test = new NHLS_Api_Test();
$test->process();
