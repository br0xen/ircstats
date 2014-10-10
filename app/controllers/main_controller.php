<?php

class Main_controller extends Controller {
  public $irc; // The data model
  public $parms;
  public $default_function = 'statistics';
  public $view_data = array();
  // Right now only one channel at a time is allowed
  public $current_channel = '#devict';

  public function __construct($a) {
    parent::__construct($a);
    $this->load_library('sqlite');
    $this->load_model('irc');
    $this->load_helper('irc');
    $this->irc = new Irc_model(new Anvil_SQLite());
    $this->parms = $this->anvil->request->uriToPairs(1);
    $this->view_data['stylesheets'] = array(
        '/assets/css/pure-min.css',
        '/assets/css/font-awesome/css/font-awesome.min.css',
        '/assets/css/main.css'
    );
    $this->view_data['scripts'] = array(
        '/assets/js/B.js',
        '/assets/js/main.js'
    );
    $this->view_data['page_title'] = $this->anvil->config->item('site_title');
    $this->view_data['current_channel'] = $this->current_channel;
  }

  public function statistics() {
    $this->view_data['current_page'] = 'statistics';
    $view_title = 'IRC Statistics Dashboard';
    $get_stats = $this->irc->getStatistics();
    $last_stat = array_shift($get_stats);
    if(isset($this->parms['user']) && !empty($this->parms['user'])) {
      $this->view_data['user'] = $this->parms['user'];
    }
    if(isset($this->parms['wordlength']) && is_numeric($this->parms['wordlength'])) {
      $this->view_data['override_min_word_length']=$this->parms['wordlength'];
    }
    // Calculate Statistics since the last Calculation ($last_stat['end_time'])
    $stats = json_decode($last_stat['statistics'], TRUE);
    $this->view_data['stats'] = $stats;
    $this->view_data['stats_time'] = $last_stat['end_time'];
    $this->view_data['view_title'] = 'Statistics as of '.date('Y-m-d H:i:s', $last_stat['end_time']);
    $this->view_data['scripts'][]='/assets/js/highcharts.js';
    $this->view_data['scripts'][]='/assets/js/dashboard.js';
    $this->view_data['stylesheets'][]='/assets/css/dashboard.css';
    $this->load_views(array('header', 'dashboard', 'footer'), $this->view_data);
  }

  public function _search() {
    $view_title = 'Displaying IRC History: ';
    if(isset($this->parms['search'])) {
      $search = $this->parms['search'];
      unset($this->parms['search']);
      $msgs = $this->irc->searchMessages($search, $this->parms);
      $view_title.=' Containing "'.$search.'"';
    } else {
      $msgs = $this->irc->getMessages($this->parms);
    }
    $final_array = array();
    foreach($msgs as $a_msg) {
      $final_array[$a_msg['time']] = $a_msg;
    }
    ksort($final_array);
    $this->view_data['history'] = $final_array;
    $this->view_data['view_title'] = $view_title;
    $this->view_data['is_searching'] = TRUE;
    array_push($this->view_data['stylesheets'], '/assets/css/history.css');
    array_push($this->view_data['scripts'], '/assets/js/history.js');
    $this->load_views(array('header','history','footer'), $this->view_data);
  }

  public function testhistory() {
    $jpts = $this->irc->getJoinParts($this->parms);
    print_r($jpts);
  }

  public function history() {
    $this->view_data['current_page'] = 'history';
    if(count($this->parms) > 0) {
      $this->view_data['is_searching'] = TRUE;
    }
    if(isset($this->parms['search'])) {
      return $this->_search();
    }
    if(!isset($this->parms['start'])) {
      $this->parms['start'] = strtotime('-1 day');
    }
    if(!isset($this->parms['end'])) {
      $this->parms['end'] = strtotime('now');
    }
    if(isset($this->parms['hide_joinparts'])) {
      unset($this->parms['hide_joinparts']);
    } else {
      $jpts = $this->irc->getJoinParts($this->parms);
    }
    $msgs = $this->irc->getMessages($this->parms);
    $final_array = array();
    foreach($msgs as $a_msg) {
      $final_array[$a_msg['time']] = $a_msg;
    }
    foreach($jpts as $a_jpt) {
      $final_array[$a_jpt['time']] = $a_jpt;
    }
    ksort($final_array);
    $this->view_data['jump_back_day'] = strtotime('-1 day', $this->parms['start']);
    $this->view_data['current_end_day'] = $this->parms['end'];
    $this->view_data['history'] = $final_array;
    $last_entry = end($this->view_data['history']);
    $first_entry = reset($this->view_data['history']);
    $this->view_data['view_title'] = 'Showing Channel History from '.date('Y-m-d H:i:s', $first_entry['time']).' to '.date('Y-m-d H:i:s', $last_entry['time']);

    array_push($this->view_data['stylesheets'], '/assets/css/history.css');
    array_push($this->view_data['scripts'], '/assets/js/history.js');
    $this->load_views(array('header','history','footer'), $this->view_data);
  }

  public function links() {
    $msgs = $this->irc->searchMessages(array('http://','https://'), array());
    $this->view_data['current_page'] = 'links';
    $this->view_data['view_title'] = 'Posted Links';
    usort($msgs, "_cmp_link_array");
    $this->view_data['link_messages'] = $msgs;
    $this->view_data['scripts'][]='/assets/js/links.js';
    $this->view_data['stylesheets'][]='/assets/css/links.css';
    $this->load_view(array('header','links','footer'), $this->view_data);
  }

  public function doStats() {
    $stats = array();
    $get_stats = $this->irc->getStatistics();
    $last_stat = array_shift($get_stats);
    // Calculate Statistics since the last Calculation ($last_stat['time'])
    $parms['start'] = $last_stat['end_time'];
    $parms['end'] = time();
    $msgs = $this->irc->getMessages($parms);
    $stats = json_decode($last_stat['statistics'], TRUE);
    if(!isset($stats['users'])) { $stats['users'] = array(); }
    if(!isset($stats['word_usage'])) { $stats['word_usage'] = array(); }
    if(!isset($stats['letter_usage'])) { $stats['letter_usage'] = array(); }
    if(!isset($stats['active_time'])) { $stats['active_time'] = array(
        0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0
    ); }
    if(!isset($stats['capitals'])) { $stats['capitals'] = array('all' => 0, 'none' => 0, 'mixed' => 0); }

    foreach($msgs as $a_msg) {
      $a_msg['message'] = removeLinks($a_msg['message']);
      if(!isset($stats['users'][$a_msg['nick']])) {
        $stats['users'][$a_msg['nick']] = array();
        $stats['users'][$a_msg['nick']]['message_count'] = 0;
        $stats['users'][$a_msg['nick']]['letter_usage'] = array();
        $stats['users'][$a_msg['nick']]['word_usage'] = array();
        $stats['users'][$a_msg['nick']]['active_time'] = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
        $stats['users'][$a_msg['nick']]['capitals'] = array('all' => 0, 'none' => 0, 'mixed' => 0);
      }
      // Check caps
      if($a_msg['message'] == strtoupper($a_msg['message'])) {
        $stats['capitals']['all']++;
        $stats['users'][$a_msg['nick']]['capitals']['all']++;
      } else if($a_msg['message'] == strtolower($a_msg['message'])) {
        $stats['capitals']['none']++;
        $stats['users'][$a_msg['nick']]['capitals']['none']++;
      } else {
        $stats['capitals']['mixed']++;
        $stats['users'][$a_msg['nick']]['capitals']['mixed']++;
      }
      // Check message time
      $stats['users'][$a_msg['nick']]['active_time'][date('G',$a_msg['time'])]++;
      $stats['active_time'][date('G',$a_msg['time'])]++;
      // Add to message count
      $stats['users'][$a_msg['nick']]['message_count']++;
      // Add to word usage
      foreach(str_word_count($a_msg['message'], 2) as $a_word) {
        if(!isset($stats['users'][$a_msg['nick']]['word_usage'][$a_word])) {
          $stats['users'][$a_msg['nick']]['word_usage'][$a_word] = 0;
        }
        if(!isset($stats['word_usage'][$a_word])) {
          $stats['word_usage'][$a_word]=0;
        }
        $stats['word_usage'][$a_word]++;
        $stats['users'][$a_msg['nick']]['word_usage'][$a_word]++;
      }
      // Add to letter usage
      foreach(count_chars($a_msg['message']) as $i => $val) {
        if($i < 32 || $i > 126) { continue; }
        if(!isset($stats['users'][$a_msg['nick']]['letter_usage'][chr($i)])) {
          $stats['users'][$a_msg['nick']]['letter_usage'][chr($i)]=0;
        }
        if(!isset($stats['letter_usage'][chr($i)])) {
          $stats['letter_usage'][chr($i)]=0;
        }
        $stats['users'][$a_msg['nick']]['letter_usage'][chr($i)]+=$val;
        $stats['letter_usage'][chr($i)]+=$val;
      }
    }
    ksort($stats['users']);
    ksort($stats);
    $this->irc->saveStatistics($parms['start'], $parms['end'], json_encode($stats));
    $jpts = $this->irc->getJoinParts($parms);
  }

  public function getJSONHistory() {
    if(count($this->parms) > 0) {
      $this->view_data['is_searching'] = TRUE;
    }
    if(isset($this->parms['search'])) {
      return $this->_search();
    }
    if(!isset($this->parms['start'])) {
      $this->parms['start'] = strtotime('-1 day');
    }
    if(!isset($this->parms['end'])) {
      $this->parms['end'] = strtotime('now');
    }
    $msgs = $this->irc->getMessages($this->parms);
    $jpts = $this->irc->getJoinParts($this->parms);
    $this->view_data['json'] = $msgs;
    $this->load_view('json_output', $this->view_data);
  }

  public function getJSONStats() {
    $get_stats = $this->irc->getStatistics();
    $last_stat = array_shift($get_stats);
    $last_stat['statistics'] = json_decode($last_stat['statistics'], TRUE);
    $this->view_data['json'] = $last_stat;
    $this->load_view('json_output', $this->view_data);
  }
}

function _cmp_link_array($a, $b) {
  if($a['time'] == $b['time']) {
    return 0;
  }
  return ($a['time'] < $b['time']) ? 1 : -1;
}
