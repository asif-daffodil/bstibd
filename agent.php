<?php
$conn = mysqli_connect("localhost", "root", "", "bstibd");

function sefuda($data)
{
    $data = htmlspecialchars($data);
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}
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
                    $name = sefuda($_POST['name']);
                    $email = sefuda($_POST['email']);
                    $mobile = sefuda($_POST['mobile']);
                    $dob = sefuda($_POST['dob']);
                    $dobArr = explode("-", $dob);
                    $address = sefuda($_POST['address']);
                    $nid = sefuda($_POST['nid']);

                    if (empty($name)) {
                        $errName = "Please write your name";
                    } elseif (!preg_match("/^[A-Za-z. ]*$/", $name)) {
                        $errName = "Invalid name";
                    } else {
                        $crrName = $conn->real_escape_string($name);
                    }

                    if (empty($email)) {
                        $errEmail = "Please write your email";
                    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errEmail = "Invalid email address";
                    } else {
                        $queryEmail = "SELECT * FROM users WHERE email = '$email'";
                        $checkEmail = $conn->query($queryEmail);
                        if ($checkEmail->num_rows > 0) {
                            $errEmail = "Email address already exicts";
                        } else {
                            $crrEmail = $conn->real_escape_string($email);
                        }
                    }

                    if (empty($mobile)) {
                        $errMobile = "Please provide your mobile number";
                    } elseif (!is_numeric($mobile)) {
                        $errMobile = "Invalid mobile number";
                    } else {
                        $queryMobile = "SELECT * FROM users WHERE mobile = '$mobile'";
                        $checkMobile = $conn->query($queryMobile);
                        if ($checkMobile->num_rows > 0) {
                            $errMobile = "Mobile number already exicts";
                        } else {
                            $crrMobile = $conn->real_escape_string($mobile);
                        }
                    }

                    if (empty($dob)) {
                        $errDob = "Please provide your date of birth";
                    } elseif (!checkdate($dobArr[2], $dobArr[1], $dobArr[0])) {
                        $errDob = "Invalid Date formate";
                    } else {
                        $crrDob = $conn->real_escape_string($dob);
                    }

                    if (empty($address)) {
                        $errAddress = "Please provide your address";
                    } elseif (is_numeric($address)) {
                        $errAddress = "Invalid address";
                    } else {
                        $crrAddress = $conn->real_escape_string($address);
                    }

                    if (empty($nid)) {
                        $errNid = "Please provide National ID Number";
                    } elseif (!is_numeric($nid)) {
                        $errNid = "Invalid NID";
                    } else {
                        $crrNid = $conn->real_escape_string($nid);
                    }

                    if (isset($crrAddress) && isset($crrDob) && isset($crrEmail) && isset($crrMobile) && isset($crrName) && isset($crrNid)) {
                        if (!empty($_FILES["pp"]["name"])) {
                            $targetDir = "images/";
                            $fileName = uniqid() . '_' . basename($_FILES["pp"]["name"]);
                            $targetFilePath = $targetDir . $fileName;
                            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                            $allowedTypes = array("jpg", "jpeg", "png");
                            if (in_array($fileType, $allowedTypes)) {
                                if (move_uploaded_file($_FILES["pp"]["tmp_name"], $targetFilePath)) {
                                    $query = "INSERT INTO `users` (`name`, `email`, `mobile`, `dob`, `address`, `nid`, `pp`) 
                                              VALUES ('$name', '$email', '$mobile', '$dob', '$address', '$nid', '$targetFilePath')";
                                    $insert = $conn->query($query);
                                    if (!$insert) {
                                        echo '<script>toastr.warning("Data inserted failed.");</script>';
                                    } else {
                                        echo '<script>toastr.success("Data inserted successfully.");</script>';
                                        $name = $email = $mobile = $dob = $address = $nid = null;
                                    }
                                } else {
                                    $errImg = "Image upload failed";
                                }
                            } else {
                                $errImg = "Invalid Image format";
                            }
                        } else {
                            $errImg = "Please upload your image";
                        }
                    }
                }
                ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" placeholder="Your Name" class="form-control <?= isset($errName) ? 'is-invalid' : null ?>" name="name" value="<?= $name ?? null ?>">
                                <label for="">Your Name</label>
                                <div class="invalid-feedback">
                                    <?= $errName ?? null ?>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" placeholder="Email" class="form-control <?= isset($errEmail) ? 'is-invalid' : null ?>" name="email" value="<?= $email ?? null ?>">
                                <label for="">Email</label>
                                <div class="invalid-feedback">
                                    <?= $errEmail ?? null ?>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="tel" placeholder="Mobile" class="form-control  <?= isset($errMobile) ? 'is-invalid' : null ?>" name=" mobile" value="<?= $mobile ?? null ?>">
                                <label for="">Mobile</label>
                                <div class="invalid-feedback">
                                    <?= $errMobile ?? null ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="date" placeholder="Date of Birth" class="form-control <?= isset($errDob) ? 'is-invalid' : null ?>" name="dob" value="<?= $dob ?? null ?>">
                                <label for="">Date of Birth</label>
                                <div class="invalid-feedback">
                                    <?= $errDob ?? null ?>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" placeholder="Address" class="form-control <?= isset($errAddress) ? 'is-invalid' : null ?>" name="address" value="<?= $address ?? null ?>">
                                <label for="">Address</label>
                                <div class="invalid-feedback">
                                    <?= $errAddress ?? null ?>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="number" placeholder="NID" class="form-control <?= isset($errNid) ? 'is-invalid' : null ?>" name="nid" value="<?= $nid ?? null ?>">
                                <label for="">NID</label>
                                <div class="invalid-feedback">
                                    <?= $errNid ?? null ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="pp" class="btn btn-success btn-sm ">
                                    Upload Your Picture
                                </label>
                                <input type="file" class="d-none <?= isset($errImg) ? "is-invalid" : null ?>" id="pp" name="pp" accept="image/jpeg, image/png">
                                <div class="invalid-feedback">
                                    <?= $errImg ?? null ?>
                                </div>
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