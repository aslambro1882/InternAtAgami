<?php
require_once("php/db/Database.php");

$db = new Database();
$dbcon = $db->db_connect();

if (!$db->is_connected()) {
    $response['error'] = true;
    $response['message'] = "Database is not connected!";
    echo json_encode($response);
    exit();
}

//list of district
$sql = "SELECT * FROM loc_district";
$stmt = $dbcon->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$districtArray = array();
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $districtArray[] = $row;
}
$dbcon->close();

// var_dump($districtArray);

// print_r($districtArray);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">

    <!-- JS CDN -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


    <!-- jquery CDN -->
    <script src="https://code.jquery.com/jquery-3.5.0.js" integrity="sha256-r/AaFHrszJtwpe+tHyNi/XCfMxYpbsRg2Uqn0x3s2zc=" crossorigin="anonymous"></script>

    <!-- Select2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

    <title>Post Office CRUD</title>
</head>

<body>
    <div class="d-flex justify-content-between container my-5">

        <div class="form-group col-md-4">
            <label for="limit">Page Limit</label>
            <select class="custom-select w-50" id="limit">
                <option selected value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <button class="btn btn-primary" id="filter">Filter</button>
        </div>


        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addDataModal">ADD</button>
    </div>

    <div class="container p-3 mb-5 bg-white rounded">

        <table id="post-table" class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Post Code</th>
                    <th scope="col">Post Office</th>
                    <th scope="col">Police Station</th>
                    <th scope="col">District</th>
                    <th scope="col">Is City</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
        <div class="d-flex justify-content-between">
            <button class="btn btn-primary" id="pagePrevButton">prev</button>
            <div>
                <input type="text" class="text-center" id="pageInput">
                <button class="btn btn-info" id="pageNo">Page: 1</button>
            </div>
            <button class="btn btn-primary" id="pageNextButton">next</button>
        </div>

    </div>

    <!-- Post Data Modal Starts here -->
    <div class="modal fade bd-example-modal-lg" id="addDataModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-lg" role="document">
            <div class="modal-content">
                <form id="postCodeSetupForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="postcodetitle">Add Postcode</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-sm">
                                    <label for="postcode">Postcode *</label>
                                    <div class="input-group mb-3">
                                        <input name="postcode" type="number" class="form-control" id="postcode" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <label for="postoffice">Post Office *</label>
                                    <div class="input-group mb-3">
                                        <input name="po" type="text" class="form-control" id="postoffice" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <label for="policestation">Police Station *</label>
                                    <div class="input-group mb-3">
                                        <input name="ps" type="text" class="form-control" id="policestation" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="input-group-prepend">
                                        <label for="district">District *</label>
                                    </div>
                                    <select name="districtno" class="custom-select" id="district">
                                        <option selected>Choose...</option>
                                        <?php
                                        foreach ($districtArray as $district) {
                                            echo "<option value=" . $district['districtno'] . ">" . $district['districtname'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="input-group-prepend">
                                        <label for="iscity">Is City *</label>
                                    </div>
                                    <select name="iscity" class="custom-select" id="iscity">
                                        <option selected>Choose...</option>
                                        <option value="0">Not City</option>
                                        <option value="1">City</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Post Data Modal Starts here -->




    <script src="myScript.js"></script>
</body>

</html>