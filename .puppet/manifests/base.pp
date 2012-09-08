# a really simple single node config file

class www1 {

  include apt
  include apache2::debian
  include mysql
  include php5
  include php5::apache2
  include postfix

  # vcap.me is a domain owned by VMWARE, which points to 127.0.0.1, as does *.vcap.me - so we can avoid using
  # the hosts file, which I prefer. Access site via something.vcap.me:8080 (or whatever port you've forwarded - vagrant shows it at boot time).

  apache2::site {"wpsite":
    root            => "/vagrant",
    servername      => "wpsite.vcap.me",
  }

  # for use on a host only network - don't leave it like this if you use bridged 
  # networking or port forwarding and work on public networks - put a password in
  mysql::user { "root":
    database => "*",
    host     => "%",
    password => "",
  }


  mysql::database { "wpsite":

  }

}

include www1
