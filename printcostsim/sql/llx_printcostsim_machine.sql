-- Table des machines d'impression
CREATE TABLE @DB_PREFIX@printcostsim_machine (
    rowid int(11) NOT NULL AUTO_INCREMENT,
    ref varchar(128) NOT NULL,
    label varchar(255) NOT NULL,
    description text,
    brand varchar(128),
    model varchar(128),
    print_type varchar(64) DEFAULT 'inkjet',
    max_width int(11) DEFAULT 210,
    max_height int(11) DEFAULT 297,
    resolution_dpi int(11) DEFAULT 300,
    speed_ppm int(11) DEFAULT 10,
    cost_per_hour decimal(10,2) DEFAULT 0.00,
    maintenance_cost decimal(10,2) DEFAULT 0.00,
    active tinyint(1) DEFAULT 1,
    date_creation datetime NOT NULL,
    tms timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fk_user_creat int(11),
    fk_user_modif int(11),
    import_key varchar(14),
    status int(11) DEFAULT 1,
    PRIMARY KEY (rowid),
    UNIQUE KEY uk_printcostsim_machine_ref (ref)
) ENGINE=InnoDB;

