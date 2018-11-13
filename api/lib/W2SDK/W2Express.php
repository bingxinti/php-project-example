<?php
/**
 *
 * 快递鸟物流轨迹即时查询接口
 *
 * @author: CQ
 * @qq: 1069712970
 * @see: http://www.kdniao.com/YundanChaxunAPI.aspx
 * @copyright: 深圳市快金数据技术服务有限公司
 *
 * DEMO中的电商ID与私钥仅限测试使用，正式环境请单独注册账号
 * 单日超过500单查询量，建议接入我方物流轨迹订阅推送接口
 *
 * ID和Key请到官网申请：http://www.kdniao.com/ServiceApply.aspx
 * 测试ID和KEY已经关闭
 * ID:1237100
 * KEY:518a73d8-1f7f-441a-b644-33e77b49d846
 */

//电商ID
defined('EBusinessID') or define('EBusinessID', '1256508');
//电商加密私钥，快递鸟提供，注意保管，不要泄漏
defined('AppKey') or define('AppKey', 'b076a2be-b59d-4c7d-9bee-0edb3b983ee9');
//请求url
defined('ReqURL') or define('ReqURL', 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx');


//调用获取物流轨迹
//-------------------------------------------------------------

// $logisticResult = getOrderTracesByJson('YD', '1901280494608');
// $logisticResult = getOrderTracesByJson('STO', '227297035155');
// $logisticResult = getOrderTracesByJson('ZTO', '448722308550');
// $logisticResult = getOrderTracesByJson('HHTT', '666408031063');
// echo $logisticResult;

//-------------------------------------------------------------



/**
 * Json方式 查询订单物流轨迹
 */
function getOrderTracesByJson($shipperCode, $logisticCode){
	$requestData= "{\"OrderCode\":\"\",\"ShipperCode\":\"".$shipperCode."\",\"LogisticCode\":\"".$logisticCode."\"}";
	$datas = array(
        'EBusinessID' => EBusinessID,
        'RequestType' => '1002',
        'RequestData' => urlencode($requestData) ,
        'DataType' => '2',
    );
    $datas['DataSign'] = encrypt($requestData, AppKey);
	$result=sendPost(ReqURL, $datas);

	//根据公司业务处理返回的信息......

	return $result;
}

/**
 * XML方式 查询订单物流轨迹
 */
function getOrderTracesByXml(){
	$requestData= "<?xml version=\"1.0\" encoding=\"utf-8\" ?>".
						"<Content>".
						"<OrderCode></OrderCode>".
						"<ShipperCode>SF</ShipperCode>".
						"<LogisticCode>589707398027</LogisticCode>".
						"</Content>";

	$datas = array(
        'EBusinessID' => EBusinessID,
        'RequestType' => '1002',
        'RequestData' => urlencode($requestData) ,
        'DataType' => '1',
    );
    $datas['DataSign'] = encrypt($requestData, AppKey);
	$result=sendPost(ReqURL, $datas);

	//根据公司业务处理返回的信息......

	return $result;
}

/**
 *  post提交数据
 * @param  string $url 请求Url
 * @param  array $datas 提交的数据
 * @return url响应返回的html
 */
function sendPost($url, $datas) {
    $temps = array();
    foreach ($datas as $key => $value) {
        $temps[] = sprintf('%s=%s', $key, $value);
    }
    $post_data = implode('&', $temps);
    $url_info = parse_url($url);
    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
    $httpheader.= "Host:" . $url_info['host'] . "\r\n";
    $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
    $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
    $httpheader.= "Connection:close\r\n\r\n";
    $httpheader.= $post_data;
    $fd = fsockopen($url_info['host'], 80);
    fwrite($fd, $httpheader);
    $gets = "";
	$headerFlag = true;
	while (!feof($fd)) {
		if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
			break;
		}
	}
    while (!feof($fd)) {
		$gets.= fread($fd, 128);
    }
    fclose($fd);

    return $gets;
}

/**
 * 电商Sign签名生成
 * @param data 内容
 * @param appkey Appkey
 * @return DataSign签名
 */
function encrypt($data, $appkey) {
    return urlencode(base64_encode(md5($data.$appkey)));
}


/**
* 快递查询
*/
class W2Express
{
    protected static $list = [

        'SF' => '顺丰',
        'HTKY' => '百世快递',
        'ZTO' => '中通',
        'STO' => '申通',
        'YTO' => '圆通',
        'YD' => '韵达',
        'YZPY' => '邮政平邮',
        'EMS' => 'EMS',
        'HHTT' => '天天',
        'JD' => '京东',
        'QFKD' => '全峰',
        'GTO' => '国通',
        'UC' => '优速',
        'DBL' => '德邦',
        'FAST' => '快捷',
        'AMAZON' => '亚马逊',
        'ZJS' => '宅急送',
        'AJ' => '安捷快递',
        'AMAZON' => '亚马逊物流',
        'ANE' => '安能物流',
        'AXD' => '安信达快递',
        'AYCA' => '澳邮专线',
        'BDT' => '八达通',
        'BFDF' => '百福东方',
        'BHGJ' => '贝海国际',
        'BQXHM' => '北青小红帽',
        'BTWL' => '百世快运',
        'CCES' => 'CCES快递',
        'CG' => '程光',
        'CITY100' => '城市100',
        'CJKD' => '城际快递',
        'CNPEX' => 'CNPEX中邮快递',
        'COE' => 'COE东方快递',
        'CSCY' => '长沙创一',
        'CDSTKY' => '成都善途速运',
        'CTG' => '联合运通',
        'DBL' => '德邦',
        'DSWL' => 'D速物流',
        'DTWL' => '大田物流',
        'EWE' => 'EWE',
        'EMS' => 'EMS',
        'FAST' => '快捷速递',
        'FEDEX' => '(国内件）   FEDEX联邦',
        'FEDEX_GJ' => '(国际件）    FEDEX联邦',
        'FKD' => '飞康达',
        'FTD' => '富腾达',
        'GD' => '冠达',
        'GDEMS' => '广东邮政',
        'GSD' => '共速达',
        'GTO' => '国通快递',
        'GTONG' => '广通',
        'GTSD' => '高铁速递',
        'HFWL' => '汇丰物流',
        'HGLL' => '黑狗冷链',
        'HHTT' => '天天快递',
        'HLWL' => '恒路物流',
        'HOAU' => '天地华宇',
        'HOTSCM' => '鸿桥供应链',
        'HPTEX' => '海派通物流公司',
        'hq568' => '华强物流',
        'HQSY' => '环球速运',
        'HTKY' => '百世快递',
        'HXLWL' => '华夏龙物流',
        'HXWL' => '豪翔物流',
        'HYLSD' => '好来运快递',
        'JAD' => '捷安达',
        'JD' => '京东',
        'JGSD' => '京广速递',
        'JIUYE' => '九曳供应链',
        'JJKY' => '佳吉快运',
        'JLDT' => '嘉里物流',
        'JTKD' => '捷特快递',
        'JXD' => '急先达',
        'JYKD' => '晋越快递',
        'JYM' => '加运美',
        'JGWL' => '景光物流',
        'JYWL' => '佳怡物流',
        'KYSY' => '跨越速运',
        'LB' => '龙邦快递',
        'LHT' => '联昊通速递',
        'MB' => '民邦快递',
        'MHKD' => '民航快递',
        'MK' => '美快',
        'MLWL' => '明亮物流',
        'NF' => '南方',
        'NEDA' => '能达速递',
        'PADTF' => '平安达腾飞快递',
        'PANEX' => '泛捷快递',
        'PJ' => '品骏',
        'PCA' => 'Express   PCA',
        'QCKD' => '全晨快递',
        'QFKD' => '全峰快递',
        'QRT' => '全日通快递',
        'QXT' => '全信通',
        'RFEX' => '瑞丰速递',
        'RFD' => '如风达',
        'RFEX' => '瑞丰速递',
        'SAD' => '赛澳递',
        'SAWL' => '圣安物流',
        'SBWL' => '盛邦物流',
        'SDWL' => '上大物流',
        'SF' => '顺丰快递',
        'SFWL' => '盛丰物流',
        'SHWL' => '盛辉物流',
        'ST' => '速通物流',
        'STWL' => '速腾快递',
        'SUBIDA' => '速必达物流',
        'SDEZ' => '速递e站',
        'SURE' => '速尔快递',
        'HHTT' => '天天',
        'UAPEX' => '全一快递',
        'UEQ' => 'Express   UEQ',
        'UC' => '优速快递',
        'WJK' => '万家康',
        'WJWL' => '万家物流',
        'WXWL' => '万象物流',
        'XBWL' => '新邦物流',
        'XCWL' => '迅驰物流',
        'XFEX' => '信丰快递',
        'XYT' => '希优特',
        'XJ' => '新杰物流',
        'YADEX' => '源安达快递',
        'YCWL' => '远成物流',
        'YD' => '韵达快递',
        'YDH' => '义达国际物流',
        'YDT' => '易达通',
        'YFEX' => '越丰物流',
        'YFHEX' => '原飞航物流',
        'YFSD' => '亚风快递',
        'YTKD' => '运通快递',
        'YTO' => '圆通速递',
        'YXKD' => '亿翔快递',
        'YUNDX' => '运东西',
        'YZPY' => '/小包  邮政平邮',
        'ZENY' => '增益快递',
        'ZHQKD' => '汇强快递',
        'ZJS' => '宅急送',
        'ZTE' => '众通快递',
        'ZTKY' => '中铁快运',
        'ZTO' => '中通速递',
        'ZTWL' => '中铁物流',
        'ZYWL' => '中邮物流',
        'AAE' => 'AAE全球专递',
        'ACS' => 'ACS雅仕快递',
        'ADP' => 'Express Tracking  ADP',
        'ANGUILAYOU' => '安圭拉邮政',
        'AOMENYZ' => '澳门邮政',
        'APAC' => 'APAC',
        'ARAMEX' => 'Aramex',
        'AT' => '奥地利邮政',
        'AUSTRALIA' => 'Post Tracking   Australia',
        'BEL' => '比利时邮政',
        'BHT' => 'BHT快递',
        'BILUYOUZHE' => '秘鲁邮政',
        'BR' => '巴西邮政',
        'BUDANYOUZH' => '不丹邮政',
        'CA' => '加拿大邮政',
        'D4PX' => '递四方速递',
        'DHL' => 'DHL',
        'DHL_EN' => '(英文版)  DHL',
        'DHL_GLB' => 'DHL全球',
        'DHLGM' => 'Global Mail DHL',
        'DK' => '丹麦邮政',
        'DPD' => 'DPD',
        'DPEX' => 'DPEX',
        'EMSGJ' => 'EMS国际',
        'ESHIPPER' => 'EShipper',
        'GJEYB' => '国际e邮宝',
        'GJYZ' => '国际邮政包裹',
        'GLS' => 'GLS',
        'IADLSQDYZ' => '安的列斯群岛邮政',
        'IADLYYZ' => '澳大利亚邮政',
        'IAEBNYYZ' => '阿尔巴尼亚邮政',
        'IAEJLYYZ' => '阿尔及利亚邮政',
        'IAFHYZ' => '阿富汗邮政',
        'IAGLYZ' => '安哥拉邮政',
        'IAGTYZ' => '阿根廷邮政',
        'IAJYZ' => '埃及邮政',
        'IALBYZ' => '阿鲁巴邮政',
        'IALQDYZ' => '奥兰群岛邮政',
        'IALYYZ' => '阿联酋邮政',
        'IAMYZ' => '阿曼邮政',
        'IASBJYZ' => '阿塞拜疆邮政',
        'IASEBYYZ' => '埃塞俄比亚邮政',
        'IASNYYZ' => '爱沙尼亚邮政',
        'IASSDYZ' => '阿森松岛邮政',
        'IBCWNYZ' => '博茨瓦纳邮政',
        'IBDLGYZ' => '波多黎各邮政',
        'IBDYZ' => '冰岛邮政',
        'IBELSYZ' => '白俄罗斯邮政',
        'IBHYZ' => '波黑邮政',
        'IBJLYYZ' => '保加利亚邮政',
        'IBJSTYZ' => '巴基斯坦邮政',
        'IBLNYZ' => '黎巴嫩邮政',
        'IBLSD' => '便利速递',
        'IBLWYYZ' => '玻利维亚邮政',
        'IBLYZ' => '巴林邮政',
        'IBMDYZ' => '百慕达邮政',
        'IBOLYZ' => '波兰邮政',
        'IBTD' => '宝通达',
        'IBYB' => '贝邮宝',
        'ICKY' => '出口易',
        'IDFWL' => '达方物流',
        'IDGYZ' => '德国邮政',
        'IE' => '爱尔兰邮政',
        'IEGDEYZ' => '厄瓜多尔邮政',
        'IELSYZ' => '俄罗斯邮政',
        'IELTLYYZ' => '厄立特里亚邮政',
        'IFTWL' => '飞特物流',
        'IGDLPDEMS' => '瓜德罗普岛EMS',
        'IGDLPDYZ' => '瓜德罗普岛邮政',
        'IGJESD' => '俄速递',
        'IGLBYYZ' => '哥伦比亚邮政',
        'IGLLYZ' => '格陵兰邮政',
        'IGSDLJYZ' => '哥斯达黎加邮政',
        'IHGYZ' => '韩国邮政',
        'IHHWL' => '华翰物流',
        'IHLY' => '互联易',
        'IHSKSTYZ' => '哈萨克斯坦邮政',
        'IHSYZ' => '黑山邮政',
        'IJBBWYZ' => '津巴布韦邮政',
        'IJEJSSTYZ' => '吉尔吉斯斯坦邮政',
        'IJKYZ' => '捷克邮政',
        'IJNYZ' => '加纳邮政',
        'IJPZYZ' => '柬埔寨邮政',
        'IKNDYYZ' => '克罗地亚邮政',
        'IKNYYZ' => '肯尼亚邮政',
        'IKTDWEMS' => '科特迪瓦EMS',
        'IKTDWYZ' => '科特迪瓦邮政',
        'IKTEYZ' => '卡塔尔邮政',
        'ILBYYZ' => '利比亚邮政',
        'ILKKD' => '林克快递',
        'ILMNYYZ' => '罗马尼亚邮政',
        'ILSBYZ' => '卢森堡邮政',
        'ILTWYYZ' => '拉脱维亚邮政',
        'ILTWYZ' => '立陶宛邮政',
        'ILZDSDYZ' => '列支敦士登邮政',
        'IMEDFYZ' => '马尔代夫邮政',
        'IMEDWYZ' => '摩尔多瓦邮政',
        'IMETYZ' => '马耳他邮政',
        'IMJLGEMS' => '孟加拉国EMS',
        'IMLGYZ' => '摩洛哥邮政',
        'IMLQSYZ' => '毛里求斯邮政',
        'IMLXYEMS' => '马来西亚EMS',
        'IMLXYYZ' => '马来西亚邮政',
        'IMQDYZ' => '马其顿邮政',
        'IMTNKEMS' => '马提尼克EMS',
        'IMTNKYZ' => '马提尼克邮政',
        'IMXGYZ' => '墨西哥邮政',
        'INFYZ' => '南非邮政',
        'INRLYYZ' => '尼日利亚邮政',
        'INWYZ' => '挪威邮政',
        'IPTYYZ' => '葡萄牙邮政',
        'IQQKD' => '全球快递',
        'IQTWL' => '全通物流',
        'ISDYZ' => '苏丹邮政',
        'ISEWDYZ' => '萨尔瓦多邮政',
        'ISEWYYZ' => '塞尔维亚邮政',
        'ISLFKYZ' => '斯洛伐克邮政',
        'ISLWNYYZ' => '斯洛文尼亚邮政',
        'ISNJEYZ' => '塞内加尔邮政',
        'ISPLSYZ' => '塞浦路斯邮政',
        'ISTALBYZ' => '沙特阿拉伯邮政',
        'ITEQYZ' => '土耳其邮政',
        'ITGYZ' => '泰国邮政',
        'ITLNDHDBGE' => '特立尼达和多巴哥EMS',
        'ITNSYZ' => '突尼斯邮政',
        'ITSNYYZ' => '坦桑尼亚邮政',
        'IWDMLYZ' => '危地马拉邮政',
        'IWGDYZ' => '乌干达邮政',
        'IWKLEMS' => '乌克兰EMS',
        'IWKLYZ' => '乌克兰邮政',
        'IWLGYZ' => '乌拉圭邮政',
        'IWLYZ' => '文莱邮政',
        'IWZBKSTEMS' => '乌兹别克斯坦EMS',
        'IWZBKSTYZ' => '乌兹别克斯坦邮政',
        'IXBYYZ' => '西班牙邮政',
        'IXFLWL' => '小飞龙物流',
        'IXGLDNYYZ' => '新喀里多尼亚邮政',
        'IXJPEMS' => '新加坡EMS',
        'IXJPYZ' => '新加坡邮政',
        'IXLYYZ' => '叙利亚邮政',
        'IXLYZ' => '希腊邮政',
        'IXPSJ' => '夏浦世纪',
        'IXPWL' => '夏浦物流',
        'IXXLYZ' => '新西兰邮政',
        'IXYLYZ' => '匈牙利邮政',
        'IYDLYZ' => '意大利邮政',
        'IYDNXYYZ' => '印度尼西亚邮政',
        'IYDYZ' => '印度邮政',
        'IYGYZ' => '英国邮政',
        'IYLYZ' => '伊朗邮政',
        'IYMNYYZ' => '亚美尼亚邮政',
        'IYMYZ' => '也门邮政',
        'IYNYZ' => '越南邮政',
        'IYSLYZ' => '以色列邮政',
        'IYTG' => '易通关',
        'IYWWL' => '燕文物流',
        'IZBLTYZ' => '直布罗陀邮政',
        'IZLYZ' => '智利邮政',
        'JP' => '日本邮政',
        'NL' => '荷兰邮政',
        'ONTRAC' => 'ONTRAC',
        'QQYZ' => '全球邮政',
        'RDSE' => '瑞典邮政',
        'SWCH' => '瑞士邮政',
        'TAIWANYZ' => '台湾邮政',
        'TNT' => 'TNT快递',
        'UPS' => 'UPS',
        'USPS' => 'USPS美国邮政',
        'YAMA' => '(Yamato) 日本大和运输',
        'YODEL' => 'YODEL',
        'YUEDANYOUZ' => '约旦邮政',
        'AOL' => 'AOL（澳通）',
        'BCWELT' => 'BCWELT',
        'BN' => '笨鸟国际',
        'COE' => 'COE快递',
        'UEX' => 'UEX',
        'ZY_AG' => '爱购转运',
        'ZY_AOZ' => '爱欧洲',
        'ZY_AUSE' => '澳世速递',
        'ZY_AXO' => 'AXO',
        'ZY_AZY' => '澳转运',
        'ZY_BDA' => '八达网',
        'ZY_BEE' => '蜜蜂速递',
        'ZY_BH' => '贝海速递',
        'ZY_BL' => '百利快递',
        'ZY_BM' => '斑马物流',
        'ZY_BOZ' => '败欧洲',
        'ZY_BT' => '百通物流',
        'ZY_BYECO' => '贝易购',
        'ZY_CM' => '策马转运',
        'ZY_CTM' => '赤兔马转运',
        'ZY_CUL' => 'CUL中美速递',
        'ZY_DGHT' => '德国海淘之家',
        'ZY_DYW' => '德运网',
        'ZY_EFS' => 'POST   EFS',
        'ZY_ESONG' => '宜送转运',
        'ZY_ETD' => 'ETD',
        'ZY_FD' => '飞碟快递',
        'ZY_FG' => '飞鸽快递',
        'ZY_FLSD' => '风雷速递',
        'ZY_FX' => '风行快递',
        'ZY_FXSD' => '风行速递',
        'ZY_FY' => '飞洋快递',
        'ZY_HC' => '皓晨快递',
        'ZY_HCYD' => '皓晨优递',
        'ZY_HDB' => '海带宝',
        'ZY_HFMZ' => '汇丰美中速递',
        'ZY_HJSD' => '豪杰速递',
        'ZY_HTAO' => '360hitao转运',
        'ZY_HTCUN' => '海淘村',
        'ZY_HTKE' => '365海淘客',
        'ZY_HTONG' => '华通快运',
        'ZY_HXKD' => '海星桥快递',
        'ZY_HXSY' => '华兴速运',
        'ZY_HYSD' => '海悦速递',
        'ZY_IHERB' => 'LogisticsY',
        'ZY_JA' => '君安快递',
        'ZY_JD' => '时代转运',
        'ZY_JDKD' => '骏达快递',
        'ZY_JDZY' => '骏达转运',
        'ZY_JH' => '久禾快递',
        'ZY_JHT' => '金海淘',
        'ZY_LBZY' => '联邦转运FedRoad',
        'ZY_LPZ' => '领跑者快递',
        'ZY_LX' => '龙象快递',
        'ZY_LZWL' => '量子物流',
        'ZY_MBZY' => '明邦转运',
        'ZY_MGZY' => '美国转运',
        'ZY_MJ' => '美嘉快递',
        'ZY_MST' => '美速通',
        'ZY_MXZY' => '美西转运',
        'ZY_MZ' => '美中快递    168',
        'ZY_OEJ' => '欧e捷',
        'ZY_OZF' => '欧洲疯',
        'ZY_OZGO' => '欧洲GO',
        'ZY_QMT' => '全美通',
        'ZY_QQEX' => '-EX   QQ',
        'ZY_RDGJ' => '润东国际快线',
        'ZY_RT' => '瑞天快递',
        'ZY_RTSD' => '瑞天速递',
        'ZY_SCS' => 'SCS国际物流',
        'ZY_SDKD' => '速达快递',
        'ZY_SFZY' => '四方转运',
        'ZY_SOHO' => 'SOHO苏豪国际',
        'ZY_SONIC' => '-Ex速递    Sonic',
        'ZY_ST' => '上腾快递',
        'ZY_TCM' => '通诚美中快递',
        'ZY_TJ' => '天际快递',
        'ZY_TM' => '天马转运',
        'ZY_TN' => '滕牛快递',
        'ZY_TPAK' => 'TrakPak',
        'ZY_TPY' => '太平洋快递',
        'ZY_TSZ' => '唐三藏转运',
        'ZY_TTHT' => '天天海淘',
        'ZY_TWC' => 'TWC转运世界',
        'ZY_TX' => '同心快递',
        'ZY_TY' => '天翼快递',
        'ZY_TZH' => '同舟快递',
        'ZY_UCS' => 'UCS合众快递',
        'ZY_WDCS' => '文达国际DCS',
        'ZY_XC' => '星辰快递',
        'ZY_XDKD' => '迅达快递',
        'ZY_XDSY' => '信达速运',
        'ZY_XF' => '先锋快递',
        'ZY_XGX' => '新干线快递',
        'ZY_XIYJ' => '西邮寄',
        'ZY_XJ' => '信捷转运',
        'ZY_YGKD' => '优购快递',
        'ZY_YJSD' => '(UCS) 友家速递',
        'ZY_YPW' => '云畔网',
        'ZY_YQ' => '云骑快递',
        'ZY_YQWL' => '一柒物流',
        'ZY_YSSD' => '优晟速递',
        'ZY_YSW' => '易送网',
        'ZY_YTUSA' => '运淘美国',
        'ZY_ZCSD' => '至诚速递',
    ];
    /**
     * [query description]
     * @param  string $code   [description]
     * @param  string $number [description]
     * @return [type]         [description]
     */
    public static function query($code = '', $number = '')
    {
        $res = array();
        if(!empty($code) && !empty($number))
        {
            // $logisticResult = getOrderTracesByJson('ZTO', '448722308550');
            $logisticResult = getOrderTracesByJson($code, $number);
            $res            = Utility::jsonDecode($logisticResult);
            if(!empty($res['ShipperCode']))
            {
                // D( static::$list );
                $res['ShipperLabel'] = isset(static::$list[$res['ShipperCode']]) ? static::$list[$res['ShipperCode']] : '';
            }
        }
        return $res;
    }

}