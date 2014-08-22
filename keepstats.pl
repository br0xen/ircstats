use strict;
use vars qw($VERSION %IRSSI);

use Irssi qw(command_bind signal_add);
use DBI;

# This is the irssi plugin for the ircstats application

$VERSION = '0.10';
%IRSSI = (
  authors => 'Brian Buller',
  contact => 'brbuller@gmail.com',
  name => 'keepstats',
  description => 'Track stats/history!'
);

# Right now, I'm just keeping stats in #devict
my @statsChannels = ("#devict");
# This needs to be set to the full path of the stats database in the php application
my $db = "stats.db";
# This is the nick of the user with this plugin installed, for the 'own' signals
my $myname = "br0xen";

sub should_bail {
  my ($server, $target, $nick, $nick_addr) = @_;
  my $bail = 1;
  my $c;
  foreach $c (@statsChannels) {
    if(lc($c) eq lc($target)) {
      $bail = 0;
    }
  }
  return $bail;
}
sub track_stats_join {
  my ($server, $target, $nick, $nick_addr) = @_;
  if(should_bail($server, $target, $nick, $nick_addr)) { return 0; }
  send_joinpart_to_database($server, $nick, $nick_addr, $target, "JOIN", "", "");
}
sub track_stats_part {
  my ($server, $target, $nick, $nick_addr, $reason) = @_;
  if(should_bail($server, $target, $nick, $nick_addr)) { return 0; }
  send_joinpart_to_database($server, $nick, $nick_addr, $target, "PART", $reason, "");
}
sub track_stats_quit {
  my ($server, $target, $nick, $nick_addr, $reason) = @_;
  if(should_bail($server, $target, $nick, $nick_addr)) { return 0; }
  send_joinpart_to_database($server, $nick, $nick_addr, $target, "QUIT", $reason, "");
}
sub track_stats_kick {
  my ($server, $target, $nick, $kicker, $nick_addr, $reason) = @_;
  if(should_bail($server, $target, $nick, $nick_addr)) { return 0; }
  send_joinpart_to_database($server, $nick, $nick_addr, $target, "KICK", $reason, $kicker);
}

sub track_stats_own_message {
  my ($server, $msg, $target) = @_;
  if(should_bail($server, $target, $myname, "")) { return 0; }
  send_message_to_database($server, $msg, $myname, '', $target, 'MESSAGE');
}
sub track_stats_message {
  my ($server, $msg, $nick, $nick_addr, $target) = @_;
  if(should_bail($server, $target, $nick, $nick_addr)) { return 0; }
  send_message_to_database($server, $msg, $nick, $nick_addr, $target, 'MESSAGE');
}

sub track_stats_action {
  my ($server, $msg, $nick, $nick_addr, $target) = @_;
  if(should_bail($server, $target, $nick, $nick_addr)) { return 0; }
  send_message_to_database($server, $msg, $nick, $nick_addr, $target, 'ACTION');
}

sub track_stats_ownaction {
  my ($server, $msg, $target) = @_;
  if(should_bail($server, $target, $myname, "")) { return 0; }
  send_message_to_database($server, $msg, $myname, '', $target, 'ACTION');
}

sub send_joinpart_to_database {
  my ($server, $nick, $nick_addr, $target, $type, $reason, $kicker) = @_;
  my $dbh = DBI->connect(
      "dbi:SQLite:dbname=".$db,
      "","",{ RaiseError => 1},) or die $DBI::errstr;
  my $insert_query = "INSERT INTO joinpart(nick, address, server, channel, type, "
                    ."message, kicker) "
                    ."VALUES(?, ?, ?, ?, ?, ?, ?);";
  my $sth = $dbh->prepare($insert_query);
  $sth->execute($nick, $nick_addr, $server->{address}, $target, $type, $reason, $kicker);
  $dbh->disconnect();
}

sub send_message_to_database {
  my ($server, $msg, $nick, $nick_addr, $target, $type) = @_;
  my $dbh = DBI->connect(
      "dbi:SQLite:dbname=".$db,
      "","",{ RaiseError => 1},) or die $DBI::errstr;
  my $insert_query = "INSERT INTO message(nick, address, server, channel, message, type) "
                    ."VALUES(?, ?, ?, ?, ?, ?);";

  my $sth = $dbh->prepare($insert_query);
  $sth->execute($nick, $nick_addr, $server->{address}, $target, $msg, $type);
  $dbh->disconnect();
}

Irssi::signal_add("message public", "track_stats_message");
Irssi::signal_add("message own_public", "track_stats_own_message");
Irssi::signal_add("message join", "track_stats_join");
Irssi::signal_add("message part", "track_stats_part");
Irssi::signal_add("message quit", "track_stats_quit");
Irssi::signal_add("message kick", "track_stats_kick");
Irssi::signal_add("message irc action", "track_stats_action");
Irssi::signal_add("message irc own_action", "track_stats_ownaction");
