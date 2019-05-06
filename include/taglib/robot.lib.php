<?php

/**
 *
 * @name           caijixia for dedecms
 * @version        V2.5 2011/09/01 00:00 qjpemai $
 * @copyright      Copyright (c) 2011，caijixia.net.
 * @license        This is NOT a freeware, use is subject to license terms
 */

if(!defined('DEDEINC')) exit('Request Error!');

function lib_robot(&$ctag,&$refObj)
{
    $cfg_automatic =  $GLOBALS['kw_automatic'];
    if($cfg_automatic == 1){
        global $cfg_cmsurl;
        $copyright = $ctag->GetAtt('copyright');
        if(preg_match('/qjpemail/',$copyright))
        {
            $jsvar = '<script type="text/javascript" src="'.$cfg_cmsurl.'/Plugins/apps/CaiJiXia/cjx.js"></script>';
        }else
        {
            $jsvar = '<script type="text/javascript">alert("智能采集模块异常，请检查调用代码是否正确");</script>';
        }
        return $jsvar;
    }
    return '';
}
?>