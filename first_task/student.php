<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
    <title>Document</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha256-ENFZrbVzylNbgnXx0n3I1g//2WeO47XxoPe0vkp3NC8=" crossorigin="anonymous" />

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha256-3blsJd4Hli/7wCQ+bmgXfOdK7p/ZUMtPXY08jmxSSgk=" crossorigin="anonymous"></script>


    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    </script>
</head>

<body>
    <div class="container py-3">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h5 class="font-weight-bold">Student</h5>
                <button id="add_student_button" class="btn btn-primary" type="button">
                    <i class="fas fa-plus-circle mr-2"></i> Add
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="table-primary">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="student_tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="setup_student_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="setup_student_modal_form">
                    <div class="modal-header">
                        <h5 class="modal-title">Student Setup</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="d-block mb-0">
                                ID <i class="fa fa-star-of-life small text-danger"></i>
                                <input name="stdid" class="form-control shadow-sm mt-2" type="text" placeholder="ID..." required>
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="d-block mb-0">
                                Name <i class="fa fa-star-of-life small text-danger"></i>
                                <input name="name" class="form-control shadow-sm mt-2" type="text" placeholder="Name..." required>
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="d-block mb-0">
                                Department <i class="fa fa-star-of-life small text-danger"></i>
                                <select name="deptid" class="form-control shadow-sm mt-2" required></select>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        getDepartment();

        function getDepartment() {
            let resp = {
                error: false,
                data: [{
                        deptid: "1",
                        depttitle: "CSE"
                    },
                    {
                        deptid: "2",
                        depttitle: "EEE"
                    },
                    {
                        deptid: "3",
                        depttitle: "APECE"
                    },
                    {
                        deptid: "4",
                        depttitle: "BBA"
                    }
                ]
            };

            if (resp.error) {
                toastr.error(resp.message);
            } else {
                // $(`#student_tbody`).empty();
                show_department(resp.data);
            }
        }

        function show_department(data) {
            $(`#setup_student_modal_form [name="deptid"]`).append(new Option("Select Department", ""));
            $.each(data, (index, value) => {
                $(`#setup_student_modal_form [name="deptid"]`).append(new Option(value.depttitle, value.deptid));
            });
        }

        get_student();

        function get_student() {
            let resp = {
                error: false,
                data: [{
                        id: "1",
                        stdid: "1",
                        name: "Mr. ABC1",
                        deptid: "1",
                        depttitle: "CSE"
                    },
                    {
                        id: "2",
                        stdid: "2",
                        name: "Mr. ABC2",
                        deptid: "3",
                        depttitle: "APECE"
                    },
                    {
                        id: "3",
                        stdid: "3",
                        name: "Mr. ABC3",
                        deptid: "2",
                        depttitle: "EEE"
                    },
                    {
                        id: "4",
                        stdid: "4",
                        name: "Mr. ABC4",
                        deptid: "1",
                        depttitle: "CSE"
                    }
                ]
            };

            if (resp.error) {
                toastr.error(resp.message);
            } else {
                $(`#student_tbody`).empty();
                show_student(resp.data);
            }
        }

        function show_student(data) {
            $.each(data, (index, value) => {
                let row = $(`<tr>
                        <td>${value.stdid}</td>
                        <td>${value.name}</td>
                        <td>${value.depttitle}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <button class="edit_button btn btn-info rounded-circle p-2 m-1" type="button">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="delete_button btn btn-danger rounded-circle p-2 m-1" type="button">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`)
                    .appendTo(`#student_tbody`);

                (function($) {
                    $(`.edit_button`, row).click(function(e) {
                        $(`#setup_student_modal`).modal("show");

                        let form = $(`#setup_student_modal_form`);
                        $(`[name="stdid"]`, form).val(value.stdid);
                        $(`[name="name"]`, form).val(value.name);
                        $(`[name="deptid"]`, form).val(value.deptid);
                        form.data(`id`, value.id);
                    });

                    $(`.delete_button`, row).click(function(e) {
                        if (confirm("Are you sure?")) {
                            delete_student({
                                id: value.id
                            });
                        }
                    });
                })(jQuery);
            });
        }

        $(`#add_student_button`).click(function(e) {
            $(`#setup_student_modal`).modal("show");
            $(`#setup_student_modal_form`).trigger("reset").data("id", -1);
        });

        $(`#setup_student_modal_form`).submit(function(e) {
            e.preventDefault();
            let json = Object.fromEntries((new FormData(this)).entries());

            let id = Number($(this).data("id"));
            let url = ``;
            if (id > 0) {
                json.id = id;
            }
            console.log(`json =>`, json);

            $.post(`php/ui/`, json, resp => {
                if (resp.error) {
                    toastr.error(resp.message);
                } else {
                    toastr.success(resp.message);
                    get_student();
                }
            }, `json`);
        });

        function delete_student(json) {
            $.post(`php/ui/`, json, resp => {
                if (resp.error) {
                    toastr.error(resp.message);
                } else {
                    toastr.success(resp.message);
                    get_student();
                }
            }, `json`);
        }
    </script>
</body>

</html>