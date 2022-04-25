#
# Table structure for table 'tx_rkwform_domain_model_standardform'
#
CREATE TABLE tx_rkwform_domain_model_standardform (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	salutation int(11) DEFAULT '0' NOT NULL,
	first_name varchar(255) DEFAULT '' NOT NULL,
	last_name varchar(255) DEFAULT '' NOT NULL,
	company varchar(255) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,
	phone varchar(255) DEFAULT '' NOT NULL,
	text varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# extend for bst2020Form (bausachverstaendigentag.de)
#
CREATE TABLE tx_rkwform_domain_model_standardform (
  bst_number1 int(11) DEFAULT '0' NOT NULL,
  bst_number2 int(11) DEFAULT '0' NOT NULL,
  bst_number3 int(11) DEFAULT '0' NOT NULL,
  bst_agree tinyint(4) unsigned DEFAULT '0' NOT NULL,
);

#
# extend for gemCommunityForm
#
CREATE TABLE tx_rkwform_domain_model_standardform (
	identifier varchar(255) DEFAULT '' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	street varchar(5) DEFAULT '' NOT NULL,
	postal varchar(5) DEFAULT '' NOT NULL,
	city varchar(255) DEFAULT '' NOT NULL,
	theme varchar(255) DEFAULT '' NOT NULL,
  token varchar(255) DEFAULT '' NOT NULL,
	valid_until int(11) unsigned DEFAULT '0' NOT NULL,
  enabled tinyint(1) DEFAULT '0' NOT NULL,
);

