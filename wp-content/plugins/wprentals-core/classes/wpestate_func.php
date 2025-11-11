<?php
class WpestateFunk {
    
//    const WPESTATE_THEMES_API        = 'https://nppff58eg3.execute-api.us-east-1.amazonaws.com/live/wprentals/';
    
    const WPESTATE_THEMES_API        = 'https://nppff58eg3.execute-api.us-east-1.amazonaws.com/test/wprentals';
    const ENVATO_ITEM_ID            = '12921802';
    const PURCHASE_CODE_OPTION_NAME = 'envato_purchase_code_12921802';
    const NONCE_ACTION              = 'wpestate_nonce_license';
    const EXPIRY_OPTION_NAME        = 'api_expiry';
    const ACCESS_TOKEN_OPTION_NAME   =  'wpestate_access_token';
    private static $instance;


    
    private function __construct() {
        // private constructor to prevent direct instantiation
        add_action( 'wp_ajax_wpestate_register_license', [ $this, 'register_license' ] );
        add_action( 'wp_ajax_wpestate_revoke_license', [ $this, 'revoke_license' ] );
        add_action('admin_menu', [ $this, 'custom_menu_item']) ;
    }

    
    
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function update_option( $name, $value ) {
        return update_option( $name, $value );
    }

    function sanitize_checkbox( $data ) {
		return ( true === $data || 'on' === $data || 'true' === $data ) ? 'on' : 'off';
    }
    
    private function is_sha256( $string = '' ) {
		//return preg_match( '/^[a-f0-9]{64}$/', $string );
    }
        
    public function custom_menu_item(){
          
    }
    
    private function get_user_data() {
		$user = wp_get_current_user();

		$user_data = [
			'first_name' => $user->user_firstname,
			'last_name'  => $user->user_lastname,
		];

		return $user_data;
	}

    private function get_domain() {
            return get_site_url();
    }

    private function has_access_token() {
            return ! empty( $this->get_option( self::ACCESS_TOKEN_OPTION_NAME ) );
    } 
    
    private function has_purchase_code() {
            return ! empty( $this->get_option( self::PURCHASE_CODE_OPTION_NAME ) );
    }
        
    private function get_option( $name ) {
        return get_option( $name, false );
    }    
        
        
    public function show_deregister_license_form(){
     
        print '<div class="wpestate_register_license_wrapper">';
        print '<div id="wpestate_deregister_license_notification"></div>';
        
     
       
       
       
        print '</div>'; 
    }   
        
        
        
        
        
        
    public function show_license_form(){
           
      
    }
    
    
    
    
    
    
    public function register_license() {
	}
        
        
        
        
        public function verify_purchase_code( $email, $purchase_code,$envato_username ) {
		

		if ( ! $purchase_code || $this->is_sha256( $purchase_code ) ) {
			return new WP_Error(
				'purchase_code_error',
				__( 'Purchase code is invalid.', 'wprentals-core' )
			);
		}

		

		$data                     = $this->get_user_data();
		$data['website']          = $this->get_domain();
		$data['email']            = $email;
		$data['license_code']     = $purchase_code;
                $data['envato_username']  = $envato_username;
		$data['checktype']        = 'website';

          
                
		$request = wp_remote_post( self::WPESTATE_THEMES_API . '', [
			'body' => json_encode($data),
		] );
                $result = json_decode( wp_remote_retrieve_body( $request ), true );

		
		if ( is_wp_error( $request ) ) {
			return new WP_Error(
				'purchase_code_network_error',
				$request->get_error_message()
			);
		}

	
		if ( (  isset( $result['success'] ) &&  !$result['success'] )) {
			return new WP_Error(
				'purchase_code_verify_error',
				str_replace('"', '', $result['body'])
			);
		}

	

		return [
                        'success'       => true,
			'purchase_code' => $purchase_code,
			'access_token'  => $result['access_token'],
                        'message'=>$result['body']
		];
	}

        
        
        
        public function revoke_license() {
                if ( !  check_ajax_referer( 'wprentals_activate_license_nonce',  'security' )  ) {
                    wp_send_json_error( [
                            'success' => false,
                            'code'    => 'nonce_error',
                            'message' => __( 'Action is not allowed.', 'wprentals-core' ),
                    ] );
                }

                if ( ! current_user_can( 'manage_options' ) ) {
                    wp_send_json_error( 'You do not have access to this section.', 'wprentals-core' );
                }
		
		$data                 = [];
		$data['access_token'] = get_option( 'wpestate_access_token' );
                $data['license_code'] = get_option('envato_purchase_code_12921802');
                        
                        
                if( $data['access_token'] =='' ||$data['license_code']=='' ){
                    	wp_send_json_error( [
				'code'    => 'revoke_verify_error',
				'message' => 'License or token are empy',
			] );
                }

                $request = wp_remote_post( self::WPESTATE_THEMES_API . '', [
			'body' => json_encode($data),
		] );

		
		if ( is_wp_error( $request ) ) {
			wp_send_json_error( [
				'code'    => 'revoke_verify_network_error',
				'message' => $request->get_error_message(),
			] );
		}

		$result = json_decode( wp_remote_retrieve_body( $request ), true );

		if ( ! $result['success'] ) {
			wp_send_json_error( [
				'code'    => 'revoke_verify_error',
				'message' => $result['message'],
			] );
		}

		delete_option('envato_purchase_code_12921802');
                delete_option('wpestate_access_token');
                delete_transient('envato_purchase_code_12921802_demos');
		wp_send_json_success( [
                        'success'=> true,
			'code'    => 'revoke_success',
			'message' => __( 'Your license has been successfully revoked.', 'wprentals-core' ),
		] );
	}
        
        
        public function is_registered() {
       
            	$access_token = $this->has_access_token();
	        $purchase_code = $this->has_access_token() ;
                
                return (
			
			$access_token &&
			$purchase_code 
		);
	}
        
        
        public function get_demos_data(){
            if($this->is_registered() ){
              
                $demos_data= get_transient('envato_purchase_code_12921802_demos');
             
                if($demos_data==false){
                  
                    $enquire_answer  =  $this->wpestate_enquire_license(  $this->get_option( self::PURCHASE_CODE_OPTION_NAME)  );
                    if( isset($enquire_answer['demos']) ){
                        $demos_data      =  $enquire_answer['demos'];
                    }
                    return $demos_data;
                }else{
                     return $demos_data;
                }
            }else{
                return false;
            }
        }
        
        private function wpestate_enquire_license($wpestate_license_key){
            $data= array(
                        'license'   =>  trim($wpestate_license_key),
                        'action'    =>  'wpestate_envato_lic'
                    );

            $args=array(
                    'method' => 'POST',
                    'timeout' => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'sslverify' => false,
                    'blocking' => true,
                    'body' =>  $data,
                    'headers' => [
                          'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
                    ],
            );

        $url="http://support.wpestate.org/theme_license_check_wprentals_cloud.php";
        $response = wp_remote_post( $url, $args );

        if ( is_wp_error( $response ) ) {
	    $error_message = $response->get_error_message();
            die($error_message);
	} else {

           

	}
}

        
        
        
        
        
}WpestateFunk::get_instance();