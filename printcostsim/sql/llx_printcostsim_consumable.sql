-- Table des consommables d'impression
CREATE TABLE @DB_PREFIX@printcostsim_consumable (
    rowid int(11) NOT NULL AUTO_INCREMENT,
    ref varchar(128) NOT NULL,
    label varchar(255) NOT NULL,
    description text,
    type varchar(64) DEFAULT 'ink',
    color varchar(32),
    brand varchar(128),
    model varchar(128),
    capacity_ml decimal(10,2),
    capacity_pages int(11),
    cost_unit decimal(10,2) DEFAULT 0.00,
    cost_per_ml decimal(10,4) DEFAULT 0.0000,
    cost_per_page decimal(10,4) DEFAULT 0.0000,
    coverage_percent decimal(5,2) DEFAULT 5.00,
    active tinyint(1) DEFAULT 1,
    date_creation datetime NOT NULL,
    tms timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fk_user_creat int(11),
    fk_user_modif int(11),
    import_key varchar(14),
    status int(11) DEFAULT 1,
    PRIMARY KEY (rowid),
    UNIQUE KEY uk_printcostsim_consumable_ref (ref)
) ENGINE=InnoDB;

