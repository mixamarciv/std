<?php
//include_once("strFunc.php");
//-----------------------------------------------------------------------------
//выполняем скрипт так чтоб ему не были доступны текущие локальные переменные кроме $data
function my_eval($eval_script,$data){
  eval($eval_script);
  
//my_var_dump_html2("data",$data);

/*
echo "
<script type=\"text/javascript\" >
\$(function(){
  \$(\"#{$data['id_this_form']}\").find(\".field_id_table\").qs_window({
      url: window.webpath_qs_window(),
      query: { //параметры запроса
              query_template: \"SELECT ##[fields]## FROM my_table t WHERE 1=1 ##[query_filter]## \",
              query_fields: {value:\"t.id_table\",query_field:\"'['||t.id_table||'] '||t.name||' ('||t.table_name||')'\",info_field:\"t.id_table\"},
              options: {id_db:0}
          },
      onSelectItem: function(obj,new_value,old_value){ //при выборе элемента function(obj,new_value,old_value){}
          obj.html(new_value.query_field);
          obj.attr(\"value\",new_value.value);
      },  
      test:0
  });

});
</script>";

echo " 1
<div class=field_id_table type=\"text\" field=\"id_table\" value=\"{$data['field_data']}\" qs_window_id_obj=\"qs_{$data['id_form']}_table\">
{$data['field_data']}
</div>

";
*/

}
//-----------------------------------------------------------------------------
function my_eval_get_str($eval_script,$data){
    ob_start();
    my_eval($eval_script,$data);
    $str = ob_get_contents();
    ob_end_clean();
    return $str;
}
//-----------------------------------------------------------------------------