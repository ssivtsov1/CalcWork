create view vw_analit as
SELECT a.*,b.usluga as usl FROM vschet a
inner join costwork b on a.usluga=b.work
