<?php
/**
 * Name: WHMCS Textlocal Usage Widget
 * Description: This widget provides you with your Textlocal Usage on your WHMCS admin dashboard.
 * Version 1.0
 * Created by Mothersoft Technologies
 * Website: https://www.mothersoft.in/
 */
add_hook('AdminHomeWidgets', 1, function() {
    return new TextLocalWidget();
});

/**
 * Hello World Widget.
 */
class TextLocalWidget extends \WHMCS\Module\AbstractWidget
{
    protected $title = 'Textlocal Usage';
    protected $description = '';
    protected $weight = 150;
    protected $columns = 1;
    protected $cache = false;
    protected $cacheExpiry = 120;

    public function getData()
    {
        $apiKey = urlencode('enter your textlocal api key');
 
	// Prepare data for POST request
	$data = array('apikey' => $apiKey);
 
	// Send the POST request with cURL
	$ch = curl_init('https://api.textlocal.in/balance/');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	$response = json_decode($response);
	curl_close($ch);
		
 	$chn = curl_init('https://api.textlocal.in/get_history_api/');
	curl_setopt($chn, CURLOPT_POST, true);
	curl_setopt($chn, CURLOPT_POSTFIELDS, $data);
	curl_setopt($chn, CURLOPT_RETURNTRANSFER, true);
	$history = curl_exec($chn);
	$history = json_decode($history);
	curl_close($chn);
	//logActivity($history->total, 0);
		
		$dataArray = array(
            smsbalance => $response->balance->sms
			, msghistory => $history->total
        );
        
        return $dataArray;
    }
	
    public function generateOutput($data)
    {
		//logActivity($data['requests'], 0);
        return <<<EOF
			<div class="widget-content widget-billing">
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-6 bordered-right">
							<div class="item">
								<div class="data color-green" >{$data['smsbalance']}</div>
								<div class="note">SMS Balance</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="item">
								<div class="data color-pink">{$data['msghistory']}</div>
								<div class="note">Message History</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		EOF;
    }
}
