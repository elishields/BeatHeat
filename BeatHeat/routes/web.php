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

// Functions are defined here
include_once 'helpers.php';

Route::get('/', function () {
  $answer = ""; // Nothing searched yet
  return view('beatheat', ['answer' => $answer]);
});

Route::post('/query', function (Request $request) {

  // Exits script and returns notice to webpage if < 5 results found
  $fail     = view('beatheat', ['answer' => "Not enough videos. Try again."]);

  // Begin processing the query

  // Prepare query
  $query     = getQuery($request);
  if (strlen($query) < 1) { return $fail; }
  $history   = getDeadline();

  // Hit YouTube API
  $ids_url   = buildVideoIdUrl($query, $history);
  $ids_data  = callApi($ids_url);
  if(sizeof($ids_data['items'], 0) < 5) { return $fail; }
  $ids       = getVideoIds($ids_data);
  $views     = getViewCounts($ids);

  // Process returned data
  $sum       = sumViews($views);
  $response  = analyzeViews($sum);

  // Return data to view
  return view('beatheat', ['answer' => $response]);
});

?>
