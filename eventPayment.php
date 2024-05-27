<?php include("include/header.php");
if(!isset($_SESSION['loggedUserId'])) {
  echo "<script> window.location.href = '../login.php';</script>";
}
 ?>
<!-- Page Content  -->
<div id="content" class="p-4 p-md-5 pt-5">

<h2 class="mb-4">Event Booking Payment</h2>


<br>
 <!-- Filter Drop down  -->
 <div class="float-right filterBy">
<select name="category" id="eventPaymentFilter" class="form-control custom-select bg-white border-md filter">
  <option disabled="" selected="">FilterBy  </option>
  <option value="1">All</option>


  <option value="6">Less than 500</option>
  <option value="7">between 500 and 1000</option>
  <option value="9">Above 1000</option>
</select>
</div>
 <!-- table for the display the content  -->
 <div class="container-fluid" id="contentArea">

        
</div>


</div>
<script src="js/eventPayment.js"></script>
<?php include("include/footer.php"); ?>

