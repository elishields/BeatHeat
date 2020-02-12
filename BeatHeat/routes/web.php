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

  if (strlen($q_alphanum) < 1) {
    return view('beatheat', ['answer' => 'Not enough videos found. Try again.']);
  }

  $q_enc = urlencode($q_alphanum);

  // Only select for videos from past 3 months
  $deadline_raw = strtotime("-3 months");
  $deadline_date = date("Y-m-d\TH:i:s\Z", $deadline_raw);

  // Get top 5 videos by viewcount for query
  $url_base    = "https://www.googleapis.com/youtube/v3/search";
  $part        = "?" . "part="           . "snippet";
  $max_results = "&" . "maxResults="     . "5";
  $order       = "&" . "order="          . "viewCount";
  $type        = "&" . "type="           . "video";
  $pub_date    = "&" . "publishedAfter=" . urlencode($deadline_date);
  $query       = "&" . "q="              . $q_enc;
  $key         = "&" . "key="            . "AIzaSyDRMdYvc2jL8FWkZ8zDbb5N2EPL5jYaGaY";

  $url  = $url_base . $part . $max_results . $order . $type . $query . $key;
  $json = file_get_contents($url);
  $data = json_decode($json, true);

  // Check if at least 5 related videos exist
  if(count($data['items'], 0) < 5) {
    return view('beatheat', ['answer' => 'Not enough videos found. Try again.']);
  }

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

  $sum = 0;
  for ($i = 0; $i < 5; $i++) {
    $sum += $viewcounts[$i];
  }
  $ans = number_format($sum);

  $final_ans = "";
  if ($sum > 50000000) {
    $final_ans = $ans . " ... That's heat!";
  } elseif ($sum > 1000000) {
    $final_ans = $ans . " ... That's warm!";
  } else {
    $final_ans = $ans . " ... That's cold!";
  }

  return view('beatheat', ['answer' => $final_ans]);
});
