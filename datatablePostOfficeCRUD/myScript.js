$("#pageInput").hide();

$(`#postCodeSetupForm`).submit(function (e) {
    e.preventDefault();
    var json = Object.fromEntries(new FormData(this).entries());
    // console.log(json);
    $.ajax({
        url: "php/ui/settings/postOffice/setup_postoffice.php",
        method: "POST",
        dataType: "json",
        data: {
            data: JSON.stringify([json]),
        },
        success: function (response) {
            if (response.error) {
                alert(response.message);
            } else {
                console.log(response);
            }
        },
    });
});

function postDataToDBAjax() {}

// Table Data from backend
// var postOfficeData;
function getpostajax(pageNo, pageLimit) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "./php/ui/settings/postOffice/get_postoffice.php",
            method: "POST",
            dataType: "json",
            data: {
                pageno: pageNo,
                limit: pageLimit,
            },
            success: function (data) {
                resolve(data);
            },
            error: () => reject(),
        });
    });
}
var pageNo = 1;
var pageLimit;
let sl = 1;

let postOfficeDataPromise = getpostajax(1, 10);
postOfficeDataPromise.then((postOfficeData) => {
    // console.log(postOfficeData.data);
    displayTable(postOfficeData);
});
// displaying data here
function displayTable(postOfficeData) {
    let tableBody = $(`#post-table tbody`);
    $(tableBody).empty();

    $.each(postOfficeData.data, (index, singleData) => {
        let tr = $(`<tr>`).data("postCodeData", singleData).append(`
                    <th>${index + 1}</th>
                    <td>${singleData.postcode}</td>
                    <td>${singleData.po}</td>
                    <td>${singleData.ps}</td>
                    <td>${singleData.districtname}</td>
                    <td>${singleData.iscity == 1 ? `City` : `Not City`}</td>
                    <td>
                        <button type="button" class="btn btn-primary btn-sm updatePostCodeButton" data-toggle="modal" data-target="#addDataModal">E</button>
                        <button type="button" class="btn btn-danger btn-sm deletePostCodeButton">X</button>
                    </td>`);

        tableBody.append(tr);

        (function ($) {
            // update function-----------------
            $(`.updatePostCodeButton`, tableBody).click(function (e) {
                let rowData = $(this.closest("tr")).data("postCodeData");
                $(`#postcodetitle`).empty();
                // console.log("here", rowData);

                $(`#postcode`).val(rowData.postcode);
                $(`#postoffice`).val(rowData.po);
                $(`#policestation`).val(rowData.ps);
                $(`#district`).val(rowData.districtno);
                $(`#iscity`).val(rowData.iscity);
                $(`#postcodetitle`).append("Update Postcode");

                $(`#postCodeSetupForm`).submit(function (e) {
                    e.preventDefault();
                    var json = Object.fromEntries(new FormData(this).entries());
                    // console.log(json);
                    $.ajax({
                        url: "php/ui/settings/postOffice/setup_postoffice.php",
                        method: "POST",
                        dataType: "json",
                        data: {
                            data: JSON.stringify([json]),
                        },
                        success: function (response) {
                            if (response.error) {
                                alert(response.message);
                            } else {
                                console.log(response);
                                let postOfficeDataPromise = getpostajax(1, 10);
                                postOfficeDataPromise.then((postOfficeData) => {
                                    // console.log(postOfficeData.data);
                                    displayTable(postOfficeData);
                                });
                            }
                        },
                    });
                });
            });

            // delete function-----------------
            $(`.deletePostCodeButton`, tableBody).click(function (e) {
                if (confirm("Are you sure want to delete this ?") == true) {
                    let rowData = $(this.closest("tr")).data("postCodeData");
                    // console.log(rowData);
                    $.ajax({
                        url: "php/ui/settings/postOffice/delete_postoffice.php",
                        method: "POST",
                        dataType: "json",
                        data: {
                            postcode: rowData.postcode,
                            pageno: pageNo,
                            limit: pageLimit,
                        },
                        success: function (response) {
                            if (response.error) {
                                alert(response.message);
                            } else {
                                console.log(response);
                                let postOfficeDataPromise = getpostajax(1, 10);
                                postOfficeDataPromise.then((postOfficeData) => {
                                    // console.log(postOfficeData.data);
                                    displayTable(postOfficeData);
                                });
                            }
                        },
                    });
                }
            });
        })(jQuery);
    });

    $("#post-table").DataTable({
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],
        paging: false,
        // sDom: 'T<"clear">lfrtip',
        // oTableTools: {
        //     aButtons: [
        //         {
        //             sExtends: ["pdf", "xls"],
        //             sButtonText: "Save a CSV file!",
        //         },
        //     ],
        // },
    });
}

$(`#pageNo`).on("click", function () {
    $(this).hide();
    $("#pageInput").show();
    $("#pageInput").val(pageNo);
});

$("#filter").on("click", function () {
    pageLimit = $("#limit").val();
    pageNo = 1;
    $("#pageNo").empty();
    $("#pageNo").append(`Page: ${pageNo}`);
    sl = (parseInt(pageNo) - 1) * parseInt(pageLimit) + 1;
    console.log(sl);

    let postOfficeDataPromise = getpostajax(pageNo, pageLimit);
    postOfficeDataPromise.then((postOfficeData) => {
        // console.log(postOfficeData.data);
        displayTable(postOfficeData);
    });
});

$(`#pageInput`).on("keypress", function (e) {
    // console.log(this.value);
    pageNo = this.value;
    if (e.key == "Enter") {
        $("#pageNo").empty();
        $(this).hide();
        $("#pageNo").show();
        $("#pageNo").append(`Page: ${pageNo}`);

        let postOfficeDataPromise = getpostajax(pageNo, pageLimit);
        postOfficeDataPromise.then((postOfficeData) => {
            // console.log(postOfficeData.data);
            displayTable(postOfficeData);
        });
    }
});

// if (pageNo > 1) {
$("#pagePrevButton").on("click", function () {
    if (pageNo > 1) {
        $("#pageNo").empty();
        pageNo--;
        // console.log(pageNo);
        $("#pageNo").append(`Page: ${pageNo}`);

        let postOfficeDataPromise = getpostajax(pageNo, pageLimit);
        postOfficeDataPromise.then((postOfficeData) => {
            // console.log(postOfficeData.data);
            displayTable(postOfficeData);
        });
    }
});
// }
$("#pageNextButton").on("click", function () {
    $("#pageNo").empty();
    pageLimit = $("#limit").val();
    pageNo++;
    // console.log(pageNo);
    $("#pageNo").append(`Page: ${pageNo}`);

    let postOfficeDataPromise = getpostajax(pageNo, pageLimit);
    postOfficeDataPromise.then((postOfficeData) => {
        // console.log(postOfficeData.data);
        displayTable(postOfficeData);
    });
});
