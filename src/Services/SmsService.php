<?php
namespace MVC\Services;

class SmsService {
    private $apiUrl = "https://fastapi.swifttech.com.np:8080/api/Sms/ExecuteSendSms";

    public function generateOTP(): string {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function sendOTP($phone, $otp) {
        try {
            $phone = preg_replace('/[^0-9]/', '', $phone);
            $message = "Your OTP for registration is: $otp";
            
            $auth = base64_encode("Colleges Nepal:Colleges@Nepal580");
            
            $data = [
                'IsClientLogin' => 'N',
                'Username' => 'Colleges Nepal',
                'Password' => 'Colleges@Nepal580',
                'OrganisationCode' => 'Colleges Nepal',
                'ReceiverNo' => intval($phone),
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

            curl_close($ch);

            return [
                'status' => 'success',
                'message' => 'OTP sent successfully'
            ];

        } catch (\Exception $e) {
            error_log("SMS sending failed: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}