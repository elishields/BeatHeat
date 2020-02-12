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

  $urlbase = "https://www.googleapis.com/youtube/v3/videos";
  $part   = "?" . "part=" . "statistics";
  $query  = "&" . "q=" . $input_query;
  $chart  = "&" . "chart=mostPopular";
  $region = "&" . "regionCode=US";
  $key    = "&" . "key=AIzaSyDRMdYvc2jL8FWkZ8zDbb5N2EPL5jYaGaY";
  $url    = $urlbase . $part . $query . $chart . $region . $key;
  $url_enc = urlencode($url);

  // $json = file_get_contents($url);
  // $data = json_decode($json, true);
  // $dump = var_dump($data);
  // $data2 = $json[0][0];

// $request = new HttpRequest($url, HttpRequest::METH_GET);
//
// try {
//   $request->send();
//   if ($request->getResponseCode() == 200) {
//     file_put_contents('local.rss', $r->getResponseBody());
//   }
// } catch (HttpException $e) {
//     echo $e;
// }

// $answer = ;
  return view('beatheat', ['answer' => 't']);
});
