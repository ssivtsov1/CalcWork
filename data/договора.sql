update `spr_uslug`
set exec_person='Коломійчук Г.П.', exec_person_pp='Коломійчук Г.П.'
where  exec_uslug like '%Лахно%'


select distinct a.*,b.Director,b.parrent_nazv,b.mail,
                case when a.res in ("СДІЗП","СЗОЕ") then e.exec_person else c.exec_person end as exec_person,
                case when a.res in ("СДІЗП","СЗОЕ") then e.exec_person_pp else c.exec_person_pp end as exec_person_pp,
                case when a.res in ("СДІЗП","СЗОЕ") then e.exec_post else c.exec_post end as exec_post,
                case when a.res in ("СДІЗП","СЗОЕ") then e.exec_post_pp else c.exec_post_pp end as exec_post_pp,
                case when a.res in ("СДІЗП","СЗОЕ") then e.assignment else c.assignment end as assignment,
                case when a.res in ("СДІЗП","СЗОЕ") then e.date_assignment else c.date_assignment end as date_assignment, 
               c.usluga as usl
                from vschet a left join spr_res b on a.res=b.nazv
                left join costwork d on a.usluga=d.work 
                left join spr_uslug c on c.usluga=d.usluga 
                left join spr_uslug e on 1=1 and e.id=17  where schet='00006220' limit 1
                
