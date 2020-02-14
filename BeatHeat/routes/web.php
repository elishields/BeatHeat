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

include '../app/helpers.php';

Route::get('/', function () {
  $answer = ""; // Nothing searched yet
  return view('beatheat', ['answer' => $answer]);
});

Route::post('/query', function (Request $request) {

  // Exits script and returns notice to webpage if no results found
  $fail     = view('beatheat', ['answer' => "Not enough videos. Try again."]);

  // Begin processing the query

  $query     = getQuery($request);
  if (strlen($query) < 1) {
    return $fail;
  }
  $date      = getDeadline();

  $ids_url   = videoIdUrl($query, $date);
  $ids_data  = callApi($ids_url);
  $ids       = getVideoIds($ids_data);

  $views     = getViewCounts($ids);
  $sum       = sumViews($views);
  $ans       = analyzeViews($sum);

  return view('beatheat', ['answer' => $ans]);
});

?>
