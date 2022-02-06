<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$student_id = $name = $subject = $score = "";
$student_id_err = $name_err = $subject_err = $score_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
    // Validate name
    $input_student_id = trim($_POST["student_id"]);
    if(empty($input_student_id)){
        $student_id_err = "Please enter the student_id.";     
    } elseif(!ctype_digit($input_student_id)){
        $student_id_err = "Please enter a positive integer value.";
    } else{
        $student_id = $input_student_id;
    }


    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
        $name_err = "Please enter a name.";
    } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $name_err = "Please enter a valid name.";
    } else{
        $name = $input_name;
    }
    
    // Validate address subject
    $input_subject = trim($_POST["subject"]);
    if(empty($input_subject)){
        $subject_err = "Please enter a name.";
    } elseif(!filter_var($input_subject, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $subject_err = "Please enter a valid name.";
    } else{
        $subject= $input_subject;
    }



    
    // Validate score
    $input_score = trim($_POST["score"]);
    if(empty($input_score)){
        $score_err = "Please enter the score .";     
    } elseif(!ctype_digit($input_score)){
        $score_err = "Please enter a positive integer value.";
    } else{
        $score = $input_score;
    }
    
    // Check input errors before inserting in database
    if(empty($student_id_err) && empty($name_err) && empty($subject_err) && empty($score_err)){
        // Prepare an update statement
        $sql = "UPDATE employees SET student_id=?, name=?, subject=?, score=? WHERE id=?";
        #$sql = $link->prepare("UPDATE employees SET student_id=?, name=?, subject=?, score=? WHERE id=?");

         if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
           # mysqli_stmt_bind_param($stmt, "issii",$param_student_id, $param_name, $param_subject, $param_score, $param_id);
            mysqli_stmt_bind_param($stmt, "issii",$param_student_id, $param_name, $param_subject, $param_score, $param_id);
            #$stmt->bind_param("issii",$param_student_id,  $param_name, $param_subject, $param_score); 
            // Set parameters
            $param_student_id = $student_id;
            $param_name = $name;
            $param_subject = $subject;
            $param_score = $score;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
   }
   else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM employees WHERE id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $student_id= $row["student_id"];
                    $name = $row["name"];
                    $address = $row["subject"];
                    $salary = $row["score"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($link);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Update Record</h2>
                    <p>Please edit the input values and submit to update the student record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>student_id</label>
                            <input type="text" name="student_id" class="form-control <?php echo (!empty($student_id_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $student_id; ?>">
                            <span class="invalid-feedback"><?php echo $student_id_err;?></span>
                        </div> 
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <span class="invalid-feedback"><?php echo $name_err;?></span>
                        </div>
                        
                        <div class="form-group">
                            <label>subject</label>
                            <input type="text" name="subject" class="form-control <?php echo (!empty($subject_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $subject; ?>">
                            <span class="invalid-feedback"><?php echo $subject_err;?></span>
                        </div>

                        <div class="form-group">
                            <label>score</label>
                            <input type="text" name="score" class="form-control <?php echo (!empty($score_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $score; ?>">
                            <span class="invalid-feedback"><?php echo $score_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>