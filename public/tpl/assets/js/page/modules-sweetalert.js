"use strict";

jq("#swal-1").click(function() {
	swal('Hello');
});

jq("#swal-2").click(function() {
	swal('Good Job', 'You clicked the button!', 'success');
});

jq("#swal-3").click(function() {
	swal('Good Job', 'You clicked the button!', 'warning');
});

jq("#swal-4").click(function() {
	swal('Good Job', 'You clicked the button!', 'info');
});

jq("#swal-5").click(function() {
	swal('Good Job', 'You clicked the button!', 'error');
});

jq("#swal-6").click(function() {
  swal({
      title: 'Are you sure?',
      text: 'Once deleted, you will not be able to recover this imaginary file!',
      icon: 'warning',
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete) {
      swal('Poof! Your imaginary file has been deleted!', {
        icon: 'success',
      });
      } else {
      swal('Your imaginary file is safe!');
      }
    });
});

jq("#swal-7").click(function() {
  swal({
    title: 'What is your name?',
    content: {
    element: 'input',
    attributes: {
      placeholder: 'Type your name',
      type: 'text',
    },
    },
  }).then((data) => {
    swal('Hello, ' + data + '!');
  });
});

jq("#swal-8").click(function() {
  swal('This modal will disappear soon!', {
    buttons: false,
    timer: 3000,
  });
});
