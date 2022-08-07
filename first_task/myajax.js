$(document).ready(function () {
    // Ajax call for grades ------------
    var gradesArray;
    function getGrades() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "grades.json",
                method: "GET",
                dataType: "json",
                success: function (grades) {
                    gradesArray = grades;
                    resolve();
                },
                error: () => reject(),
            });
        });
    }

    // Ajax call for Splits ------------
    var splitsArray;
    function getSplits() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "splits.json",
                method: "GET",
                dataType: "json",
                success: function (splits) {
                    splitsArray = splits;
                    resolve();
                },
                error: () => reject(),
            });
        });
    }

    // Ajax call for courses ------------
    var coursesArray;
    function getCourses() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "courses.json",
                method: "GET",
                dataType: "json",
                success: function (courses) {
                    coursesArray = courses;
                    resolve();
                },
                error: () => reject(),
            });
        });
    }

    // Ajax call for students ------------
    var studentObj;
    function getStudent() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "student.json",
                method: "GET",
                dataType: "json",
                success: function (student) {
                    studentObj = student;
                    resolve();
                },
                error: () => reject(),
            });
        });
    }

    let gradePromise = getGrades();
    gradePromise
        .then((result) => {
            let splitPromise = getSplits();
            splitPromise
                .then((result) => {
                    let coursePromise = getCourses();
                    coursePromise
                        .then((result) => {
                            let studentPromise = getStudent();
                            studentPromise
                                .then((result) => {
                                    showTableData(
                                        gradesArray,
                                        splitsArray,
                                        coursesArray,
                                        studentObj
                                    );
                                })
                                .catch((err) => {});
                        })
                        .catch((err) => {});
                })
                .catch((err) => {});
        })
        .catch((err) => {});

    // Function for Grade Point ------------
    function getGP(mark, gradesArray) {
        for (let grade of gradesArray) {
            if (mark >= 0 && mark <= 100) {
                if (mark < grade.upper && mark >= grade.lower) {
                    return parseFloat(grade.gradepoint).toFixed(2);
                }
            } else {
                return "-";
            }
        }
    }

    // Function for Latter Grade---------
    function getLG(gradesArray, GPAHTML) {
        for (let grade of gradesArray) {
            console.log(
                parseFloat(GPAHTML.toFixed(2)),
                ">",
                parseFloat(grade.gradepoint)
            );
            if (
                parseFloat(GPAHTML.toFixed(2)) >= parseFloat(grade.gradepoint)
            ) {
                return grade.lettergrade;
            }
        }
    }

    // Function for Result ------------
    function getResult(LGHTML) {
        if (LGHTML == "F") {
            return "Fail";
        } else {
            return "Pass";
        }
    }

    function showTableData2(
        gradesArray,
        splitsArray,
        coursesArray,
        studentObj
    ) {
        let template = $(`<div class="table-responsive">
					<table
						class="transcript_table table table-bordered table-striped table-hover table-light">
						<thead>
							<tr>	
								<th></th>
								${splitsArray.map((a) => `<th>${a.splittitle}</th>`).join("")}
								<th>GP</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>`).appendTo(`#result_container`);

        $.each(coursesArray, (indexInCourse, valueOfCourse) => {
            let row = $(`<tr>
					<th>${valueOfCourse.coursetitle}</th>
				</tr>`);

            $(`.transcript_table tbody`, template).append(row);

            $.each(splitsArray, (indexInSplit, valueOfSplit) => {
                let markObj = studentObj.marks.find(
                    (a) =>
                        a.coursecode == valueOfCourse.coursecode &&
                        a.splitno == valueOfSplit.splitno
                );
                let mark;

                if (markObj && markObj.marks) {
                    mark = Math.ceil(markObj.marks);
                    row.append(`<td>${mark}</td>`);
                } else {
                    row.append(`<td>-</td>`);
                }

                if (valueOfSplit.splittype == `TOTAL`) {
                    row.append(`<td>${getGP(mark, gradesArray)}</td>`);
                }
            });
        });
    }

    function showTableData(
        gradesArray,
        splitsArray,
        coursesArray,
        studentArray
    ) {
        $.each(studentArray, (indexInStudent, valueOfStudent) => {
            let failedCourses = [];
            let template = $(`
            <div class="d-flex justify-content-center align-items-center">
                <div class="table-responsive my-5 w-75">
                    <div class="container text-center">
                        
                        <h3 class="fw-bold">MAHILA COLLEGE CHATTOGRAM</h3>
                        <p>49, Enayet Bazar, Chattogram</p>
                        <h3 class="btn btn-info fw-bold btn-lg">Academic
                            Transcript
                        </h3>
                        <h5>${valueOfStudent.name_en}</h5>
                        <p>HSC (Humanities), 1st Year-${
                            valueOfStudent.academicyear
                        }, ID: ${valueOfStudent.stdid},
                            Sesstion: ${valueOfStudent.academicsession}</p>
                    </div>
                    <table class="student-table table table-bordered table-striped table-hover table-light">
                        <thead>
                            <tr>
                                <th></th>
                                ${splitsArray
                                    .map(
                                        (split) =>
                                            `<th class="text-center">${split.splittitle}</th>`
                                    )
                                    .join("")}
                                <th class="text-center">GP</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-around">
                        <div>
                            <table
                                class="table table-bordered table-striped table-hover table-light">
                                <thead>
                                    <tr>
                                        <td></td>
                                        <td class="text-danger">Exams</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Total Marks</td>
                                        <td id="TM"></td>
                                    </tr>
                                    <tr>
                                        <td>GPA</td>
                                        <td id="GPA"></td>
                                    </tr>
                                    <tr>
                                        <td>LG</td>
                                        <td id="LG"></td>
                                    </tr>
                                    <tr>
                                        <td>Result</td>
                                        <td id="result"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div>
                            <table
                                class="failed-list table table-bordered table-striped table-hover table-light">
                                <thead>
                                    <tr>
                                        <th>Failed in:</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <p class="text-center">Supported by: AGAMiLabs Ltd. (www.agamilabs.com)
                        </p>
                </div>
            </div>
            `).appendTo(`#result_container1`);
            let isPass = true;
            let totalMark = 0;
            let totalGP = 0;
            let GPA;
            let result;
            let LG;

            $.each(coursesArray, (indexInCourse, valueOfCourse) => {
                isPass = true;
                let row = $(`
                <tr>
                    <th>${valueOfCourse.coursetitle}</th>
                </tr>
                `);
                $(`.student-table tbody`, template).append(row);

                $.each(splitsArray, (indexInSplit, valueOfSplit) => {
                    let markObj = valueOfStudent.marks.find(
                        (mark) =>
                            mark.coursecode == valueOfCourse.coursecode &&
                            mark.splitno == valueOfSplit.splitno
                    );
                    let mark;
                    if (markObj && markObj.marks) {
                        mark = Math.ceil(markObj.marks);
                        if (mark < markObj.min_to_pass) {
                            // console.log("hello");
                            isPass = false;
                            row.append(
                                `<td class="text-danger text-center">${mark}</td>`
                            );
                            if (
                                valueOfCourse.coursecode == markObj.coursecode
                            ) {
                                failedCourses.push(valueOfCourse.coursetitle);
                            }
                        } else {
                            row.append(`<td class="text-center">${mark}</td>`);
                            if (valueOfSplit.splittitle == `Exam Total`) {
                                totalMark += mark;
                            }
                        }
                    } else {
                        row.append(`<td class="text-center">-</td>`);
                    }

                    if (isPass) {
                        if (valueOfSplit.splittype == `TOTAL`) {
                            row.append(
                                `<td class="text-center">${getGP(
                                    mark,
                                    gradesArray
                                )}</td>`
                            );
                            if (getGP(mark, gradesArray) > 0) {
                                totalGP += parseFloat(getGP(mark, gradesArray));
                            }
                        }
                    } else {
                        if (valueOfSplit.splittype == `TOTAL`) {
                            row.append(`<td class="text-center">0.00</td>`);
                        }
                    }
                });
            });
            console.log(failedCourses);

            GPA = totalGP / coursesArray.length;
            $.each(gradesArray, (indexInGrades, valueOfGrades) => {
                console.log(GPA, valueOfGrades.gradepoint);
                if (GPA >= parseFloat(valueOfGrades.gradepoint)) {
                    LG = valueOfGrades.lettergrade;
                    console.log(valueOfGrades.gradepoint);
                    return false;
                }
            });
            // console.log(totalGP);
            if (LG == "F") {
                result = "Fail";
            } else {
                result = "Pass";
            }
            $(`#TM`, template).append(totalMark);
            $(`#GPA`, template).append(GPA.toFixed(2));
            $(`#LG`, template).append(LG);
            $(`#result`, template).append(result);
            console.log("Table loop end here ");

            $.each(
                failedCourses,
                (indexInFailedCourses, valueOfFailedCourses) => {
                    console.log(valueOfFailedCourses);
                    let row = $(`
                    
                    <tbody>
                        <tr>
                            <td>${valueOfFailedCourses}</td>
                        </tr>
                    </tbody>
                    `);
                    $(`.failed-list`, template).append(row);
                }
            );
        });
        console.log("total mark:", totalMark);
    }
});
//
