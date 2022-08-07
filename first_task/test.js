$(document).ready(function(){
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
    let gradePromise = getGrades();
    gradePromise.then((result)=>{
        console.log(gradesArray)
    })
});