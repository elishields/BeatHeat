<?php

// Returns cleaned and encoded search term query
function getQuery($request) {
  // Raw query from POSTed form
  $query_raw = $request->input('query', ' ');
  // To lowercase
  $query_low = strtolower($query_raw);
  // Strip out non-alphanumeric characters
  $query_alphanum = preg_replace("/[^A-Za-z0-9 ]/", '', $query_low);
  // Encode for HTTP
  $query_enc = urlencode($query_alphanum);

  return($query_enc);
}

// Returns past date to limit search to
function getDeadline() {
  // Limit search to past 3 months
  $history_raw = strtotime("-3 months");
  // YouTube API datetime format
  $history_fmt = date("Y-m-d\TH:i:s\Z", $history_raw);
  // Encode for inclusion in URL
  $history_enc = urlencode($history_fmt);

  return($history_enc);
}

// Returns URL for accessing the 5 most relevant videos
function buildVideoIdUrl($query, $history) {
  $url_base    = "https://www.googleapis.com/youtube/v3/search";

  $part        = "?" . "part="           . "snippet";
  $max_results = "&" . "maxResults="     . "5";
  $order       = "&" . "order="          . "viewCount";
  $type        = "&" . "type="           . "video";
  $pub_date    = "&" . "publishedAfter=" . $history;
  $query       = "&" . "q="              . $query;
  // $key         = "&" . "key="            . "AIzaSyDRMdYvc2jL8FWkZ8zDbb5N2EPL5jYaGaY";
  $key         = "&" . "key="            . config('api,key');

  // Join url parts
  $url  = $url_base . $part . $max_results . $order . $type . $pub_date . $query . $key;

  return($url);
}

// Returns decoded data from API
function callApi($url) {
  // Call API
  $json = file_get_contents($url);
  // Decode JSON
  $data = json_decode($json, true);

  return($data);
}

// Returns id's of related videos
function getVideoIds($ids_data) {
  $ids = [];
  for ($i = 0; $i < sizeof($ids_data['items']); $i++) {
    $ids[$i] = $ids_data['items'][$i]['id']['videoId'];
  }

  return($ids);
}

// Returns viewcounts of related videos
function getViewCounts($ids) {
  $views = [];

  // Get viewcount's
  for ($i = 0; $i < sizeof($ids); $i++) {
    $url_base = "https://www.googleapis.com/youtube/v3/videos";

    $part     = "?" . "part=" . "statistics";
    $id       = "&" . "id="   . $ids[$i];
    $key      = "&" . "key="  . config('api,key');
    // $key      = "&" . "key="  . "AIzaSyDRMdYvc2jL8FWkZ8zDbb5N2EPL5jYaGaY";

    // Join url parts
    $url      = $url_base . $part . $id . $key;

    // Get viewcount's
    $data = callApi($url);
    $value = $data['items'][0]['statistics']['viewCount'];
    if (is_numeric($value)) {
      $views[$i] = $value;
    } else {
      $views[i] = 0;
    }
  }

  return($views);
}

// Sums up total views of video results
function sumViews($views) {
  $sum = 0;
  for ($i = 0; $i < 5; $i++) {
    $sum += $views[$i];
  }

  return($sum);
}

// Build an answer string to return to the view (webpage)
function analyzeViews($sum) {
  $ans = number_format($sum);
  $response = "";

  if ($sum > 100000000) { // 100 million views
    $response = $ans . " views ... hot!";
  } elseif ($sum > 10000000) { // 10 million views
    $response = $ans . " views ... warmish ...";
  } else { // Less than 10 million views
    $response = $ans . " views ... cooold.";
  }

  return($response);
}

?>
