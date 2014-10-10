<?php

class Irc_model extends Model {
  private $db;
  private $channel = '#devict';
  public function __construct($db) {
    $this->db = $db;
  }

  public function getMessages($parms=NULL) {
    $select_query = 'SELECT * FROM message ';
    if($parms!==NULL) {
      $where_query = '';
      // Build the query manually
      foreach($parms as $k => $v) {
        switch($k) {
          case 'ids':
            $all_ids = split(',',$v);
            foreach($all_ids as $an_id) {
              if(!empty($an_id)) {
                $where_query = $this->_addToWhere($where_query, 'id = '.$an_id, 'OR');
              }
            }
            break;
          case 'start':
            $where_query = $this->_addToWhere($where_query, 'time >= '.$v);
            break;
          case 'end':
            $where_query = $this->_addToWhere($where_query, 'time <= '.$v);
            break;
          case 'from_id':
            $where_query = $this->_addToWhere($where_query, 'id >= '.$v);
            break;
          case 'to_id':
            $where_query = $this->_addToWhere($where_query, 'id <= '.$v);
            break;
          case 'channel': 
            $this->channel = '#'.$v;
            break;
          case 'time':
          case 'nick':
          case 'address':
          case 'server':
          case 'message':
            $where_query = $this->_addToWhere($where_query, $k.' = "'.$v.'"');
        }
      }
    }
    $where_query = $this->_addToWhere($where_query, 'channel = "'.$this->channel.'"');
    return $this->db->query($select_query.$where_query)->fetch_array();
  }

  public function getMessagesBind($parms) {
    $this->db->where($parms);
    return $this->db->get('message')->fetch_array();
  }

  public function searchMessages($srch_term, $parms) {
    $select_query = 'SELECT * FROM message ';
    if(is_array($srch_term)) {
      $where_query = '';
      foreach($srch_term as $a_term) {
        $where_query = $this->_addToWhere($where_query, 'message LIKE "%'.$a_term.'%"', 'OR');
      }
    } else {
      $where_query = $this->_addToWhere('', 'message LIKE "%'.$srch_term.'%"');
    }
    if($parms!==NULL) {
      // Build the query manually
      foreach($parms as $k => $v) {
        switch($k) {
          case 'start':
            $where_query = $this->_addToWhere($where_query, 'time >= '.$v);
            break;
          case 'end':
            $where_query = $this->_addToWhere($where_query, 'time <= '.$v);
            break;
          case 'channel':
            $this->channel = '#'.$v;
            break;
          case 'time':
          case 'nick':
          case 'address':
          case 'server':
          case 'message':
            $where_query = $this->_addToWhere($where_query, $k.' = "'.$v.'"');
        }
      }
    }
    $where_query = $this->_addToWhere($where_query, 'channel = "'.$this->channel.'"');
    return $this->db->query($select_query.$where_query)->fetch_array();
  }

  public function getJoinParts($parms=NULL) {
    $select_query = 'SELECT * FROM joinpart ';
    $where_query = '';
    if($parms!==NULL) {
      // Build the query manually
      foreach($parms as $k => $v) {
        switch($k) {
          case 'start':
            $where_query = $this->_addToWhere($where_query, 'time >= '.$v);
            break;
          case 'end':
            $where_query = $this->_addToWhere($where_query, 'time <= '.$v);
            break;
          case 'channel':
            $this->channel = '#'.$v;
            break;
          case 'time':
          case 'nick':
          case 'address':
          case 'server':
          case 'type':
          case 'message':
          case 'kicker':
            $where_query = $this->_addToWhere($where_query, $k.' = "'.$v.'"');
        }
      }
    }
    $where_query = $this->_addToWhere($where_query, 'channel = "'.$this->channel.'"');
    return $this->db->query($select_query.$where_query)->fetch_array();
  }

  public function getQuits($parms=NULL) {
    $select_query = 'SELECT * FROM joinpart ';
    $where_query = $this->_addToWhere('', 'type = "QUIT"');
    if($parms!==NULL) {
      // Build the query manually
      foreach($parms as $k => $v) {
        switch($k) {
          case 'start':
            $where_query = $this->_addToWhere($where_query, 'time >= '.$v);
            break;
          case 'end':
            $where_query = $this->_addToWhere($where_query, 'time <= '.$v);
            break;
          case 'channel':
            $this->channel = '#'.$v;
            break;
          case 'time':
          case 'nick':
          case 'address':
          case 'server':
          case 'message':
          case 'kicker':
            $where_query = $this->_addToWhere($where_query, $k.' = "'.$v.'"');
        }
      }
    }
    return $this->db->query($select_query.$where_query)->fetch_array();
  }

  public function getStatistics($parms=NULL) {
    if(!isset($parms)) {
      return $this->db->query('SELECT * FROM statistics WHERE ID = (SELECT MAX(ID) FROM statistics)')->fetch_array();
    }
  }

  public function saveStatistics($start, $end, $stats) {
    $this->db->insert('statistics', array('start_time' => $start, 'end_time' => $end, 'statistics' => $stats));
  }

  public function _addToWhere($where, $str, $join_type='AND') {
    if(empty($where)) {
      $where = 'WHERE '.$str;
    } else {
      $where .= ' '.$join_type.' '.$str;
    }
    return $where;
  }
}
