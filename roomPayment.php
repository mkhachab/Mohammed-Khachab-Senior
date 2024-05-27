<?php include("include/header.php"); 
if(!isset($_SESSION['loggedUserId'])) {
  echo "<script> window.location.href = '../login.php';</script>";
}
?>
<!-- Page Content  -->
<div id="content" class="p-4 p-md-5 pt-5">

<h2 class="mb-4">Room Booking Payment</h2>


<br>
 <!-- Filter Drop down  -->
 <div class="float-right filterBy">
<select name="category" id="roomPaymentFilter" class="form-control custom-select bg-white border-md filter">
  <option disabled="" selected="">FilterBy  </option>
  <option value="1">All</option>
  
  <option value="6">Less than 50</option>
  <option value="7">between 50and 100</option>
  <option value="8">between 100 and 150</option>
  <option value="9">Above 150</option>
</select>
</div>
 <!-- table for the display the content  -->
 <div class="container-fluid" id="contentArea">

        
</div>


</div>
<script src="js/roomPayment.js"></script>
<?php include("include/footer.php"); ?>

