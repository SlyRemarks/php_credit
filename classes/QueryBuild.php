<?php

class QueryBuild
{
  private $page         = 0;
  private $limit        = 0;
  private $last_updated = 0;
  private $date_now     = 0;
  
  public function __construct($page, $limit, $last_updated, $date_now)
  {
    $this->page         = $page;
    $this->limit        = $limit;
    $this->last_updated = $last_updated;
    $this->date_now     = $date_now;
  }
  
  function queryBuild()
  {
    
    $query = http_build_query([
     'min_date_modified' => (string)$this->last_updated,
     'max_date_modified' => (string)$this->date_now,
     "limit"             => (string)$this->limit,
     "page"              => (string)$this->page,
    ]);
    
    return $query;
  }
}

?>