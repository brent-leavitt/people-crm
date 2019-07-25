<?php


$importer = new NB_User_Import();



class NB_User_Import{
	
	public $userdata = array(
		'ID' 					=> 0, 	//(int) User ID. If supplied, the user will be updated.
		'user_pass'				=> '', 	//(string) The plain-text user password.
		'user_login' 			=> '', 	//(string) The user's login username.
		'user_nicename' 		=> '', 	//(string) The URL-friendly user name.
		'user_url' 				=> '', 	//(string) The user URL.
		'user_email' 			=> '', 	//(string) The user email address.
		'display_name' 			=> '', 	//(string) The user's display name. Default is the user's username.
		'nickname' 				=> '', 	//(string) The user's nickname. Default is the user's username.
		'first_name' 			=> '', 	//(string) The user's first name. For new users, will be used to build the first part of the user's display name if $display_name is not specified.
		'last_name' 			=> '', 	//(string) The user's last name. For new users, will be used to build the second part of the user's display name if $display_name is not specified.
		'description' 			=> '', 	//(string) The user's biographical description.
		'rich_editing' 			=> '', 	//(string|bool) Whether to enable the rich-editor for the user. False if not empty.
		'syntax_highlighting' 	=> '', 	//(string|bool) Whether to enable the rich code editor for the user. False if not empty.
		'comment_shortcuts' 	=> '', 	//(string|bool) Whether to enable comment moderation keyboard shortcuts for the user. Default false.
		'admin_color' 			=> '', 	//(string) Admin color scheme for the user. Default 'fresh'.
		'use_ssl' 				=> '', 	//(bool) Whether the user should always access the admin over https. Default false.
		'user_registered' 		=> '', 	//(string) Date the user registered. Format is 'Y-m-d H:i:s'.
		'show_admin_bar_front' 	=> '', 	//(string|bool) Whether to display the Admin Bar for the user on the site's front end. Default true.
		'role' 					=> '', 	//(string) User's role.
		'locale' 				=> '', 	//(string) User's locale. Default empty.

	),
		
	$user_meta = array(
		'' => '',
		'' => '',
		'' => '',
		'' => '',
		'' => '',
		'' => '',
	
	),
	$csvfile = null ;

	
	
/***

	name: 		__construct
	descrip: 
	params: 
	return: 

***/	
	
	public function __construct(){
		
		$this->init();
		
	}
	
	
/***

	name:		init	
	descrip: 	
	params: 
	return: 

***/	
		
	public function init(){
		
		if( !empty ($_FILES) ){
			
			// print_pre($_FILES);
			$this->csvfile = $_FILES['nb_csv']['tmp_name'];
			
			$data_arr = $this->read_csv();
			
			echo "RESULTS: <br />";
			$this->print_pre( $data_arr );
			
			//WP_Insert_user
			foreach( $data_arr as $data ){
				$u_id = $this->insert_user( $data );
			
				if( is_numeric( $u_id ) ){
					
					//nb_insert_misc_user_meta( $u_id,  );
					
				}
			} //end foreach loop
			
		} else {
			
			$this->load_import_form();
			
		}
		
	}
	
	
/***

	name:		read_csv
	descrip: 	
	params: 	NULL
	return: 

***/	
		
	public function read_csv(){
		
		if( $this->csvfile !== NULL ){
			
			
			$csvcontent = '';
				
			if( !file_exists( $this->csvfile ) ) {
				
				echo "File ".$this->csvfile." not found. Make sure you specified the correct path.\n";
				return NULL;
				
			} else{
				
				$file = fopen($this->csvfile,"r");

				if(!$file) {
					echo "Error opening data file.\n";
					return NULL;
				} 
				
				$size = filesize($this->csvfile);

				if(!$size) {
					echo "File is empty.\n";
					return NULL;
				} 

				$csvcontent = fread($file,$size);

				fclose($file);
				
			}
			
			$listArr = array(); //This is what is going to be returned by the function as the final output array. 
			$lineseparator = "/[\r\n]/";
			
			$sourceArr = preg_split( $lineseparator, $csvcontent );
			
			//First line is labels, pop it off the front to generate labels.
			$labelArr = $this->convert_to_array( array_shift( $sourceArr ) );
			
			
			//print_pre( $labelArr );
			
			//print_pre( $sourceArr );
			foreach($sourceArr as $line) {
		
				if( !empty( $line ) ){
					
					$lineArr = $this->convert_to_array( $line );
					
					if($lineArr =  array_combine( $labelArr , $lineArr )){
						//echo "Array combination success! Value of each combo is: <br>";
						//print_pre($lineArr);
						
						$listArr[] = $lineArr;
						
					} else{
						
						echo "Array combination FAILED! : <br>";
						//print_pre($lineArr);
					}
				}
			}
			
			return $listArr;
		}

		return NULL;	
	}
	
	
	
/***

	name:		insert_user
	descrip: 
	params: 
	return: 

***/	
		

	public function insert_user( $user_arr ){
	
	
		//Preform check of submitted user data before attempting to insert new user. 
		// check email address against existing options 
			//user_email or a user_meta field such as paypal_email. 
		
		//
		//Search for user by email first. 
		
		$email_fields = array( 'email_address','user_email','paypal_email' );

		$i = 0; 
		$user = false;

		//Run through the list of possible fields where the email address may be found. 
		while( ( $user == false ) && ( $i < ( sizeof( $email_fields ) - 1 ) ) ){
			
			$user = get_user_by( 'user_email', $listArr[ $email_fields[ $i ] ] );
			
			$i++;
		} 

		//reset, assumes $user is still false. 
		$i = 0;

		//Also look at the user meta for paypal_email. 
		while( ( $user == false ) && ( $i < ( sizeof( $email_fields ) - 1 ) ) ){
			
			$user = get_user_by_meta_data( 'paypal_email', $listArr[ $email_fields[ $i ] ] );
			
			$i++;
		} 



		if( $user_id == 'false' ){
			//insert new user. 
			
			
			
			
		}else{
			
			//user exists, upate or skip data? 
			//Outputs a report of new verses current data. 
			
			
			

		}

		
		
		//$result = wp_insert_user( $user_data ); //returns ID on sucess, WP_Error Object on fail. 
		
		return $result;
	}

	
/***

	name:		convert_to_array
	descrip:	Takes a single line of the CSV file and turns it into an array. 
	params: 
	return: 

***/	
	
	
	public function convert_to_array( $csv_str ){
		//echo "<br />Convert to array invoked. <br />";
		
		$fieldseparator = ",";

		$array = explode( $fieldseparator, $csv_str ); 
		
		if( empty( $array[ sizeof( $array ) -1 ] ) ){
			//echo "<br />last element in array is empty. <br />";
			$trash = array_pop( $array ); //drops the last item off the array as trash. 
		}
		
		
		foreach( $array as $key => $val ){
			$val = str_replace( '"', '', $val ); //remove the quotation marks from the string. 
			$val = str_replace( ' ', '_', $val ); //replace space with underscore in string. 
			
			//Filter to not surpress names with upcase middle letters like LuAnne. 
			$val = strtolower( $val );//lowercase it all. 
			
			$array[ $key ] = $val;
			
		}
		
		return $array;
	}

	
	
/***

	name:		insesrt_misc_user_meta
	descrip: 	
	params: 
	return: 

***/	
	
	public function insert_misc_user_meta( $user_id, $meta = array() ){
		
		
		
		
	}
	
/***

	name:		insert_email_meta_data
	descrip: 	Inserts data specific to email campaigns and the user's status with that campaign.
	params: 	
	return: 

***/	
	
	public function insert_email_meta_data( $user_id, $campaign = 'newsletter', $date_added = '' , $status = 'pending', $track = false ){
		//date("Y-m-d H:i:s")
		
		
		
	}

	
/***

	name:		load_import_form
	descrip: 	Loads a form that allows user to upload CSV files. 
	params: 
	return: 

***/	
	
	public function load_import_form(){
		
		$current_page = $_SERVER['REQUEST_URI'];

		echo "
			<div style='margin: 20px 0; border: 3px solid green; background: white; padding: 20px; width: 500px; font-family: Raleway; font-size: 1.2em;'>
			<p>First line of CSV file must be labels. It will be processed accordingly. </p>
			<form action='$current_page' method='post' enctype='multipart/form-data'>
			<label for='nb_csv'>Upload Recent Records:</label>
			<input type='file' name='nb_csv' id='file'><br>
			<input type='submit' name='submit' value='Upload'>
			</form>
			</div>
		";
	} 


	
/***

	name:		get_user_by_meta_data
	descrip: 	custom function that selects first user from database with selected meta value. Good only for unique meta values. 
	params: 
	return: 

***/	
	
	public function get_user_by_meta_data( $meta_key, $meta_value ) {

		// Query for users based on the meta data
		$user_query = new WP_User_Query(
			array(
				'meta_key'	  =>	$meta_key,
				'meta_value'	=>	$meta_value
			)
		);

		// Get the results from the query, returning the first user
		$users = $user_query->get_results();

		return $users[0];

	} // end get_user_by_meta_data


	
/***

	name:		print_pre
	descrip: 	A simple debugging tool for formatting arrays in a more legible format. 
	params: 
	return: 

***/	
	
	private function print_pre($arr = array()){
		print('<pre>');
		var_dump($arr);
		print('</pre>');
	} 
	
	
}




















 

?>