CREATE VIEW `vtransport` AS select `a`.`id` AS `id`,`a`.`transport` AS `transport`,`a`.`nomer` AS `nomer`,`a`.`locale` AS `locale`,`a`.`prostoy` AS `prostoy`,`a`.`proezd` AS `proezd`,`a`.`rabota` AS `rabota`,`b`.`nazv` AS `nazv` from (`transport` `a` left join `spr_res` `b` on((`a`.`locale` = `b`.`id`)))
