<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Test extends CI_Controller
{

    public $data = array();
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }

    public function index()
    {
        $tokenRand = md5(uniqid(mt_rand(), true));
        $this->session->set_userdata(array('AmazonToken' => $tokenRand));
        $this->data['token'] = $tokenRand;
        $this->load->view('Test',$this->data);
    }



    public function aws_signature()
    {
        $token = $this->session->userdata('AmazonToken');
        if ($this->input->get_post("csrf") && $this->input->get_post("csrf") == $token) {
            $this->session->unset_userdata('AmazonToken');
            // Mandatory fields
            $amount = $this->input->get_post("amount");
            $returnURL = base_url('Test/SuccessPaymentFromAmazon');

            // Optional fields
            $currencyCode = $this->input->get_post("currencyCode");
            $sellerNote = $this->input->get_post("sellerNote");
            $sellerOrderId = $this->input->get_post("OrderDetail");
            $shippingAddressRequired = "true";
            $paymentAction = "AuthorizeAndCapture"; // other values None,Authorize

            //Addding the parameters to the PHP data structure
            $parameters["lwaClientId"] = 'CLIENT_ID';
            $parameters["sellerId"] = 'MERCHANT_ID';
            $parameters["accessKey"] = 'ACCESS_KEY';
            $parameters["amount"] = $amount;
            $parameters["returnURL"] = $returnURL;
            $parameters["sellerNote"] = $sellerNote;
            $parameters["sellerOrderId"] = $sellerOrderId;
            $parameters["currencyCode"] = $currencyCode;
            $parameters["shippingAddressRequired"] = $shippingAddressRequired;
            $parameters["paymentAction"] = $paymentAction;

            uksort($parameters, 'strcmp');

            //call the function to sign the parameters and return the URL encoded signature
            $Signature = $this->amazon->_urlencode($this->amazon->_signParameters($parameters));

            //add the signature to the parameters data structure
            $parameters["signature"] = $Signature;

            //echoing the parameters will be picked up by the ajax success function in the front end
            $this->output->set_output(json_encode($parameters));
            $string = $this->output->get_output();
            echo $string;
            exit();
        }
    }


    public function SuccessPaymentFromAmazon(){

        /*After success user will be redirected to this action */
        echo '<pre>';
        print_r($_REQUEST);
        die;
    }

}
?>