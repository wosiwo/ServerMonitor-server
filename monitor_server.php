<?php
define('DEBUG', 'on');
define("WEBPATH", realpath(__DIR__.'/'));
require __DIR__ . '/libs/lib_config.php';
//require __DIR__'/phar://swoole.phar';
Swoole\Config::$debug = false;

class EchoServer extends Swoole\Protocol\Base
{
    function onReceive($server,$client_id, $from_id, $data)
    {

    	// $data = json_decode($data,true);
    	$data = $this->get_used_status();
    	//'{"cpu":{"total":100,"used":"0.5","percent":"0.05"},"mem":{"total":"502260","used":"486744","percent":"0.969"},"hd":{"total":"20G","used":"14G","percent":"0.72"}}';
        $data = json_encode($data);
        $this->server->send($client_id, $data);
    }

    //通过执行系统命令来获取服务器信息
    function get_used_status()
	{
		$fp = popen('top -b -n 1 | grep -E "^(Cpu|Mem)"',"r");//获取某一时刻系统cpu和内存使用情况
		//top | grep -E "^(Cpu|Mem)"
		$rs = "";
		while(!feof($fp))
		{
			$rs .= fread($fp,1024);
		}
		pclose($fp);
		$sys_info = explode("\n",$rs);
		// print_r($sys_info);
		$cpu_info = explode("  ",$sys_info[0]);
		$mem_info = explode(" ",$sys_info[1]);
		//print_r($cpu_info);
		// print_r($mem_info);
		$cpu_usage = substr($cpu_info[1],0,3)/100;
		//var_dump($cpu_usage);

		$mem_total = trim($mem_info[4],'k');
		$mem_used = trim($mem_info[8],'k');
		$mem_usage = round(intval($mem_used)/intval($mem_total),1);
		//var_dump($mem_usage);

		$fp = popen("df -lh","r");
		$rs = fread($fp,1024);
		// pclose($fp);
		$hd_info = explode("\n",$rs);
		// print_r($hd_info);
		$hd = explode(" ",$hd_info[1]);
		//print_r($hd);
		$hd_total = $hd[9];
		$hd_usage = $hd[12];
		$hd_percent = substr($hd[16],0,3)/100;
			

		return array(
			'cpu'=>array(
					'total'  =>100,
					'used'	 =>$cpu_usage,
					'percent'=>$cpu_usage,
			),
			'mem'=>array(
					'total'  =>$mem_total,
					'used'	 =>$mem_used,
					'percent'=>$mem_usage,
			),
			'hd'=>array(
					'total'  =>$hd_total,
					'used'	 =>$hd_usage,
					'percent'=>$hd_percent,
			),
		);
	}
}

$AppSvr = new EchoServer();
$server = Swoole\Network\Server::autoCreate('0.0.0.0', 9505);
$server->setProtocol($AppSvr);
$server->run(array('worker_num' => 1));
