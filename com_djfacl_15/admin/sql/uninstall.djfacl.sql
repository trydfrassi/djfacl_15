update #__modules m, #__groups g set m.access = 0 where m.access = g.id and g.name = "djfacl";
DROP TABLE #__djfacl_contenuti;
DROP TABLE #__djfacl_gruppi_utenti;
DROP TABLE #__djfacl_gruppi_icone;
DROP TABLE #__djfacl_cssblock;
DROP TABLE #__djfacl_quickicon;
DROP TABLE #__djfacl_jtask;
DROP TABLE #__djfacl_components;

update
#__core_acl_groups_aro_map as aa,
#__core_acl_aro_groups as bb,
#__users as uu
set uu.gid = 18, uu.usertype="Registered"
where
bb.`value` = "djfacl"
and
bb.id = aa.group_id
and
uu.gid = bb.id;


update
#__core_acl_groups_aro_map as aa,
#__core_acl_aro_groups as bb
set aa.group_id = 18
where bb.`value` = "djfacl" and bb.id = aa.group_id;



delete from #__core_acl_aro_groups where `value` = "djfacl";


		





