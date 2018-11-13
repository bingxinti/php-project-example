<?php
/**
 * 地图处理函数库文件
 * @package W2
 * @author axing
 * @since 1.0
 * @version 1.0
 */

// SELECT
//     *,
//     (2 * 6378.137 * ASIN(SQRT(POW(SIN(PI() * (118.743244 - lng) / 360),
//     2) + COS(PI() * 32.003515 / 180) * COS(lat * PI() / 180) * POW(SIN(PI() * (32.003515 - lat) / 360),
//     2)))) AS juli
// FROM
//     `userSite`
// ORDER BY juli ASC
// LIMIT 0 , 20

// INSERT INTO `userSite` (`location`,  `lng`, `lat` )
// VALUES
// ('建邺区','118.743244','32.003515'),
// ('雨花台','118.782625','32.003515'),
// ('鼓楼','118.776876','32.097777'),
// ('浦口','118.616762','32.066204'),
// ('六合','118.828187','32.369483'),
// ('马鞍山','118.508534','31.675901'),
// ('无锡','120.366661','31.479016'),
// ('上海','121.66827','31.151263'),
// ('徐州','117.193128','34.219563');

class W2Map {
	// http://www.mapanet.eu/EN/resources/Script-Distance.htm

	const EARTH_RADIUS = 6378.137;// 地球半径

	/**
	 * [distanceOrder 附近的人]
	 * @param  string $lng [description]
	 * @param  string $lat [description]
	 * @return [type]      [description]
	 */
	public static function distanceOrder($lng = '',$lat = '')
	{
		$r = W2Map::EARTH_RADIUS;
		$pField = '';
		$pField = " (2 * {$r} * ASIN(SQRT(POW(SIN(PI() * ({$lng} - lng) / 360),
	    2) + COS(PI() * {$lat} / 180) * COS(lat * PI() / 180) * POW(SIN(PI() * ({$lat} - lat) / 360),
	    2)))) ";
	    return $pField;
	}

	/**
	 *
	 * 根据经纬度计算距离
	 * @param float $lng1　经度1
	 * @param float $lat1　纬度2
	 * @param float $lng2　经度1
	 * @param float $lat2　纬度2
	 * @return float      单位(公里 KM)
	 */
	public static function getdistance($lng1,$lat1,$lng2,$lat2) {
	    $r = W2Map::EARTH_RADIUS;
	    $dlat = deg2rad($lat2 - $lat1);
	    $dlng = deg2rad($lng2 - $lng1);
	    $a = pow(sin($dlat / 2), 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * pow(sin($dlng / 2), 2);
	    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
	    return round($r * $c,2);
	}

	 /**
	 *计算某个经纬度的周围某段距离的正方形的四个点
	 *
	 *@param lng float 经度
	 *@param lat float 纬度
	 *@param distance float 该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米
	 *@return array 正方形的四个点的经纬度坐标
	 */
	 public static function returnSquarePoint($lng, $lat,$distance = 0.5){

	    $dlng =  2 * asin(sin($distance / (2 * W2Map::EARTH_RADIUS)) / cos(deg2rad($lat)));
	    $dlng = rad2deg($dlng);

	    $dlat = $distance/W2Map::EARTH_RADIUS;
	    $dlat = rad2deg($dlat);

	    return array(
	                'left-top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),
	                'right-top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),
	                'left-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),
	                'right-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)
	                );
	 }

	 /**
	 *计算某个经纬度的周围某段距离的范围SQL语句
	 *
	 *@param lng float 经度
	 *@param lat float 纬度
	 *@param distance float 该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米
	 *@return array $p_where
	 */
	 public static function returnWhereForDBToolAroundPoint($lng, $lat,$distance = 0.5)
	 {
		$squares = W2Map::returnSquarePoint($lng, $lat,$distance);
		$p_where = array();
		$p_where['lat > \'%s\''] = $squares['right-bottom']['lat'];
		$p_where['lat < \'%s\''] = $squares['left-top']['lat'];
		$p_where['lng > \'%s\''] = $squares['left-top']['lng'];
		$p_where['lng < \'%s\''] = $squares['right-bottom']['lng'];
		return $p_where;
	 }


	public static function renderReverse($lat = '', $lng= '')
	{
		$key  = W2BAIDU_MAP_KEY;
		// http://api.map.baidu.com/geocoder/v2/?callback=renderReverse&location=32.0572355,118.77807441&output=json&pois=1&ak=72fefdae842ab9acff62d9669b659350
		$url = "http://api.map.baidu.com/geocoder/v2/?callback=renderReverse&location={$lat},{$lng}&output=json&pois=1&ak={$key}";
		$data = W2Web::loadStringByUrl($url);

		if(strpos($data,'&&')!==false)
		{
			$data = stristr($data, '(');
			$data = trim($data,'(');
			$data = trim($data,')');
		}
		// $data = file_get_contents($url);
		return $data;
	}


}
