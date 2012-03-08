CREATE TABLE simpleforum_topic (
  id int(11) NOT NULL auto_increment,
  node_id int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  name varchar(150) NOT NULL default '',
  content longtext NOT NULL,
  state enum('VALIDATED', 'MODERATED', 'PUBLISHED', 'CLOSED') default 'PUBLISHED',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE simpleforum_response (
  id int(11) NOT NULL auto_increment,
  topic_id int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  name varchar(150) NOT NULL default '',
  content longtext NOT NULL,
  positive_vote int(11) NOT NULL default '0',
  total_vote int(11) NOT NULL default '0',
  state enum('VALIDATED', 'MODERATED', 'PUBLISHED') default 'PUBLISHED',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (id),
    CONSTRAINT FOREIGN KEY (topic_id)
      REFERENCES simpleforum_topic (id) ON DELETE CASCADE
) ENGINE=InnoDB;