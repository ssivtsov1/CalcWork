create view vspr_res_koord as
select a.*,b.nazv 
from spr_res_koord a inner join spr_res b on 
a.id_res=b.id
