<?php

$dir = dirname(__FILE__);

function curl( $d, $options=array() )
{
    $ch = curl_init();
    $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_ENCODING , "gzip");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    if (is_array($d) && !empty($d['post'])) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $d['post']);
    }

    if (!empty($options)) {
        curl_setopt_array($ch, $options);
    }
    return $ch;
}

function process_http_response( $c, $id )
{
    global $mh;

    $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
	echo "$id - $status\n";
    if ($status == 200)
    {
        $content = curl_multi_getcontent($c);
		echo "$content\n";
        $json = json_decode($content, true);
        process_data($json, $id);
    }
    curl_multi_remove_handle($mh, $c);
    curl_close($c);
}

$result = array();

function process_data($json, $id)
{
    global $result;

    if (!empty($json['error']))
    {
        $result[$id] = $json['error'];
    }
    else
    {
        $result[$id] = isset($json['features']) ? $json['features'] : null;
    }
}

function build_url($params, $services = 'Statistik_Perkembangan_COVID19_Indonesia') {
    return "https://services5.arcgis.com/VS6HdKS0VfIhv8Ct/arcgis/rest/services/$services/FeatureServer/0/query?" . http_build_query($params);
}

//Tanggal<timestamp '2020-04-01 17:00:00'
$time = time();
$hournow = date('G', $time);
if ($hournow > 17) {
    $now = date('Y-m-d 17:00:00', strtotime('+1 day'));
    $past2days = date('Y-m-d', $time - 86400);
} else {
    $now = date('Y-m-d 17:00:00', $time);
    $past2days = date('Y-m-d', $time - 86400);
}

$today = date('Y-m-d', $time);
$yesterday = date('Y-m-d', $time - 86400);

$params = array(
    'f' => 'json',
    'where' => "Tanggal<timestamp '$now'",
    'returnGeometry' => 'false',
    'spatialRel' => 'esriSpatialRelIntersects',
    'outFields' => '*',
    'orderByFields' => 'Tanggal asc',
    'resultOffset' => '0',
    'resultRecordCount' => '2000',
    'cacheHint' => 'true',
);
//https://services5.arcgis.com/VS6HdKS0VfIhv8Ct/arcgis/rest/services/COVID19_Indonesia_per_Provinsi/FeatureServer/0/query

$params2 = array(
    'f' => 'json',
    'where' => "(Kasus_Posi <> 0) AND (Provinsi <> 'Indonesia')",
    'returnGeometry' => 'false',
    'spatialRel' => 'esriSpatialRelIntersects',
    'outFields' => '*',
    'orderByFields' => 'Kasus_Posi DESC',
    'outSR' => '102100',
    'resultOffset' => '0',
    'resultRecordCount' => '8000',
    'cacheHint' => 'true',
);

$params3 = array(
    'f' => 'json',
    'where' => '1=1',
    'returnGeometry' => 'false',
    'spatialRel' => 'esriSpatialRelIntersects',
    'outFields' => '*',
    'outStatistics' => '[{"statisticType":"sum","onStatisticField":"Jumlah_Kasus_Baru_per_Hari","outStatisticFieldName":"value"}]',
    'cacheHint' => 'true',
);

$params4 = array(
    'f' => 'json',
    'where' => "(Tanggal>=timestamp '$yesterday 17:00:00' AND Tanggal<=timestamp '$today 16:59:59' OR Tanggal>timestamp '$past2days 16:59:59')",
    'returnGeometry' => 'false',
    'spatialRel' => 'esriSpatialRelIntersects',
    'outFields' => '*',
    'outStatistics' => '[{"statisticType":"sum","onStatisticField":"Jumlah_Kasus_Baru_per_Hari","outStatisticFieldName":"value"}]',
    'cacheHint' => 'true',
);

$params4 = array(
    'f' => 'json',
    'where' => "(Tanggal>=timestamp '$yesterday 17:00:00' AND Tanggal<=timestamp '$today 16:59:59' OR Tanggal>timestamp '$past2days 16:59:59')",
    'returnGeometry' => 'false',
    'spatialRel' => 'esriSpatialRelIntersects',
    'outFields' => '*',
    'outStatistics' => '[{"statisticType":"sum","onStatisticField":"Jumlah_Kasus_Baru_per_Hari","outStatisticFieldName":"value"}]',
    'cacheHint' => 'true',
);

$params5 = array(
    'f' => 'json',
    'where' => "(Tanggal>=timestamp '$yesterday 17:00:00' AND Tanggal<=timestamp '$today 16:59:59' OR Tanggal>timestamp '$past2days 16:59:59')",
    'returnGeometry' => 'false',
    'spatialRel' => 'esriSpatialRelIntersects',
    'outFields' => '*',
    'outStatistics' => '[{"statisticType":"sum","onStatisticField":"Jumlah_Pasien_Meninggal","outStatisticFieldName":"value"}]',
    'cacheHint' => 'true',
);

$params6 = array(
    'f' => 'json',
    'where' => "(Tanggal>=timestamp '$yesterday 17:00:00' AND Tanggal<=timestamp '$today 16:59:59' OR Tanggal>timestamp '$past2days 16:59:59')",
    'returnGeometry' => 'false',
    'spatialRel' => 'esriSpatialRelIntersects',
    'outFields' => '*',
    'outStatistics' => '[{"statisticType":"sum","onStatisticField":"Jumlah_Pasien_Sembuh","outStatisticFieldName":"value"}]',
    'cacheHint' => 'true',
);

$params7 = array(
    'f' => 'json',
    'where' => "(Tanggal>=timestamp '$yesterday 17:00:00' AND Tanggal<=timestamp '$today 16:59:59' OR Tanggal>timestamp '$past2days 16:59:59')",
    'returnGeometry' => 'false',
    'spatialRel' => 'esriSpatialRelIntersects',
    'outFields' => '*',
    'outStatistics' => '[{"statisticType":"sum","onStatisticField":"Jumlah_pasien_dalam_perawatan","outStatisticFieldName":"value"}]',
    'cacheHint' => 'true',
);

$urls = array(
    'date' => build_url($params),
    'propinsi' => build_url($params2, 'COVID19_Indonesia_per_Provinsi'),
    'total' => build_url($params3),
    'penambahan' => build_url($params4),
    'meninggal' => build_url($params5),
    'sembuh' => build_url($params6),
    'pdp' => build_url($params7),
);

if (isset($argv[1]) && $argv[1] === 'reload')
{
    $mh = curl_multi_init();
    $handlers=array();
    foreach($urls as $i=>$url)
    {
		echo "$i = $url\n";
        $handlers[$i] = curl( $url );
        curl_multi_add_handle($mh, $handlers[$i]);
    }

    $active=null;
    $lastactive=null;
    $lasttime=null;
    do {
        $mrc = curl_multi_exec($mh, $active);
        if (curl_multi_select($mh) == -1) usleep(10000);
        if ( $lastactive == $active && ( time()-$lasttime ) > 60 ) break;

        $lastactive = $active;
        $lasttime = time();

        if (false !== $info = curl_multi_info_read($mh))
        {
            $request_id = false;
            foreach($handlers as $id => $ch2)
            {
                if ( $ch2 == $info["handle"] )
                {
                    $request_id = $id;
                    break;
                }
            }

            if ($request_id)
            {
                if ($info["result"] == CURLE_OK)
                {
                    process_http_response($info["handle"], $request_id);
                }
                unset($handlers[$request_id]);
            }
        }
        else
        {
            usleep(10000);
        }

    } while ($active || $mrc == CURLM_CALL_MULTI_PERFORM);

    foreach($handlers as $id => $c)
    {
        process_http_response($c, $id);
    }

    curl_multi_close($mh);

    $oldata = file_get_contents('covid.json');
    $old = json_decode($oldata, true);
    $is_error = false;

    foreach ($urls as $id => $url)
    {
        $res = isset($result[$id]) ? $result[$id] : false;
        if (!$res)
        {
            $is_error = true;
        }

        if ($res && isset($res['code']))
        {
            $is_error = true;
        }
    }

    if (!$is_error)
    {
        file_put_contents($dir . '/covid.json', json_encode($result));
    }
    exit;
}

include 'cms_chart.php';

$mtime = filemtime($dir . '/covid.json');
$content = file_get_contents($dir . '/covid.json');
$result = json_decode($content, true);


?><!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Covid19 Indonesia 2020">
<meta name="author" content="@ferdhie">
<title>COVID19 INDONESIA</title>
<style>
html,body{ font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;}
h1 { font-size: 26px; font-weight:400; text-align: center; }
body { font-size:20px; text-align: center; color: #333; }
article { display: block; text-align: left; width: 650px; margin: 0 auto; min-width:300px; max-width:95% }
a { color: #dc8100; text-decoration: none; }
a:hover { color: red; }
ul,li { list-style: none; margin: 0; padding: 0;}
li {display: inline-block; text-align: center; margin-bottom: 1em; flex: 0 0 25%; max-width: 25%; }
ul {display: flex; flex-direction: row; flex-wrap: wrap; justify-content: space-between; align-items: stretch;}
li h2 {font-size:14px; font-weight: normal; line-height: 1; margin: 0;}
li p {margin: 0; font-size: 32px; line-height: 1; padding:0; }
small {margin: 0; font-size: 14px;}
td,th {font-size: 14px; padding: 10px; text-align: center; }
td {text-align: right;}
td:first-child {text-align: left;}
thead tr { background-color: #fff; border-bottom: 1px solid #ddd;}
table {
    background-color: #fff;
    border-collapse: collapse;
    width: 100%;
}
@media (max-width: 480px) {
    li { flex: 0 0 50%; max-width: 50%; }
}
.bar {width: 100%; height:3px; background-color: #f0f0f0; margin: 10px auto 0;}
.bar-content {height:3px; background-color: #33C3F0;}
footer p { font-size:14px; font-style: italic; }
footer { border-top: 1px solid #ddd; margin-top: 20px; }
</style>
<?php cms_chart_css (1, ''); ?>
</head>
<body>
<article>
    <h1>COVID 19 INDONESIA</h1>
    <ul>
        <li>
            <div class="chart-konfirmasi">
                <?php
                $data = [];
                $init = ['title' => '', 'xTitle' => '', 'yTitle' => ''];
                foreach($result['date'] as $date)
                {
                    $data[] = $date['attributes']['Jumlah_Kasus_Baru_per_Hari'];
                }
                $init['chart'] = 'line';
                $init['xSkip'] = 1;
                $init['ySkip'] = 1;
                cms_chart($data, $init);
                ?>
            </div>
            <h2>TERKONFIRMASI</h2>
            <p><?php echo $result['total'][0]['attributes']['value']; ?></p>
            <small>+<?php echo $result['penambahan'][0]['attributes']['value']; ?></small>
        </li>
        <li>
            <div class="chart-dirawat">
                <?php
                $data = [];
                $init = ['title' => '', 'xTitle' => '', 'yTitle' => ''];
                foreach($result['date'] as $date)
                {
                    $data[] = $date['attributes']['Jumlah_Kasus_Dirawat_per_Hari'];
                }
                $init['chart'] = 'line';
                $init['xSkip'] = 1;
                $init['ySkip'] = 1;
                $init['color'] = 'f0ad4e';
                cms_chart($data, $init);
                ?>
            </div>
            <h2>PERAWATAN</h2>
            <p><?php echo $result['pdp'][0]['attributes']['value']; ?></p>
        </li>
        <li>
            <div class="chart-sembuh">
                <?php
                $data = [];
                $init = ['title' => '', 'xTitle' => '', 'yTitle' => ''];
                foreach($result['date'] as $date)
                {
                    $data[] = $date['attributes']['Jumlah_Kasus_Sembuh_per_Hari'];
                }
                $init['chart'] = 'line';
                $init['xSkip'] = 1;
                $init['ySkip'] = 1;
                $init['color'] = '5cb85c';
                cms_chart($data, $init);
                ?>
            </div>
            <h2>SEMBUH</h2>
            <p><?php echo $result['sembuh'][0]['attributes']['value']; ?></p>
        </li>
        <li>
            <div class="chart-meninggal">
                <?php
                $data = [];
                $init = ['title' => '', 'xTitle' => '', 'yTitle' => ''];
                foreach($result['date'] as $date)
                {
                    $data[] = $date['attributes']['Jumlah_Kasus_Meninggal_per_Hari'];
                }
                $init['chart'] = 'line';
                $init['xSkip'] = 1;
                $init['ySkip'] = 1;
                $init['color'] = 'ce1797';
                cms_chart($data, $init);
                ?>
            </div>
            <h2>MENINGGAL</h2>
            <p><?php echo $result['meninggal'][0]['attributes']['value']; ?></p>
            <small><?php 
				
				$meninggal = $result['meninggal'][0]['attributes']['value'];
				$total = $result['total'][0]['attributes']['value'];
				$percent = number_format(round($meninggal * 100 / $total, 2), 2);
				echo "$percent%";
				?></small>
        </li>
    </ul>

    <table>
        <thead>
        <tr>
            <th style="width: 64%">Propinsi</th>
            <th style="width: 12%">Positif</th>
            <th style="width: 12%">Sembuh</th>
            <th style="width: 12%">Meninggal</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $provinsi = $result['propinsi'];

        $total = 0;

        foreach($provinsi as $i => $prop) {
            $provinsi[$i]['total'] = $prop['attributes']['Kasus_Posi'] + $prop['attributes']['Kasus_Semb'] + $prop['attributes']['Kasus_Meni'];
            $total+= $provinsi[$i]['total'];
        }

        $totpercent = 0;

        foreach($provinsi as $i => $prop): ?>
            <tr>
                <td>
                    <?php echo $prop['attributes']['Provinsi']; ?>
                    <?php

                    if ($i == count($provinsi)-1) {
                        $percent = 100 - $totpercent;
                    } else {
                        $current = $prop['total'];
                        $percent = $total > 0 ? (($current * 100) / $total) : 0;
                        $totpercent += $percent;
                    }
                    ?>

                    <div class="bar">
                        <div class="bar-content" style="width: <?php echo $percent; ?>%"></div>
                    </div>

                </td>
                <td><?php echo $prop['attributes']['Kasus_Posi']; ?></td>
                <td><?php echo $prop['attributes']['Kasus_Semb']; ?></td>
                <td><?php echo $prop['attributes']['Kasus_Meni']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <footer>
        <p>
            <a href="https://www.covid19.go.id/">COVID.GO.ID</a> @<?php echo date('d F y H:i', $mtime); ?>
        </p>
    </footer>
</article>
<script>
	function update() {
		var xhr = new XMLHttpRequest();
		xhr.timeout = 10000;
		xhr.open('GET', location.pathname + '?_' + Math.random());
		xhr.onload = function () {
		  if (xhr.readyState === 4 && xhr.status === 200) {
			  var article = xhr.responseText.match(/<article>.*?<\/article>/sm);
			  var el = document.createElement('div')
			  el.innerHTML = article;
			  var articleBody = document.querySelector('article');
			  articleBody.parentNode.replaceChild( el.firstChild, articleBody );
			  console.log('updated')
		  }
		}
		xhr.send();
	}
	setInterval(update, 60000);	
</script>
</body>
</html>
