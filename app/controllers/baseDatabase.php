<?php

class baseDatabaseController extends baseController {
	
	public function __construct() {
		if (isset($this->details)) {
			$this->tablename = $this->details['tablename'];
			$this->primary_key = ($this->details['primary']) ? $this->details['primary'] : 'id';
			
			$name = ($this->details['connection']) ? $this->details['connection'] : 'default';
	
			$config = self::$config->get('database',$name);
			
			$this->query = new Query($config['dsn'],$config['user'],$config['password']);
		}
	}

	/* remap second seg */
	public function __call($methodname,$args) {
	
		/* shift the Method (which database elements don't have - back onto the segs */
		self::$app->uri = explode('/',$this->raw_uri);
		array_shift(self::$app->uri);

		switch (self::$app->raw_request) {
			case 'Get':
				return $this->getRest();
			break;
			case 'Post':
				return $this->postRest();			
			break;
			case 'Put':
				return $this->putRest();
			break;
			case 'Delete':
				return $this->deleteRest();
			break;
		}
	}

	public function indexAction() {
		return $this->getRest();
	}

  /* sql select */
  /* returns
  200: records array()
  */
  private function getRest() {
    $calc_ary = array('eq'=>'=','lt'=>'<','gt'=>'>','lk'=>'like');
    $dir_ary = array('a'=>'asc','d'=>'desc','z'=>'desc');

    $sql = "select * from `".$this->tablename."`";

    $arg = 0;
    $where = $orderby = $limit = '';

    // single id
    if ($this->segCount() == 1) {
      $where = " where `".$this->primary_key."` ='".$this->seg($arg++)."'";
    } elseif ($this->segCount() > 1) {
      /*
      bunch of argments
      where + 3 = column + lt,gt,eq,like + value
      orderby + 2 = column + a,d
      limit + # or limit + #,#
      */
      while ($arg <= $this->segCount()) {
        if ($arg > 10) {
        	self::$view->badrequest($errno=903,$errtxt='Arg Overflow Error');
        	return;
        }
        
        if ($this->segClean($arg) == 'search') {
          // where
          $field = $this->segClean(++$arg);
          $calc = @$calc_ary[$this->segClean(++$arg)];
          $val = $this->segClean(++$arg);

          if ($field == NULL || $calc == NULL || $val == NULL) {
          	self::$view->badrequest($errno=900,$errtxt='Search Incorrectly Formatted');
          	return;
          }

          $where = " where `".$field."` ".$calc." '".$val."'";
        }
        
        if ($this->segClean($arg) == 'sort') {
          // sort order by
          $field = $this->segClean(++$arg);          
          $dir = $dir_ary[$this->seg(++$arg)];
          
          if ($field == NULL || $dir == NULL) {
          	self::$view->badrequest($errno=901,$errtxt='Sort Incorrectly Formatted');
          	return;
          }

          $orderby = ' order by `'.$field.'` '.$dir;
        }
        
        if ($this->segClean($arg) == 'limit') {
          // limit # or limit #,#
          $cnt = $this->segClean(++$arg);
          
          if ($cnt == NULL) {
          	$this->badrequest($errno=902,$errtxt='Limit Incorrectly Formatted');
          	return;
          }
    
          $limit = ' limit '.$cnt;
        }
        
        $arg++;
      }
    }
    
    $output = array();
		$query = $sql.$where.$orderby.$limit;

		$rows = $this->query->run($query);
    foreach ((array)$rows as $row) {
      $output[] = $row;
    }
    
    self::$view->set('records',$output)->ok();
  }
  
  	
} /* end database */