<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Http\Request;

Route::get('/', function () {
  $answer = ""; // Nothing searched yet
  return view('beatheat', ['answer' => $answer]);
});

Route::post('/query', function (Request $request) {

  $input_query = $request->input('query', 'beats'); // Get query from POST form

  $q_lower = strtolower($input_query); // Query to lowercase
  $q_alphanum = preg_replace("/[^A-Za-z0-9 ]/", '', $q_lower); // Alphanum only
  $q_enc = urlencode($q_alphanum);

  // Get top 5 videos by viewcount for query
  $url_base    = "https://www.googleapis.com/youtube/v3/search";
  $part        = "?" . "part="       . "snippet";
  $max_results = "&" . "maxResults=" . "5";
  $order       = "&" . "order="      . "viewCount";
  $type        = "&" . "type="       . "video";
  $query       = "&" . "q="          . $q_enc;
  $key         = "&" . "key="        . "AIzaSyDRMdYvc2jL8FWkZ8zDbb5N2EPL5jYaGaY";

  $url  = $url_base . $part . $max_results . $order . $type . $query . $key;
  $json = file_get_contents($url);
  $data = json_decode($json, true);

  $video_ids = [];
  $viewcounts = [];

  // Get video id's
  for ($i = 0; $i < 5; $i++) {
    $video_ids[$i] = $data['items'][$i]['id']['videoId'];
  }

  // Get viewcount's
  for ($i = 0; $i < 5; $i++) {
    $url_base = "https://www.googleapis.com/youtube/v3/videos";
    $part     = "?" . "part=" . "statistics";
    $id       = "&" . "id="   . $video_ids[$i];
    $key      = "&" . "key="  . "AIzaSyDRMdYvc2jL8FWkZ8zDbb5N2EPL5jYaGaY";
    $url      = $url_base . $part . $id . $key;

    $json = file_get_contents($url);
    $data = json_decode($json, true);
    $viewcounts[$i] = $data['items'][0]['statistics']['viewCount'];
  }

  $map = [];
  for ($i = 0; $i < 5; $i++) {
    $map[$video_ids[$i]] = $viewcounts[$i];
  }
  var_dump($map);

  return view('beatheat', ['answer' => 'ans']);
});
