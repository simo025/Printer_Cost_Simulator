-- Table de liaison simulation-consommable
CREATE TABLE @DB_PREFIX@printcostsim_simulation_consumable (
    rowid int(11) NOT NULL AUTO_INCREMENT,
    fk_simulation int(11) NOT NULL,
    fk_consumable int(11) NOT NULL,
    quantity_used decimal(10,4) DEFAULT 0.0000,
    cost_total decimal(10,4) DEFAULT 0.0000,
    coverage_percent decimal(5,2) DEFAULT 5.00,
    date_creation datetime NOT NULL,
    tms timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (rowid),
    UNIQUE KEY uk_simulation_consumable (fk_simulation, fk_consumable),
    KEY idx_simulation_consumable_simulation (fk_simulation),
    KEY idx_simulation_consumable_consumable (fk_consumable)
) ENGINE=InnoDB;

