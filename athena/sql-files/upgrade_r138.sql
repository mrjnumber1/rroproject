
INSERT INTO `item_db2` (`id`, `name_english`, `name_japanese`, `type`, `price_buy`, `price_sell`, `weight`, `attack`, `defence`, `range`, `slots`, `equip_jobs`, `equip_upper`, `equip_genders`, `equip_locations`, `weapon_level`, `equip_level`, `refineable`, `view`, `script`, `equip_script`, `unequip_script`) VALUES
(598, 'Light_Red_Pot', 'Light Red Potion', 0, 50, NULL, 1, NULL, NULL, NULL, NULL, 4294967295, 7, 2, NULL, NULL, NULL, NULL, NULL, 'itemheal rand(45,65),0;', NULL, NULL),
(599, 'Light_Orange_Pot', 'Light Orange Potion', 0, 200, NULL, 1, NULL, NULL, NULL, NULL, 4294967295, 7, 2, NULL, NULL, NULL, NULL, NULL, 'itemheal rand(105,145),0;', NULL, NULL),
(11500, 'Light_Yellow_Pot', 'Light Yellow Potion', 0, 550, NULL, 1, NULL, NULL, NULL, NULL, 4294967295, 7, 2, NULL, NULL, NULL, NULL, NULL, 'itemheal rand(175,235),0;', NULL, NULL),
(11501, 'Light_White_Pot', 'Light White Potion', 0, 1200, NULL, 1, NULL, NULL, NULL, NULL, 4294967295, 7, 2, NULL, NULL, NULL, NULL, NULL, 'itemheal rand(325,405),0;', NULL, NULL),
(11502, 'Light_Blue_Pot', 'Light Blue Potion', 0, 5000, NULL, 1, NULL, NULL, NULL, NULL, 4294967295, 7, 2, NULL, NULL, NULL, NULL, NULL, 'itemheal 0,rand(40,60);', NULL, NULL),
(14509, 'Light_Center_Pot', 'Light Concentration Potion', 2, 800, NULL, 1, NULL, NULL, NULL, NULL, 4294967295, 7, 2, NULL, NULL, NULL, NULL, NULL, 'sc_start SC_ASPDPOTION0,1800000,0;', NULL, NULL),
(14510, 'Light_Awakening_Pot', 'Light Awakening Potion', 2, 1500, NULL, 1, NULL, NULL, NULL, NULL, 4294442735, 7, 2, NULL, NULL, 40, NULL, NULL, 'sc_start SC_ASPDPOTION1,1800000,0;', NULL, NULL),
(14511, 'Light_Berserk_Pot', 'Light Berserk Potion', 2, 3000, NULL, 1, NULL, NULL, NULL, NULL, 31868582, 7, 2, NULL, NULL, 85, NULL, NULL, 'sc_start SC_ASPDPOTION2,1800000,0;', NULL, NULL);
