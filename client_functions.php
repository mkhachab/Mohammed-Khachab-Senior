<?php

include("../include/functions.php");
if(isset($_POST['bookRoom'])){
    $roomTypeId = $_POST['roomTypeId'];
    $email = $_POST['email'];
    $contactno = $_POST['contactno'];
    $no_of_guest = $_POST['no_of_guest'];
    $checkIn = $_POST['checkIn'];
    $checkOut = $_POST['checkOut'];
    $totalCost = $_POST['totalCost'];
    $userId = $_SESSION['loggedUserId'];

    $checkIn = strtotime($checkIn);
    $checkIn = date('Y-m-d', $checkIn); 
    
    $checkOut = strtotime($checkOut);
    $checkOut = date('Y-m-d', $checkOut);

    $query_roomType = "SELECT * FROM room_list WHERE RoomTypeId = '$roomTypeId' AND Status = 'active' ORDER BY RoomId";
    $roomType  = mysqli_query($con, $query_roomType);


    if(mysqli_num_rows($roomType) > 0) {
        // Count the number of available rooms of the specified type
        $availableRoomsCount = mysqli_num_rows($roomType);

        // Count the number of bookings for the specified room type during the specified period
        $existingBookingsQuery = "SELECT * FROM room_booking WHERE RoomId IN (
            SELECT RoomId FROM room_list WHERE RoomTypeId = '$roomTypeId' AND Status = 'active'
        ) AND ((CheckIn < '$checkOut' AND CheckOut > '$checkIn') OR 
        (CheckIn >= '$checkIn' AND CheckOut <= '$checkOut'))";
        $existingBookingsResult = mysqli_query($con, $existingBookingsQuery);
        $bookedRoomsCount = mysqli_num_rows($existingBookingsResult);

        // Calculate the number of available rooms after considering existing bookings
        $availableRoomsCount -= $bookedRoomsCount;

        // Query to fetch the minimum checkout date
        $minCheckoutDateQuery = "SELECT MIN(CheckOut) AS min_checkout_date FROM room_booking";

        // Execute the query
        $minCheckoutDateResult = mysqli_query($con, $minCheckoutDateQuery);
        
        $minCheckoutDateRow = mysqli_fetch_assoc($minCheckoutDateResult);
    
    // Retrieve the minimum checkout date from the result
    $minCheckoutDate = $minCheckoutDateRow['min_checkout_date'];


        if ($availableRoomsCount > 0) {
            // If there are available rooms, proceed with booking
            $row = mysqli_fetch_assoc($roomType); // Fetch one room for booking

            $ID = $row['RoomId'];

            // Insert booking details into room_booking table
            $reg = "INSERT INTO room_booking (RoomId, User_id, Date, CheckIn, CheckOut, NoOfGuest, Amount, Email, Phone_number)
                    VALUES ('$ID', '$userId', CURDATE(), '$checkIn', '$checkOut', '$no_of_guest', '$totalCost', '$email', '$contactno')";

            mysqli_query($con, $reg);

            // Update room status to 'Booked'
            $update_query = "UPDATE room_list SET Booking_status = 'Booked' WHERE RoomId = '$ID'";
            mysqli_query($con, $update_query);

            echo "<script>alert('Booking Successful.'); window.location.href='mybooking.php'; </script>";
        } else {
            // If no rooms are available, display an alert
            echo "<script>alert('Oops! No rooms available for this type before $minCheckoutDate.'); window.location.href='room.php'; </script>";
        }
    } else {
        // If no rooms of the specified type are found, display an alert
        echo "<script>alert('Oops! No rooms available for this type.'); window.location.href='room.php'; </script>";
    }
}
 
if (isset($_POST['bookEvent'])) {

    // Sanitize and validate input data
    $eventTypeId = mysqli_real_escape_string($con, $_POST['eventTypeId']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $contactno = mysqli_real_escape_string($con, $_POST['contactno']);
    $eventDate = mysqli_real_escape_string($con, $_POST['eventDate']);
    $eventTime = mysqli_real_escape_string($con, $_POST['eventTime']);
    $total_hours = mysqli_real_escape_string($con, $_POST['total_hours']);
    $totalCost = mysqli_real_escape_string($con, $_POST['totalCost']);
    $userId = $_SESSION['loggedUserId'];
    $no_of_guest = mysqli_real_escape_string($con, $_POST['no_of_guest']);

    // Format the date and time
    $eventTime = date("H:i", strtotime($eventTime));
    $eventDate = date('Y-m-d', strtotime($eventDate));

    // Fetch event type details
    $query_eventType = "SELECT * FROM event_list WHERE EventTypeId = '$eventTypeId' AND Status = 'active' ORDER BY EventId";
    $Type = mysqli_query($con, $query_eventType);

    if (mysqli_num_rows($Type) > 0) {
        while ($row = mysqli_fetch_assoc($Type)) {
            $ID = $row['EventId'];

            // Check if the event is already booked for the specified date and time
            $existingEventQuery = "
                SELECT EventTime, ADDTIME(EventTime, SEC_TO_TIME(Package * 3600)) AS EndTime 
                FROM event_booking 
                WHERE EventId = '$ID' 
                AND Event_date = '$eventDate' 
                ORDER BY EventTime
            ";
            $existingEventResult = mysqli_query($con, $existingEventQuery);

            $isAvailable = true;
            $nextAvailableTime = $eventTime;
            $requestedEndTime = date("H:i", strtotime("+$total_hours hours", strtotime($eventTime)));

            while ($booking = mysqli_fetch_assoc($existingEventResult)) {
                $existingStartTime = $booking['EventTime'];
                $existingEndTime = $booking['EndTime'];

                if (
                    ($eventTime >= $existingStartTime && $eventTime < $existingEndTime) || 
                    ($requestedEndTime > $existingStartTime && $requestedEndTime <= $existingEndTime) ||
                    ($eventTime < $existingStartTime && $requestedEndTime > $existingEndTime)
                ) {
                    $isAvailable = false;
                    $nextAvailableTime = $existingEndTime;
                }
            }

            if ($isAvailable) {
                // Insert new booking
                $reg = "
                    INSERT INTO event_booking (EventId, User_id, Date, Event_date, NoOfGuest, EventTime, Package, Amount, Email, Phone_number)
                    VALUES ('$ID', '$userId', CURDATE(), '$eventDate', '$no_of_guest', '$eventTime', '$total_hours', '$totalCost', '$email', '$contactno')
                ";
                
                // Update event status to booked
                $update_query = "UPDATE event_list SET Booking_status = 'Booked' WHERE EventId = '$ID'";
                
                // Execute queries
                mysqli_query($con, $reg);
                mysqli_query($con, $update_query);
                
                echo "<script>alert('Booking Successful.'); window.location.href='mybooking.php'; </script>";
            } else {
                echo "<script>alert('This event is already taken on the specified date and time. The next available slot at $eventDate is $nextAvailableTime.'); window.location.href='room.php'; </script>";
            }
            break;
        }
    } else {
        echo "<script>alert('Event type not found or inactive.'); window.location.href='room.php'; </script>";
    }
}

// ----------------------------------------- Account Action -----------------------------------------------
//update the details of user table

if(isset($_POST['updateAccount'])){
             
  $user_id = mysqli_real_escape_string($con, $_POST['updateAccount']);
  $firstname = mysqli_real_escape_string($con, $_POST['firstName']);
  $lastname = mysqli_real_escape_string($con, $_POST['lastname']);
  $email = mysqli_real_escape_string($con, $_POST['email']);
  $contactno = mysqli_real_escape_string($con, $_POST['contactno']);  
  $gender = mysqli_real_escape_string($con, $_POST['gender']);

  // profile image upload
  $profileImageName = $_FILES["profileImage"]["name"];
  $tempname = $_FILES["profileImage"]["tmp_name"];   
  $folder = "../assets/picture/profiles/".$profileImageName;
       

  // $re_pass = base64_encode(mysqli_real_escape_string($conn, $_POST['reg_pass']));

  $User_details="SELECT * FROM users_details WHERE (Firstname='$firstname' OR Email='$email') AND UserId <> ' $user_id '";
  $result=mysqli_query($con,$User_details)or die("can't fetch");
  $num=mysqli_num_rows($result);


  $sendData = array();
 
  
 if ($firstname == "admin") {
      $error="Invalid Username (You cannot use the username as admin!)";
      $sendData = array(
          "msg"=>"",
          "error"=>$error
      );
      echo json_encode($sendData);
  } 
 else if ($num>0) {
      $error="Username or email id is already taken!";
      $sendData = array(
          "msg"=>"",
          "error"=>$error
      );
      echo json_encode($sendData);
  } else {

                  // query validation
                  $update="UPDATE users_details SET  FirstName='$firstname', LastName ='$lastname',Email='$email',ContactNo='$contactno',Gender='$gender',ProfileImage='$profileImageName' where UserId = '$user_id'" ;


                  if(mysqli_query($con,$update))
                  {
                      if(!move_uploaded_file($tempname, $folder)){
                      //if(false){
                        $error = "Error in Updation ...! Try after sometime";
                        $sendData = array(
                            "msg" => "",
                            "error" => $error
                        );
                        echo json_encode($sendData);
                    } else {
                        $message = "User details updated";
                        $sendData = array(
                            "msg" => $message,
                            "error" => ""
                        );
                        echo json_encode($sendData);
                    }
                }
                else {
                      $error = "Error in Updation ...! Try after sometime";
                      $sendData = array(
                        "msg" => "",
                        "error" => $error
                    );
                    echo json_encode($sendData);

              }

         
    
}

}

// -------------------------------- Change password -----------------------------------

if(isset($_POST["oldPassword"])){
$old = $_POST['oldPassword'];
$new = $_POST['newPassword'];
$ID = $_POST['change_password'];

$Q = "SELECT * FROM users_details WHERE UserId = '$ID'";
$res = mysqli_query($con, $Q);
$row = mysqli_fetch_assoc($res);
$num = mysqli_num_rows($res);


$sendData = array();
if($num > 0){

    if($old == $row['Password']){
        $Q_update = "UPDATE users_details us SET us.Password = '$new' WHERE UserId = '$ID'";
        $result = mysqli_query($con, $Q_update);
        $msg = "Password Changed";
        $sendData = array(
            "msg" => $msg,
            "error" => ""
        );
    } else {
        $error = "Oops! Wrong Old Password";
        $sendData = array(
          "msg" => "",
          "error" => $error
      );
    }
} else {

    $error = "Invalid User ID ";
    $sendData = array(
      "msg" => "",
      "error" => $error
  );
}
echo json_encode($sendData);
}
?> 

