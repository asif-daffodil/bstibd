<?php
$conn = mysqli_connect("localhost", "root", "", "bstibd");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" integrity="sha512-SbiR/eusphKoMVVXysTKG/7VseWii+Y3FdHrt0EpKgpToZeemhqHeZeLWLhJutz/2ut2Vw1uQEj2MbRF+TVBUA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css" integrity="sha512-6S2HWzVFxruDlZxI3sXOZZ4/eJ8AcxkQH1+JjSe/ONCEqR9L4Ysq5JdT5ipqtzU7WHalNwzwBv+iE51gNHJNqQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>
    <div class="container">
        <div class="row min-vh-100">
            <div class="col-md-12 m-auto">
                <h2 class="text-center mb-5">Agent Registration</h2>
                <?php
                if (isset($_POST['su'])) {
                    // Retrieve form data
                    $name = $_POST['name'];
                    $email = $_POST['email'];
                    $mobile = $_POST['mobile'];
                    $dob = $_POST['dob'];
                    $address = $_POST['address'];
                    $nid = $_POST['nid'];

                    // Check if email is unique
                    $emailExists = false;
                    $queryEmail = "SELECT * FROM users WHERE email = '$email'";
                    $checkEmail = $conn->query($queryEmail);
                    if ($checkEmail->num_rows > 0) {
                        $emailExists = true;
                    }

                    $mobileExists = false;
                    $queryMobile = "SELECT * FROM users WHERE mobile = '$mobile'";
                    $checkMobile = $conn->query($queryMobile);
                    if ($checkMobile->num_rows > 0) {
                        $mobileExists = true;
                    }

                    if ($emailExists) {
                        echo '<script>toastr.error("Email already exists. Please choose a different email.");</script>';
                    } elseif ($mobileExists) {
                        echo '<script>toastr.error("Mobile number already exists. Please choose a different mobile number.");</script>';
                    } else {
                        // File upload handling
                        $targetDir = "images/"; // Directory where the uploaded files will be stored
                        $fileName = uniqid() . '_' . basename($_FILES["pp"]["name"]); // Add a unique prefix to the filename
                        $targetFilePath = $targetDir . $fileName;
                        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

                        // Check if the file is a valid image
                        $allowedTypes = array("jpg", "jpeg", "png");
                        if (in_array($fileType, $allowedTypes)) {
                            // Move the uploaded file to the target directory
                            if (move_uploaded_file($_FILES["pp"]["tmp_name"], $targetFilePath)) {
                                // Insert query
                                $query = "INSERT INTO `users` (`name`, `email`, `mobile`, `dob`, `address`, `nid`, `pp`) 
                                              VALUES ('$name', '$email', '$mobile', '$dob', '$address', '$nid', '$targetFilePath')";

                                // Execute the query using your database connection
                                // ...
                                $insert = $conn->query($query);
                                // Show success message using Toaster.js
                                if (!$insert) {
                                    echo '<script>toastr.warning("Data inserted failed.");</script>';
                                } else {
                                    echo '<script>toastr.success("Data inserted successfully.");</script>';
                                }
                            } else {
                                echo '<script>toastr.error("Sorry, there was an error uploading your file.");</script>';
                            }
                        } else {
                            echo '<script>toastr.error("Sorry, only JPG, JPEG, and PNG files are allowed.");</script>';
                        }
                    }
                }
                ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" placeholder="Your Name" class="form-control" name="name" required>
                                <label for="">Your Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" placeholder="Email" class="form-control" name="email" required>
                                <label for="">Email</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="tel" placeholder="Mobile" class="form-control" name="mobile" required>
                                <label for="">Mobile</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="date" placeholder="Date of Birth" class="form-control" name="dob" required>
                                <label for="">Date of Birth</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" placeholder="Address" class="form-control" name="address" required>
                                <label for="">Address</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="number" placeholder="NID" class="form-control" name="nid" required>
                                <label for="">NID</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="pp" class="btn btn-success btn-sm">
                                    Upload Your Picture
                                </label>
                                <input type="file" class="d-none" id="pp" name="pp" accept="image/jpeg, image/png" required>
                            </div>
                            <div class="mb-3">
                                <input type="submit" class="btn btn-primary" value="Sign Up" name="su">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js" integrity="sha512-i9cEfJwUwViEPFKdC1enz4ZRGBj8YQo6QByFTF92YXHi7waCqyexvRD75S5NVTsSiTv7rKWqG9Y5eFxmRsOn0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>

</html>