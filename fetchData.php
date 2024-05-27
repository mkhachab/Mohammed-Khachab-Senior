<style>
    /* The Modal (background) */
.modal {
  display: none;
  position: fixed;
  z-index: 1;
  padding-top: 60px;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgb(0,0,0);
  background-color: rgba(0,0,0,0.9);
}

/* Modal Content (image) */
.modal-content {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
}

/* Caption of Modal Image */
#caption {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
  text-align: center;
  color: #ccc;
  padding: 10px 0;
  height: 150px;
}

/* Add Animation */
.modal-content, #caption {  
  -webkit-animation-name: zoom;
  -webkit-animation-duration: 0.6s;
  animation-name: zoom;
  animation-duration: 0.6s;
}

@-webkit-keyframes zoom {
  from {-webkit-transform:scale(0)} 
  to {-webkit-transform:scale(1)}
}

@keyframes zoom {
  from {transform:scale(0)} 
  to {transform:scale(1)}
}

/* The Close Button */
.close {
  position: absolute;
  top: 15px;
  right: 35px;
  color: #f1f1f1;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
}

.close:hover,
.close:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}

/* Next & previous buttons */
.prev, .next {
  cursor: pointer;
  position: absolute;
  top: 50%;
  width: auto;
  padding: 16px;
  margin-top: -22px;
  color: white;
  font-weight: bold;
  font-size: 20px;
  transition: 0.6s ease;
  border-radius: 0 3px 3px 0;
  user-select: none;
}

.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}

.prev {
  left: 0;
  border-radius: 3px 0 0 3px;
}

.prev:hover, .next:hover {
  background-color: rgba(0,0,0,0.8);
}

</style>
<script>
let modal;
let modalImg;
let images = [];
let currentIndex = 0;

function openModal(element) {
  images = [element.getAttribute('data-image'), element.getAttribute('data-image1'), element.getAttribute('data-image2')];
  currentIndex = 0;
  modal = document.getElementById("myModal");
  modalImg = document.getElementById("modalImage");
  modal.style.display = "block";
  modalImg.src = images[currentIndex];
}

function closeModal() {
  modal.style.display = "none";
}

function nextImage() {
  currentIndex = (currentIndex + 1) % images.length;
  modalImg.src = images[currentIndex];
}

function prevImage() {
  currentIndex = (currentIndex - 1 + images.length) % images.length;
  modalImg.src = images[currentIndex];
}

window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>
<!-- The Modal -->
<div id="myModal" class="modal">
  <span class="close" onclick="closeModal()">&times;</span>
  <img class="modal-content" id="modalImage">
  <a class="prev" onclick="prevImage()">&#10094;</a>
  <a class="next" onclick="nextImage()">&#10095;</a>
</div>

<?php
include("../include/functions.php");
// ------------------------------------ Room types Available ------------------------------------

if(isset($_POST['roomType'])){
    $roomTypeCard ='';
    $typeFilter = $_POST['filter'];
    switch($typeFilter){
        case 1:  $selectAllType = "select rt.*,count(rl.RoomId) as count_rooms
                                    from room_type rt inner join room_list rl on rt.RoomTypeId = rl.RoomTypeId 
                                    where rl.Status='active' AND rt.Status='active'
                                    group by rl.RoomTypeId "; break;

        case 2:  $selectAllType ="select rt.*,count(rl.RoomId) as count_rooms
                                    from room_type rt inner join room_list rl on rt.RoomTypeId = rl.RoomTypeId 
                                    where rl.Status='active' AND rt.Status='active' AND rt.Cost<=50
                                    group by rl.RoomTypeId "; break;

        case 3:  $selectAllType ="select rt.*,count(rl.RoomId) as count_rooms
                                    from room_type rt inner join room_list rl on rt.RoomTypeId = rl.RoomTypeId 
                                    where rl.Status='active' AND rt.Status='active'  AND rt.Cost>=50 AND rt.Cost<=100 
                                    group by rl.RoomTypeId "; break;

        case 4:  $selectAllType = "select rt.*,count(rl.RoomId) as count_rooms
                                    from room_type rt inner join room_list rl on rt.RoomTypeId = rl.RoomTypeId 
                                    where rl.Status='active' AND rt.Status='active' AND rt.Cost>100
                                    group by rl.RoomTypeId "; break;

        default: $selectAllType = "select rt.*,count(rl.RoomId) as count_rooms
                                    from room_type rt inner join room_list rl on rt.RoomTypeId = rl.RoomTypeId 
                                    where rl.Status='active' AND rt.Status='active'
                                    group by rl.RoomTypeId "; break;
        
     }
     $allType = mysqli_query($con,$selectAllType);
     $noOfType = mysqli_num_rows($allType);
 
     if($noOfType>=1){
         while($row=mysqli_fetch_assoc($allType))
         {
             $query_avail = "select count(RoomId) as avail_rooms from room_list where RoomTypeId = ' ".$row["RoomTypeId"]." ' AND Status = 'active' AND Booking_status = 'Available'";
             $exec_avail = mysqli_query($con,$query_avail);
             $countOfRooms=mysqli_fetch_assoc($exec_avail);

          
             $roomTypeCard .= '
             <div class="col-md-4 col-sm-6 ftco-animate fadeInUp ftco-animated">
                 <div class="block-7">
                     <form action="roomBooking.php" method="POST">
                         <div class="room-images" onmouseover="changeImage(this)" onmouseout="restoreImage(this)">
                             <img class="img" src="../assets/picture/RoomType/'.$row['RoomImage'].'" data-image="../assets/picture/RoomType/'.$row['RoomImage'].'" data-image1="../assets/picture/RoomType/'.$row['RoomImage1'].'" data-image2="../assets/picture/RoomType/'.$row['RoomImage2'].'" onclick="openModal(this)" />
                         </div>
                         <div class="text-center p-4">
                             <span class="excerpt d-block">'.$row['RoomType'].'</span>
                             <span class="price mb-2"><sup>USD</sup> <span class="number">'.$row['Cost'].'</span> <sub>/per night</sub></span>
                             <ul class="pricing-text mb-2">';
                                
                                 $roomTypeCard .= '<li><span class="fa fa-check"></span> Facilities: '.$row['Description'].'</li>                      
                             </ul>
                             <input type="hidden" name="roomTypeId" value="'.$row['RoomTypeId'].'" />
                             <button class="btn btn-primary d-block px-1 py-2" value="" name="bookRoom" type="submit">Book</button>
                         </div>
                     </form>
                 </div>
             </div>';
             
             
         }
       }
     else 
     {
     
       $roomTypeCard.='<br><br>
      
            <p class="col-12 text-center text-danger" >No Room Types are Available...</p>'
           ;
     
     }

     echo $roomTypeCard;
}



// ------------------------------------ My Room Booking -------------------------------------
if(isset($_POST['roomBooking'])){

  $roomBooking='<br><br>';
  
  if(isset($_POST['msg'])){ 
    $roomBooking.='<div class="alert alert-success" role="alert">' . $_POST["msg"].' </div>';
  }
  if (isset($_POST["error"])) {
    $roomBooking.='<div class="alert alert-danger">' . $_POST["error"] . '</div>';
  }
  $roomBooking  .='<div class="row">';
  
  $filter = $_POST['filter'];
  $userId = $_SESSION['loggedUserId'];
  switch($filter){
    case 1:  $selectBooking = "SELECT rm.*,rt.RoomType,rl.RoomNumber FROM
                               room_booking rm inner join room_list rl on rl.RoomId = rm.RoomId
                               inner join room_type rt on rl.RoomTypeId = rt.RoomTypeId 
                               where rm.User_id = '$userId' order by rm.Date desc"; break;

    case 2:  $selectBooking =  "SELECT rm.*,rt.RoomType,rl.RoomNumber FROM
                                room_booking rm inner join room_list rl on rl.RoomId = rm.RoomId
                                inner join room_type rt on rl.RoomTypeId = rt.RoomTypeId 
                                where rm.User_id = '$userId' AND rm.Status= 'Booked'  order by rm.Date desc"; break;

    case 3:  $selectBooking =  "SELECT rm.*,rt.RoomType,rl.RoomNumber FROM
                                room_booking rm inner join room_list rl on rl.RoomId = rm.RoomId
                                inner join room_type rt on rl.RoomTypeId = rt.RoomTypeId 
                                where rm.User_id = '$userId' AND (rm.Status= 'Paid' OR rm.Status= 'CheckedOut' ) order by rm.Date desc "; break;

    case 4:  $selectBooking =  "SELECT rm.*,rt.RoomType,rl.RoomNumber FROM
                                room_booking rm inner join room_list rl on rl.RoomId = rm.RoomId
                                inner join room_type rt on rl.RoomTypeId = rt.RoomTypeId 
                                where rm.User_id = '$userId' AND rm.Status= 'Cancelled'  order by rm.Date desc"; break;

    case 5:  $selectBooking =  "SELECT rm.*,rt.RoomType,rl.RoomNumber FROM
                                room_booking rm inner join room_list rl on rl.RoomId = rm.RoomId
                                inner join room_type rt on rl.RoomTypeId = rt.RoomTypeId 
                                where rm.User_id = '$userId' AND rm.Status= 'Rejected'  order by rm.Date desc"; break;

    case 6:  $selectBooking =  "SELECT rm.*,rt.RoomType,rl.RoomNumber FROM
                                room_booking rm inner join room_list rl on rl.RoomId = rm.RoomId
                                inner join room_type rt on rl.RoomTypeId = rt.RoomTypeId 
                                where rm.User_id = '$userId' AND rm.Checkout < CURDATE()  order by rm.Date desc "; break;

    default: $selectBooking =  "SELECT rm.*,rt.RoomType,rl.RoomNumber FROM room_booking rm inner join room_list rl on rl.RoomId = rm.RoomId
                                inner join room_type rt on rl.RoomTypeId = rt.RoomTypeId 
                                where rm.User_id = '$userId' order by rm.Date desc"; break;
    
 }
 $all = mysqli_query($con,$selectBooking);

 if(mysqli_num_rows($all)>=1){
  while($row=mysqli_fetch_assoc($all))
  {
     
      $roomBooking .='
                    <div id="roomBooking" class="col-lg-4 col-md-6" >
                        <div class="card card-margin">
                            <div class="card-header no-border">
                                <h5 class="card-title">'.$row['Status'].'</h5>
                            </div>
                            <div class="card-body pt-0">
                                <div class="widget-49">
                                    <div class="widget-49-title-wrapper">';
                                    if($row['Status']=="Booked"){
                                        $roomBooking .='   <div class="widget-49-date-primary"> ';
                                    }
                                    else if ($row['Status']=="Paid"){
                                        $roomBooking .='   <div class="widget-49-date-success"> ';
                                    }
                                    else if ($row['Status']=="Cancelled"){
                                        $roomBooking .='   <div class="widget-49-date-warning"> ';
                                    } 
                                    else if ($row['Status']=="Rejected"){
                                        $roomBooking .='   <div class="widget-49-date-danger"> ';
                                    } 
                                    //checked Out
                                    else{
                                        $roomBooking .='   <div class="widget-49-date-success"> ';
                                    }
                                    $roomBooking .='  
                                            <span class="widget-49-date-day">'.date('d',strtotime($row['Date'])).'</span>
                                            <span class="widget-49-date-month">'.date('M',strtotime($row['Date'])).'</span>
                                        </div>
                                        <div class="widget-49-meeting-info">
                                       <span class="font-weight-bold text-uppercase">'.$row['RoomType'].'</span> 
                                            <span class="widget-49-meeting-time">Room No: '.$row['RoomNumber'].'</span>
                                            <span class="widget-49-meeting-time">Date : '.$row['Date'].'</span>
                                        </div>
                                    </div>
                                    <ul class="widget-49-meeting-points">
                                        <li class="widget-49-meeting-item"><span class="font-weight-bold ">Check-In Date : '.$row['CheckIn'].'</span></li>
                                        <li class="widget-49-meeting-item"><span class="font-weight-bold ">Check-Out Date : '.$row['CheckOut'].'</span></li>
                                        
                                        <li class="widget-49-meeting-item"><span class="font-weight-bold ">Total Cost : <i class="fa fa-usd" aria-hidden="true"></i>'.$row['Amount'].'</span></li>
                                
                                      
                                        <li class="widget-49-meeting-item"><span>Email : '.$row['Email'].'</span></li>
                                        <li class="widget-49-meeting-item"><span>Phone number : '.$row['Phone_number'].'</span></li>
                                    
                                
                                    </ul>';
                                    if($row['Status']=="Booked"){
                                        $roomBooking .=' <div class="time">
                                        
                                        <a href="#" class="btn btn-danger btn-sm" onclick="confirm(\'Are you sure ? Do you want to Cancel this Booking \') && setCancel(\''.$row["BookingId"].'\')">Cancel</a>
                                        <span class="pull-right">Modified Date : '.$row['Modified_date'].'</span>
                                        </div>	 ';
                                    }
                                    else if ($row['Status']=="Paid"){
                                        $roomBooking .='<form action="../include/pdf.php" method="POST" ><div class="time">
                                        <input type="hidden" value="'.$row['BookingId'].'"  name="bookingId" />
                                        <button type="submit" class="btn btn-primary btn-sm">Bill</button>
                                        <span class="pull-right">Modified Date : '.$row['Modified_date'].'</span>
                                        </div></form> 	 ';
                                    }
                                    else if ($row['Status']=="Cancelled"){
                                        $roomBooking .='       <div class="time">
                                       
                                        <span class="pull-right">Modified Date : '.$row['Modified_date'].'</span>
                                        </div>	';
                                    } 
                                    else if ($row['Status']=="Rejected"){
                                        $roomBooking .='       <div class="time">
                        
                                        <span class="pull-right">Modified Date : '.$row['Modified_date'].'</span>
                                        </div>	';
                                    }
                                    //checked Out
                                    else{
                                        $roomBooking .='<form action="../include/pdf.php" method="POST" ><div class="time">
                                        <input type="hidden" value="'.$row['BookingId'].'"  name="bookingId" />
                                        <button type="submit" class="btn btn-primary btn-sm">Bill</button>
                                        <span class="pull-right">Modified Date : '.$row['Modified_date'].'</span>
                                        </div></form> 	 ';
                                    }
                                    
                 $roomBooking .=' </div>
                            </div>
                        </div>
                    </div>';
  }
}
else 
{

$roomBooking.='</div> <br><br>

     <p class="col-12 text-center text-danger" >No Booked Rooms are available</p>'
    ;

}
echo $roomBooking;

}


// --------------------------------- Events types Available -----------------------------------
if(isset($_POST['eventType'])){
    $TypeCard ='';
    $typeFilter = $_POST['filter'];
    
    switch($typeFilter){
        case 1:  $selectAllType = "select et.*,count(el.EventId) as count_events
                                    from event_type et inner join event_list el on et.EventTypeId = el.EventTypeId 
                                    where el.Status='active' AND et.Status='active'
                                    group by el.EventTypeId "; break;
        case 2:  $selectAllType = "select et.*,count(el.EventId) as count_events
                                    from event_type et inner join event_list el on et.EventTypeId = el.EventTypeId 
                                    where el.Status='active' AND et.Status='active' AND et.Cost <500
                                    group by el.EventTypeId "; break;
        case 3:  $selectAllType = "select et.*,count(el.EventId) as count_events
                                    from event_type et inner join event_list el on et.EventTypeId = el.EventTypeId 
                                    where el.Status='active' AND et.Status='active'  AND (et.Cost >= 500  AND et.Cost <= 1000)
                                    group by el.EventTypeId "; break;
        case 4:  $selectAllType = "select et.*,count(el.EventId) as count_events
                                    from event_type et inner join event_list el on et.EventTypeId = el.EventTypeId 
                                    where el.Status='active' AND et.Status='active' AND et.Cost > 1000  
                                    group by el.EventTypeId "; break;

        default: $selectAllType = "select et.*,count(el.EventId) as count_events
                                    from event_type et inner join event_list el on et.EventTypeId = el.EventTypeId 
                                    where el.Status='active' AND et.Status='active'
                                    group by el.EventTypeId "; break;
        
     }
     $allType = mysqli_query($con,$selectAllType);
     $noOfType = mysqli_num_rows($allType);
 
     if($noOfType>=1){
         while($row=mysqli_fetch_assoc($allType))
         {
             $query_avail = "select count(EventId) as avail_events from event_list where EventTypeId = ' ".$row["EventTypeId"]." ' AND Status = 'active' AND Booking_status = 'Available'";
             $exec_avail = mysqli_query($con,$query_avail);
             $countOfRooms=mysqli_fetch_assoc($exec_avail);

           $TypeCard.=
           '<div class="col-md-4 col-sm-6 ftco-animate fadeInUp ftco-animated">
            <div class="block-7">
            <form action="eventBooking.php" method= "POST">
               <img class="img" src="../assets/picture/EventType/'.$row['EventImage'].'" />
               <div class="text-center p-4">
                   <span class="excerpt d-block">'.$row['EventType'].'</span>
                   <span class="price mb-2"><sup>USD</sup> <span class="number">'.$row['Cost'].'</span> <sub>/per hour</sub></span>
                   <ul class="pricing-text mb-2">';
                   
                       
                   $TypeCard.='<li><span class="fa fa-check" ></span> Facilities: '.$row['Description'].'</li>                      
                  </ul>  ';   
                    $TypeCard.='<input type="hidden" name="eventTypeId" value="'.$row['EventTypeId'].'" />';   

                  
                    $TypeCard.='<button class="btn btn-primary d-block px-1 py-2" value="" name="bookEvent" type="submit">Book</button>';
                  
       
                   
                  
                   $TypeCard.=' </div>
               </form>
           </div>
       </div> ';
         }
       }
     else 
     {
     
       $TypeCard.='<br><br>
      
            <p class="col-12 text-center text-danger" >No Event Types are Available...</p>'
           ;
     
     }

     echo $TypeCard;
}

// ------------------------------------ My Event Booking -------------------------------------
if(isset($_POST['eventBooking'])){

    $eventBooking='<br><br>';
    
    if(isset($_POST['msg'])){ 
      $eventBooking.='<div class="alert alert-success" role="alert">' . $_POST["msg"].' </div>';
    }
    if (isset($_POST["error"])) {
      $eventBooking.='<div class="alert alert-danger">' . $_POST["error"] . '</div>';
    }
    $eventBooking  .='<div class="row">';
    
    $filter = $_POST['filter'];
    $userId = $_SESSION['loggedUserId'];
    switch($filter){
    case 1:  $selectBooking = "SELECT em.*,et.EventType,el.HallNumber FROM
                                event_booking em inner join event_list el on el.EventId = em.EventId
                                inner join event_type et on el.EventTypeId = et.EventTypeId 
                                where em.User_id = '$userId' order by em.Date desc"; break;

    case 2:  $selectBooking =  "SELECT em.*,et.EventType,el.HallNumber FROM
                                event_booking em inner join event_list el on el.EventId = em.EventId
                                inner join event_type et on el.EventTypeId = et.EventTypeId 
                                where em.User_id = '$userId' AND em.Status = 'Booked' order by em.Date desc"; break;

    case 3:  $selectBooking =  "SELECT em.*,et.EventType,el.HallNumber FROM
                                event_booking em inner join event_list el on el.EventId = em.EventId
                                inner join event_type et on el.EventTypeId = et.EventTypeId 
                                where em.User_id = '$userId'
                                AND (em.Status= 'Paid' OR em.Status= 'CheckedOut' )  order by em.Date desc"; break;

    case 4:  $selectBooking =  "SELECT em.*,et.EventType,el.HallNumber FROM
                                event_booking em inner join event_list el on el.EventId = em.EventId
                                inner join event_type et on el.EventTypeId = et.EventTypeId 
                                where em.User_id = '$userId' AND em.Status= 'Cancelled' order by em.Date desc"; break;

    case 5:  $selectBooking =  "SELECT em.*,et.EventType,el.HallNumber FROM
                                event_booking em inner join event_list el on el.EventId = em.EventId
                                inner join event_type et on el.EventTypeId = et.EventTypeId 
                                where em.User_id = '$userId' AND em.Status = 'Rejected' order by em.Date desc"; break;

    case 6:  $selectBooking =  "SELECT em.*,et.EventType,el.HallNumber FROM
                                event_booking em inner join event_list el on el.EventId = em.EventId
                                inner join event_type et on el.EventTypeId = et.EventTypeId 
                                where em.User_id = '$userId' AND em.Event_date < CURDATE() order by em.Date desc"; break;



    default: $selectBooking =  "SELECT em.*,et.EventType,el.HallNumber FROM
                                event_booking em inner join event_list el on el.EventId = em.EventId
                                inner join event_type et on el.EventTypeId = et.EventTypeId 
                                where em.User_id = '$userId' order by em.Date desc"; break;

      
   }
   $all = mysqli_query($con,$selectBooking);
  
   if(mysqli_num_rows($all)>=1){
    while($row=mysqli_fetch_assoc($all))
    {
       
        $eventBooking .='
                      <div id="eventBooking" class="col-lg-4 col-md-6" >
                          <div class="card card-margin">
                              <div class="card-header no-border">
                                  <h5 class="card-title">'.$row['Status'].'</h5>
                              </div>
                              <div class="card-body pt-0">
                                  <div class="widget-49">
                                      <div class="widget-49-title-wrapper">';
                                      if($row['Status']=="Booked"){
                                          $eventBooking .='   <div class="widget-49-date-primary"> ';
                                      }
                                      else if ($row['Status']=="Paid"){
                                          $eventBooking .='   <div class="widget-49-date-success"> ';
                                      }
                                      else if ($row['Status']=="Cancelled"){
                                          $eventBooking .='   <div class="widget-49-date-warning"> ';
                                      } 
                                      else if ($row['Status']=="Rejected"){
                                          $eventBooking .='   <div class="widget-49-date-danger"> ';
                                      } 
                                      //checked Out
                                      else{
                                          $eventBooking .='   <div class="widget-49-date-success"> ';
                                      }
                                      $eventBooking .='  
                                              <span class="widget-49-date-day">'.date('d',strtotime($row['Date'])).'</span>
                                              <span class="widget-49-date-month">'.date('M',strtotime($row['Date'])).'</span>
                                          </div>
                                          <div class="widget-49-meeting-info">
                                         <span class="font-weight-bold text-uppercase">'.$row['EventType'].'</span> 
                                              <span class="widget-49-meeting-time">Hall No: '.$row['HallNumber'].'</span>
                                              <span class="widget-49-meeting-time">Booked Date : '.$row['Date'].'</span>
                                          </div>
                                      </div>
                                      <ul class="widget-49-meeting-points">
                                          <li class="widget-49-meeting-item"><span class="font-weight-bold ">Event Date : '.$row['Event_date'].'</span></li>
                                          <li class="widget-49-meeting-item"><span class="font-weight-bold ">Event Time : '.$row['EventTime'].'</span></li>
                                          <li class="widget-49-meeting-item"><span class="font-weight-bold ">Package Limit : '.$row['Package'].' hrs</span></li>
                                          
                                          <li class="widget-49-meeting-item"><span class="font-weight-bold ">Total Cost : <i class="fa fa-usd" aria-hidden="true"></i>'.$row['Amount'].'</span></li>
                                  
                                        
                                          <li class="widget-49-meeting-item"><span>Email : '.$row['Email'].'</span></li>
                                          <li class="widget-49-meeting-item"><span>Phone number : '.$row['Phone_number'].'</span></li>
                                      
                                  
                                      </ul>';
                                      if($row['Status']=="Booked"){
                                          $eventBooking .=' <div class="time">
                                         
                                          <a href="#" class="btn btn-danger btn-sm" onclick="confirm(\'Are you sure ? Do you want to Cancel this Booking \') && setEventCancel(\''.$row["BookingId"].'\')">Cancel</a>
                                          <span class="pull-right">Modified Date : '.$row['Modified_date'].'</span>
                                          </div>	 ';
                                      }
                                      else if ($row['Status']=="Paid"){
                                          $eventBooking .='<form action="../include/pdf.php" method="POST" ><div class="time">
                                          <input type="hidden" value="'.$row['BookingId'].'"  name="eventBookingId" />
                                          <button type="submit" class="btn btn-primary btn-sm">Bill</button>
                                          <span class="pull-right">Modified Date : '.$row['Modified_date'].'</span>
                                          </div></form> 	 ';
                                      }
                                      else if ($row['Status']=="Cancelled"){
                                          $eventBooking .='       <div class="time">
                                         
                                          <span class="pull-right">Modified Date : '.$row['Modified_date'].'</span>
                                          </div>	';
                                      } 
                                      else if ($row['Status']=="Rejected"){
                                          $eventBooking .='       <div class="time">
                          
                                          <span class="pull-right">Modified Date : '.$row['Modified_date'].'</span>
                                          </div>	';
                                      }
                                      //checked Out
                                      else{
                                          $eventBooking .='<form action="../include/pdf.php" method="POST" ><div class="time">
                                          <input type="hidden" value="'.$row['BookingId'].'"  name="eventBookingId" />
                                          <button type="submit" class="btn btn-primary btn-sm">Bill</button>
                                          <span class="pull-right">Modified Date : '.$row['Modified_date'].'</span>
                                          </div></form> 	 ';
                                      }
                                      
                   $eventBooking .=' </div>
                              </div>
                          </div>
                      </div>';
    }
  }
  else 
  {
  
  $eventBooking.='</div> <br><br>
  
       <p class="col-12 text-center text-danger" >No Booked Events are available</p>'
      ;
  
  }
  echo $eventBooking;
  
  }
  

?>
<script>
    
    function changeImage(element) {
        element.querySelector('.img').src = element.querySelector('.img').getAttribute('data-image1');
    }
    function changeImage(element) {
        element.querySelector('.img').src = element.querySelector('.img').getAttribute('data-image2');
    }

    function restoreImage(element) {
        element.querySelector('.img').src = element.querySelector('.img').getAttribute('data-image');
    }
</script>