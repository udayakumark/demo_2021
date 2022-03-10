<?php 

// Payment type
function PaymentType($status)
{
    if($status==1){
    	return "RazorPay";
    }else if($status==2){
    	return "Cash";
    }else{
    	return "N/A";
    }
}

// Order status
function OrderStatus($status)
{
    if($status==1){
    	return "Placed";
    }else if($status==2){
    	return "In Progress";
    }else if($status==3){
    	return "Shipped";
    }else if($status==4){
    	return "Delivered";
    }else if($status==5){
    	return "Cancelled";
    }else{
    	return "N/A";
    }
}

// Payment status
function PaymentStatus($status)
{
    if($status==0){
    	return "Pending";
    }else if($status==1){
    	return "Success";
    }else if($status==2){
    	return "Failed";
    }else{
    	return "N/A";
    }
}

// Date time
function DateTime($date)
{
    if($date!=""){
    	return date('d-M-Y h:i a',strtotime($date));
    }else{
    	return "N/A";
    }
}

// Send SMSMessage
function sendSms($message,$mobile_number)
{
    $SMS_USERNAME = env('SMS_USERNAME');
    $SMS_SENDERNAME = env('SMS_SENDERNAME');
    $SMS_APIKEY = env('SMS_APIKEY');
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://sms.kitkattech.com/sendSMS?username='.$SMS_USERNAME.'&message='.$message.'&sendername='.$SMS_SENDERNAME.'&smstype=TRANS&numbers='.$mobile_number.'&apikey='.$SMS_APIKEY,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return true;
}
?>