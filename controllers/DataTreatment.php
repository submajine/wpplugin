<?php

    class DataTreatment
    { private $prk= "-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQCqGKukO1De7zhZj6+H0qtjTkVxwTCpvKe4eCZ0FPqri0cb2JZfXJ/DgYSF6vUp
wmJG8wVQZKjeGcjDOL5UlsuusFncCzWBQ7RKNUSesmQRMSGkVb1/3j+skZ6UtW+5u09lHNsj6tQ5
1s1SPrCBkedbNf0Tp0GbMJDyR4e9T04ZZwIDAQABAoGAFijko56+qGyN8M0RVyaRAXz++xTqHBLh
3tx4VgMtrQ+WEgCjhoTwo23KMBAuJGSYnRmoBZM3lMfTKevIkAidPExvYCdm5dYq3XToLkkLv5L2
pIIVOFMDG+KESnAFV7l2c+cnzRMW0+b6f8mR1CJzZuxVLL6Q02fvLi55/mbSYxECQQDeAw6fiIQX
GukBI4eMZZt4nscy2o12KyYner3VpoeE+Np2q+Z3pvAMd/aNzQ/W9WaI+NRfcxUJrmfPwIGm63il
AkEAxCL5HQb2bQr4ByorcMWm/hEP2MZzROV73yF41hPsRC9m66KrheO9HPTJuo3/9s5p+sqGxOlF
L0NDt4SkosjgGwJAFklyR1uZ/wPJjj611cdBcztlPdqoxssQGnh85BzCj/u3WqBpE2vjvyyvyI5k
X6zk7S0ljKtt2jny2+00VsBerQJBAJGC1Mg5Oydo5NwD6BiROrPxGo2bpTbu/fhrT8ebHkTz2epl
U9VQQSQzY1oZMVX8i1m5WUTLPz2yLJIBQVdXqhMCQBGoiuSoSjafUhV7i1cEGpb88h5NBYZzWXGZ
37sJ5QsW+sJyoNde3xH8vdXhzU7eT82D6X/scw9RZz+/6rCJ4p0=
-----END RSA PRIVATE KEY-----";
        private $pk = "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCqGKukO1De7zhZj6+H0qtjTkVxwTCpvKe4eCZ0
FPqri0cb2JZfXJ/DgYSF6vUpwmJG8wVQZKjeGcjDOL5UlsuusFncCzWBQ7RKNUSesmQRMSGkVb1/
3j+skZ6UtW+5u09lHNsj6tQ51s1SPrCBkedbNf0Tp0GbMJDyR4e9T04ZZwIDAQAB
-----END PUBLIC KEY-----";
        public function __construct()
        {
            set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
            include('phpseclib/Crypt/RSA.php');
            include_once('phpseclib/Math/BigInteger.php');
        }
        private $post;
        public function setPost($post){
            $this->post = $post;
        }
        public function getPost(){
          return $this->post;
        }
        public function getPrk(){
            return $this->prk;
        }
        public function getPk(){
            return $this->pk;
        }
        private function encryptTreatment($messageToEncrypt){
            $rsa = new Crypt_RSA();
            $rsa->loadKey($this->getPrk());
            return $rsa->encrypt($messageToEncrypt);
        }
        private function decryptTreatment($cryptedMessage){
            $rsa = new Crypt_RSA();
            $rsa->loadKey($this->getPk());
            return $rsa->decrypt($cryptedMessage);
        }
        public function addUserMeta($currentUser){
            $postqry= $this->getPost();
            $cryptedQuery = [
                'address'=>$this->encryptTreatment($postqry['metaPageOptions']['addressTextField']),
                'phone'=>$this->encryptTreatment($postqry['metaPageOptions']['phoneTextField']),
                'gender'=>$this->encryptTreatment($postqry['metaPageOptions']['genderRadioField']),
                'married'=>$this->encryptTreatment($postqry['metaPageOptions']['marriedRadioField'])
            ];

            foreach ( $cryptedQuery as $value){
                $key = array_search($value,$cryptedQuery);
                update_user_meta($currentUser,$key,base64_encode($value));
            }
        }
        public function getUserMeta($userDisplayName){
            global $wpdb;
            $id = $wpdb->get_row($wpdb->prepare("Select user_id from $wpdb->usermeta where meta_key = %s and meta_value =%s",'nickname',$userDisplayName),ARRAY_A);
            $addressqry= $wpdb->get_results(
                $wpdb->prepare("select meta_value from $wpdb->usermeta where user_id =%d and meta_key =%s" ,$id['user_id'],'address'),ARRAY_A
            );
            $cryptedaddress = base64_decode($addressqry[0]['meta_value']);
            $decrtyptedadress = $this->decryptTreatment($cryptedaddress);

            $phoneqry= $wpdb->get_results(
                $wpdb->prepare("select meta_value from $wpdb->usermeta where user_id =%d and meta_key =%s" ,$id['user_id'],'phone'),ARRAY_A
            );
            $cryptedphone = base64_decode($phoneqry[0]['meta_value']);
            $decrtyptedphone = $this->decryptTreatment($cryptedphone);

            $marriedqry= $wpdb->get_results(
                $wpdb->prepare("select meta_value from $wpdb->usermeta where user_id =%d and meta_key =%s" ,$id['user_id'],'married'),ARRAY_A
            );
            $cryptedmarried = base64_decode($marriedqry[0]['meta_value']);
            $decrtyptedmarried = $this->decryptTreatment($cryptedmarried);

            $genderdqry= $wpdb->get_results(
                $wpdb->prepare("select meta_value from $wpdb->usermeta where user_id =%d and meta_key =%s" ,$id['user_id'],'gender'),ARRAY_A
            );
            $cryptedgender = base64_decode($genderdqry[0]['meta_value']);
            $decrtyptedgender = $this->decryptTreatment($cryptedgender);

            return [
                'address'=>$decrtyptedadress,
                'phone'=>$decrtyptedphone,
                'married'=>$decrtyptedmarried,
                'gender'=>$decrtyptedgender
            ];

        }





    }