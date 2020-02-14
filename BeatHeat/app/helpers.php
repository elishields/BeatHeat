<?php

// Exits script and returns boilerplat to webpage if no results found
function noResults() {
  return view('beatheat', ['answer' => 'Not enough videos found. Try again.']);
}

// Returns cleaned and encoded search term query
function getQuery($r) {
  // Raw query from POSTed form
  $query_raw = $r->input('query', ' ');
  // To lowercase
  $query_low = strtolower($query_raw);
  // Strip out non-alphanumeric characters
  $query_alphanum = preg_replace("/[^A-Za-z0-9 ]/", '', $query_low); // Alphanum only
  // Check length of query
  if (strlen($query_alphanum) < 1) {
    noResults();
  }
  // Encode for inclusion in URL
  $q_enc = urlencode($query_alphanum);

  return($q_enc);
}

// Returns past date to limit search to
function getDeadline() {
  // Limit search to past 3 months
  $deadline_raw = strtotime("-3 months");
  // YouTube API datetime format
  $deadline_date = date("Y-m-d\TH:i:s\Z", $deadline_raw);
  // Encode for inclusion in URL
  $deadline_enc = urlencode($deadline_date);

  return($deadline_enc);
}

// Returns URL for accessing the 5 most relevant videos
function videoIdUrl($q, $d) {
  $url_base    = "https://www.googleapis.com/youtube/v3/search";
  $part        = "?" . "part="           . "snippet";
  $max_results = "&" . "maxResults="     . "5";
  $order       = "&" . "order="          . "viewCount";
  $type        = "&" . "type="           . "video";
  $pub_date    = "&" . "publishedAfter=" . $d;
  $query       = "&" . "q="              . $q;
  $key         = "&" . "key="            . "AIzaSyDRMdYvc2jL8FWkZ8zDbb5N2EPL5jYaGaY";
  // Join url parts into whole
  $url  = $url_base . $part . $max_results . $order . $type . $pub_date . $query . $key;

  return($url);
}

// Returns decoded data from API
function callApi($u) {
  // Call API
  $json = file_get_contents($u);
  // Decode JSON
  $data = json_decode($json, true);
  // Check if at least 5 related videos exist
  var_dump($data);
  if(sizeof($data['items'], 0) < 5) {
    noResults();
  }

  return($data);
}

// Returns id's of related videos
function getVideoIds($d) {
  $video_ids = [];
  for ($i = 0; $i < sizeof($d['items']); $i++) {
    $video_ids[$i] = $d['items'][$i]['id']['videoId'];
  }

  return($video_ids);
}

// Returns viewcounts of related videos
function getViewCounts($ids) {
  $views = [];
  // Get viewcount's
  for ($i = 0; $i < sizeof($ids); $i++) {
    $url_base = "https://www.googleapis.com/youtube/v3/videos";
    $part     = "?" . "part=" . "statistics";
    $id       = "&" . "id="   . $ids[$i];
    $key      = "&" . "key="  . "AIzaSyDRMdYvc2jL8FWkZ8zDbb5N2EPL5jYaGaY";
    // Join url parts into whole
    $url      = $url_base . $part . $id . $key;

    // Get viewcount's
    $data = callApi($url);
    $views[$i] = $data['items'][0]['statistics']['viewCount'];
  }

  return($views);
}

// Sums up total views of video results
function sumViews($v) {
  $sum = 0;
  for ($i = 0; $i < 5; $i++) {
    $sum += $v[$i];
  }

  return($sum);
}

// Build an answer string to return to the view (webpage)
function analyzeViews($sum) {
  $ans = number_format($sum);
  $ans_final = "";

  if ($sum > 100000000) { // 100 million views
    $ans_final = $ans . " views ... hot!";
  } elseif ($sum > 10000000) { // 10 million views
    $ans_final = $ans . " views ... warmish ...";
  } else { // Less than 10 million views
    $ans_final = $ans . " views ... cooold.";
  }

  return($ans_final);
}

?>
