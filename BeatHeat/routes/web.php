<?php

use Illuminate\Http\Request;
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

Route::get('/', function () {
  $answer = "";
  return view('beatheat', ['answer' => $answer]);
});

Route::post('/query', function (Request $request) {

  $input_query = $request->input('query', 'default_search_term');

// $urlbase = "https://www.googleapis.com/youtube/v3/videos";
// $query = "?"
// $key = "?key=AIzaSyDRMdYvc2jL8FWkZ8zDbb5N2EPL5jYaGaY";
// $url = $urlbase + $query + $key;
//
// $json = file_get_contents($url);
// $dump = var_dump(json_decode($json));

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
  return view('beatheat', ['answer' => $input_query]);
});
