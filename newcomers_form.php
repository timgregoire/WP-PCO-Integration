<?php
//Uses personal access token from PCO
$AppID = "";
$secret = "";

$new_person_endpoint = 'https://api.planningcenteronline.com/people/v2/people';
/*Contains specific ID for New Visitor Contact workflow*/
//Replace ID with your specific workflow ID to post to
$create_workflow_endpoint = 'https://api.planningcenteronline.com/people/v2/workflows/id/cards';




//Data from form on site
$first_name =$_POST["first_name"];
$last_name =$_POST["last_name"];
$email = $_POST["email"];
$phone_number = $_POST["phone_number"];
$gender=$_POST["gender"];
$neighborhood = $_POST["neighborhood"];
$time_attending_beloved = $_POST["time_attending_beloved"];
$first_contact = $_POST["first_contact"];
$interested_in_membership = $_POST["membership"];
$coffee_with_member = $_POST["coffee_with_member"];
$newsletter = $_POST["newsletter"];
$small_group_interest = $_POST["small_group"];

//Sanitize input from form
$email_address = filter_var($email, FILTER_SANITIZE_EMAIL);
$first_name = filter_var($first_name, FILTER_SANITIZE_STRIPPED );
$last_name =  filter_var($last_name, FILTER_SANITIZE_STRIPPED );
$phone_number =  filter_var($phone_number, FILTER_SANITIZE_NUMBER_INT);
$gender =  filter_var($gender, FILTER_SANITIZE_NUMBER_INT);
$coffee_with_member =  filter_var($coffee_with_member, FILTER_VALIDATE_BOOLEAN);
$interested_in_membership =  filter_var($interested_in_membership, FILTER_VALIDATE_BOOLEAN);
$ministeries_interested_in =  filter_var($ministeries_interested_in, FILTER_SANITIZE_STRIPPED );
$neighborhood = filter_var($neighborhood, FILTER_SANITIZE_STRIPPED );
$time_attending_beloved = filter_var($time_attending_beloved, FILTER_SANITIZE_STRIPPED );
$first_contact = filter_var($first_contact, FILTER_SANITIZE_STRIPPED );
$small_group_interest = filter_var($small_group_interest, FILTER_SANITIZE_STRIPPED );
$time_attending_beloved = filter_var($time_attending_beloved, FILTER_SANITIZE_STRIPPED );
$interested_in_membership =  filter_var($interested_in_membership, FILTER_VALIDATE_BOOLEAN);
$newsletter =  filter_var($newsletter, FILTER_VALIDATE_BOOLEAN);
$coffee_with_member =  filter_var($coffee_with_member, FILTER_VALIDATE_BOOLEAN);


if($_POST['ministry_interest'])
  $ministeries_interested_in .= $_POST['ministry_interest']  . PHP_EOL;
if($_POST['ministry_interest1'])
  $ministeries_interested_in .= $_POST['ministry_interest1'] . PHP_EOL ;
if($_POST['ministry_interest2'])
  $ministeries_interested_in .= $_POST['ministry_interest2'] . PHP_EOL;
if($_POST['ministry_interest3'])
  $ministeries_interested_in .= $_POST['ministry_interest3'] . PHP_EOL;
if($_POST['ministry_interest4'])
  $ministeries_interested_in .= $_POST['ministry_interest4'] . PHP_EOL;
if($_POST['ministry_interest5'])
  $ministeries_interested_in .= $_POST['ministry_interest5'] . PHP_EOL;
if($_POST['ministry_interest6'])
  $ministeries_interested_in .= $_POST['ministry_interest6'] . PHP_EOL;
if($_POST['ministry_interest7'])
  $ministeries_interested_in .= $_POST['ministry_interest7']  . PHP_EOL;
if($_POST['ministry_interest8'])
  $ministeries_interested_in .= $_POST['ministry_interest8'] . PHP_EOL ;
if($_POST['ministry_interest9'])
  $ministeries_interested_in .= $_POST['ministry_interest9'] . PHP_EOL;
if($_POST['ministry_interest10'])
  $ministeries_interested_in .= $_POST['ministry_interest10'] . PHP_EOL;
if($_POST['ministry_interest11'])
  $ministeries_interested_in .= $_POST['ministry_interest11'] . PHP_EOL;
if($_POST['ministry_interest12'])
  $ministeries_interested_in .= $_POST['ministry_interest12'] . PHP_EOL;
if($_POST['ministry_interest13'])
  $ministeries_interested_in .= $_POST['ministry_interest13'] . PHP_EOL;
  if($_POST['ministry_interest14'])
    $ministeries_interested_in .= $_POST['ministry_interest14']  . PHP_EOL;


if($gender==1)
  $gender = "";
if($gender == 2)
  $gender = "Male";
if($gender == 3)
  $gender = "Female";
if($gender == 4)
  $gender = "";



if($coffee_with_member == True)
  $coffee_with_member = "Yes";
  else $coffee_with_member = "No";

if($interested_in_membership == True)
  $interested_in_membership = "Yes";
  else $interested_in_membership = "No";

$notes = 'Neighborhood: ' . $neighborhood . PHP_EOL .
         'How they heard about COTB: ' . $first_contact . PHP_EOL .
         "How long they've been attending COTB: " . $time_attending_beloved. PHP_EOL .
         "Small groups interested in: " . $small_group_interest . PHP_EOL .
         "Interested in coffee with a member: " . $coffee_with_member . PHP_EOL .
         "Interested in membership: " . $interested_in_membership . PHP_EOL .
         "Ministeries interested in: " .  $ministeries_interested_in . PHP_EOL
         ;


//Create objects needed and make the call to create a new person in PCOPeople
//Get the new person's PCO ID and save it for future use
$person_request_object = create_person_request_object($first_name,$last_name, $gender);
$opts = create_opts($person_request_object,$AppID,$secret);
$context = stream_context_create($opts);
$person_file = file_get_contents($new_person_endpoint, false, $context);
$person_file = json_decode($person_file, true);
$PCO_person_id = $person_file["data"]["id"];

//Add phone number to person
$email_endpoint = $person_file["data"]["links"]["phone_numbers"];
$opts = create_opts(phone_request_object($phone_number),$AppID,$secret);
$context = stream_context_create($opts);
$file = file_get_contents($email_endpoint, false, $context);


//Add email address to person
$email_endpoint = $person_file["data"]["links"]["emails"];
$opts = create_opts(email_request_object($email_address),$AppID,$secret);
$context = stream_context_create($opts);
$file = file_get_contents($email_endpoint, false, $context);


//Add workflow card to the new person created above
$workflow_request_object = create_workflow_request_object($PCO_person_id);
$opts = create_opts($workflow_request_object,$AppID,$secret);
$context = stream_context_create($opts);
$file = file_get_contents($create_workflow_endpoint, false, $context);
$file = json_decode($file, true);


//Add note to workflow card that was just created
$note_endpoint = $file["data"]["links"]["notes"];
$opts = create_opts(workflow_note_object($notes),$AppID,$secret);
$context = stream_context_create($opts);
$file = file_get_contents($note_endpoint, false, $context);


//Redirect to another page after form is submitted to PCO
header('Location: ');
exit();



function create_opts($request_object, $AppID, $secret){

  $opts = array(
    'http'=>array(
      'method'=>"POST",
      'header' => "Content-Type: application/x-www-form-urlencoded\r\n" . "Authorization: Basic " . base64_encode("$AppID:$secret"),
       'content'=>$request_object
      )
     );
  return $opts;
}

function create_person_request_object($first_name,$last_name,$gender){
  $person_request_object =json_encode(
    array( "data"=> array(
            "type"=>"Person",
            "attributes"=>array(
              "first_name"=>$first_name,
              "last_name"=>$last_name,
              "gender"=> $gender

            )
            )
          )
        );
  return $person_request_object;
}

function create_workflow_request_object($PCO_person_id){

  $workflow_request_object =json_encode(
    array("data"=>array(
      "type"=>"WorkflowCard",
      "attributes"=>(object) array(),
      "relationships"=>array(
        "person"=>array(
          "data"=>array(
           "type"=>'Person',
           "id"=>(string) $PCO_person_id)
          )
        )
      )
    )
  );

  return $workflow_request_object;

}

function workflow_note_object($comments){
  $note_data =json_encode(
    array("data"=>array(
      "type"=>"WorkflowCard",
      "attributes"=>(object) array(
        "note" => $comments
      ),
      )
    )
  );
  return $note_data;
}

function email_request_object($email_address){

  $email_data =json_encode(
    array("data"=>array(
      "type"=>"Email",
      "attributes"=>(object) array(
        "address" => $email_address,
        "location" => "Home",
        "primary" => true
      ),
      )
    )
  );
  return $email_data;


}

function phone_request_object($phone_number){

  $phone_data =json_encode(
    array("data"=>array(
      "type"=>"Email",
      "attributes"=>(object) array(
        "number" => $phone_number,
        "location" => "Mobile",
        "primary" => true
      ),
      )
    )
  );
  return $phone_data;


}



 ?>
