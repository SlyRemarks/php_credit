<?php
class QueryBuild {
  function queryBuild()
  {
    global $result,
           $min_date_modified,
           $date_now,
           $limit,
           $page,
           $last_updated;
    
    $min_date_modified = $last_updated;
  
    $query = http_build_query([
     'min_date_modified' => (string)$last_updated,
     'max_date_modified' => (string)$date_now,
     "limit"             => (string)$limit,
     "page"              => (string)$page,
    ]);
    
    return $query;
  }
}
?>