<?php   if(!defined('DEDEINC')) exit('Request Error!');
require_once(DEDEINC."/baidusitemap.func.php");

function lib_baidusitemap(&$ctag,&$refObj)
{
    global $dsql, $envs;
    //���Դ���
    $attlist="type|code";
    FillAttsDefault($ctag->CAttribute->Items,$attlist);
    extract($ctag->CAttribute->Items, EXTR_SKIP);
    
    $reval="";
    
    if( !$dsql->IsTable("#@__plus_baidusitemap_setting") ) return 'û���ٶ�վ������ģ��';
    
    $site_id=baidu_get_setting('site_id');
    if(empty($site_id)) return '��δ��վ��ID�����¼ϵͳ��̨��';
    
    if($type=='code')
    {
        $reval .= <<<EOT
<script type="text/javascript">document.write(unescape('%3Cdiv id="bdcs"%3E%3C/div%3E%3Cscript charset="utf-8" src="http://znsv.baidu.com/customer_search/api/js?sid={$site_id}') + '&plate_url=' + (encodeURIComponent(window.location.href)) + '&t=' + (Math.ceil(new Date()/3600000)) + unescape('"%3E%3C/script%3E'));</script>
EOT;
    }
    
    return $reval;
}
