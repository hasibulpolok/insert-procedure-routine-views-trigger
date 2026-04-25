<?php
$conn = mysqli_connect('localhost', 'root', '', 'newevidance');

$message = "";

if (isset($_POST['submit'])) {

   $name = $_POST['mname'];
   $address = $_POST['address'];
   $contact = $_POST['contact'];

   $query = "CALL manufacturer_name('$name','$address','$contact')";

   if ($conn->query($query)) {
      $message = "<div class='alert alert-success'>Data inserted successfully!</div>";
   } else {
      $message = "<div class='alert alert-danger'>Insert failed!</div>";
   }

   $conn->next_result();
}
?>

<!DOCTYPE html>
<html>
<head>
   <title>Manufacturer Form</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container mt-5">
   <div class="col-md-6 offset-md-3 border p-4">

      <h3>Insert Manufacturer</h3>

      

      <form method="POST">

         Name:<br>
         <input type="text" name="mname" class="form-control" required><br>

         Address:<br>
         <input type="text" name="address" class="form-control" required><br>

         Contact:<br>
         <input type="text" name="contact" class="form-control" required><br>

         <input type="submit" name="submit" value="Insert" class="btn btn-success">

      </form>
      <?php echo $message; ?>

   </div>
</div>
</body>
</html>