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

Route::get('/', function () {
  $answer = "";
  return view('beatheat', ['answer' => $answer]);
});

Route::get('/query', function () {

  $urlBase = "https://www.googleapis.com/youtube/v3/videos";
  $url = "";

  $request = new HttpRequest(url, HttpRequest::METH_GET);

  try {
    $request->send();
    if ($request->getResponseCode() == 200) {
      file_put_contents('local.rss', $r->getResponseBody());
    }
  } catch (HttpException $e) {
      echo $e;
  }

    // $answer = ;
    return view('beatheat', ['answer' => $answer]);
});
