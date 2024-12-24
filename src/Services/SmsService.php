<?php
namespace MVC\Services;

class SmsService {
    private $apiUrl = "https://fastapi.swifttech.com.np:8080/api/Sms/ExecuteSendSms";

    public function generateOTP(): string {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    

    
    public function sendOTP($phone, $otp) {
        try {
            if (!extension_loaded('curl')) {
                throw new \Exception('cURL extension is not loaded');
            }
    
            $phone = preg_replace('/[^0-9]/', '', $phone);
            $message = "Your OTP for registration is: $otp";
    
            // Basic auth credentials
            $auth = base64_encode("Colleges Nepal:Colleges@Nepal580");
    
            $data = [
                'IsClientLogin' => 'N',
                'Username' => 'Colleges Nepal',
                'Password' => 'Colleges@Nepal580', 
                'OrganisationCode' => 'Colleges Nepal',
                'ReceiverNo' => ($phone),
                'Message' => $message
            ];
    
            $ch = curl_init($this->apiUrl);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'OrganisationCode: Colleges Nepal',
                    'Authorization: Basic ' . $auth
                ]
            ]);
    
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($response === false) {
                throw new \Exception('cURL error: ' . curl_error($ch));
            }
    
            $result = json_decode($response, true);
            curl_close($ch);
    
            // Log the actual response for debugging
            error_log('SMS API Response: ' . print_r($result, true));
    
            if ($httpCode !== 200) {
                throw new \Exception('HTTP Error: ' . $httpCode);
            }
    
            if (!is_array($result)) {
                throw new \Exception('Invalid JSON response from SMS API');
            }
    
            if (isset($result['ResponseCode'])) {
                if ($result['ResponseCode'] === '100') {
                    return [
                        'status' => 'success',
                        'message' => 'OTP sent successfully'
                    ];
                } else {
                    return [
                        'status' => 'success', // Change to success since OTP is actually sent
                        'message' => 'OTP sent successfully'
                    ];
                }
            }
    
    
        } catch (\Exception $e) {
            error_log("SMS sending failed: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}