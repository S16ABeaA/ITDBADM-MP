<?php include('header.html')?>
<!DOCTYPE html>
<html>
  <head>
    <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
    <title>Sign up</title>
    <link rel="stylesheet" href="./css/signup.css">

  </head>
  <body>

    <div class="outer-signup-container">
      <div class="signup-container">
        <!-- Form Section -->
        <div class="form-container" id="slider1">
          <!-- Member Signup Form -->
          <div class="form-content" id="member-signup-box" style="display: none;">
            <h1 class="signup">Member Signup</h1>

            <form id="mem-signup-form" method="post" action="MemberSignup.php">
              <div class="input-container">
                <input id="email-input" name="email-input" type="text" placeholder="Email"/>
                <i class="fa-solid fa-circle-exclamation msignup-warning" style="color: #ff6f61;"></i>
              </div> 

              <div class="input-container">
                <input id="password-input" name="password-input" type="password" placeholder="Password"/>
                <i class="fa-solid fa-circle-exclamation msignup-warning" style="color: #ff6f61;"></i>
              </div>

              <div class="input-container">
                <input id="confirm-password-input" name="confirm-password-input" type="password" placeholder="Confirm Password"/>
                <i class="fa-solid fa-circle-exclamation msignup-warning" style="color: #ff6f61;"></i>
              </div>
              
              <p class="invalid-message member-message">Invalid email or password input!</p>
              <button class="signup-btn member-signup-button" type="submit">Signup</button>
            </form>
            
            <div>
              Signup as
              <button class="switch-signup" onclick="showGuestSignUp()">Guest</button>
            </div>
          </div>

          <!--  Guest-Sign-Up Form -->
          <div class="form-content" id="guest-signup-box">
            <h1 class="signup">Guest Signup</h1>
            <form id="guest-signup-form" method="post" action="GuestSignup.php">
              <div class="input-container">
                <input id="guest-name" name="guest-name" class="guest-inputs" type="text" placeholder="Full Name"/>
                <i class="fa-solid fa-circle-exclamation gsignup-warning" style="color: #ff6f61;"></i>
              </div>

              <div class="input-container">
                <input id="guest-email" name="guest-email" class="guest-inputs" type="text" placeholder="Email"/>
                <i class="fa-solid fa-circle-exclamation gsignup-warning" style="color: #ff6f61;"></i>
              </div>

              <div class="input-container">
                <input id="guest-cnumber" name="guest-cnumber" class="guest-inputs" type="text" placeholder="Contact No."/>
                <i class="fa-solid fa-circle-exclamation gsignup-warning" style="color: #ff6f61;"></i>
              </div>
            
              <p class="invalid-message guest-message">Invalid email or password input!</p>
              <button class="signup-btn" type="submit">Sign Up</button>
              <div>
                Signup as
                <button class="switch-signup" onclick="showMemberSignup()">Member</button>
              </div>
            </form>

          </div>
        </div>

        <div class="design" id="slider">
          <p class="message"> Welcome to<span> GG Cafe!</span></p>
        </div>
      </div>
    </div>
 

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
      const slider = $("#slider"); // design div
      const slider1 = $("#slider1"); // form div
      const gSignupBox = $("#guest-signup-box");
      const mSignupBox = $("#member-signup-box");


    document.addEventListener('DOMContentLoaded', function() {
      // Get URL parameters
      const params = new URLSearchParams(window.location.search);
      const type = params.get('type');
      
      // Reset all form styles first
      resetAllFormStyles();
      
      // Determine which form to show based on URL parameters
      if (type === 'guest') {
        showGuestSignUp();
      } else {
        showMemberSignup();
      }

      // Handle hash-based navigation
      if(window.location.hash === '#member') {
        showMemberSignup();
      } else if(window.location.hash === '#guest') {
        showGuestSignUp();
      }
    
    });

     // Function to reset all form styles
      function resetAllFormStyles() {
        // Reset member form styles
        $("#email-input, #password-input, #confirm-password-input").css("border-color", "#ccc");
        $(".member-message").css("opacity", "0");
        $(".msignup-warning").removeClass("show");
        
        // Reset guest form styles
        $("#guest-name, #guest-email, #guest-cnumber").css("border-color", "#ccc");
        $(".guest-message").css("opacity", "0");
        $(".gsignup-warning").removeClass("show");
        
      }



      function showMemberSignup() {
        resetAllFormStyles();
        slider.css("transform", "translateX(-100%)"); // move design to the left
        slider1.css("transform", "translateX(100%)"); // move form to the right
        slider.css("transition", "1.5s");
        slider1.css("transition", "1.5s");
        gSignupBox.hide(); // Hide Sign-Up form
        mSignupBox.show();  // Show member form
        mSignupBox.addClass('fadeAnimation');
        gSignupBox.removeClass('fadeAnimation');
        clearGuestSignUp();
        window.location.hash = '#member';
      }

      function showGuestSignUp() {
        resetAllFormStyles();
        slider.css("transform", "translateX(0)"); // move design back to the right
        slider1.css("transform", "translateX(0)"); // move form back to the left
        slider.css("transition", "1.5s");
        slider1.css("transition", "1.5s");
        gSignupBox.show(); // Show Sign-Up form
        mSignupBox.hide();  // Hide member form
        gSignupBox.addClass('fadeAnimation');
        mSignupBox.removeClass('fadeAnimation');
        clearMemberSignup();
        window.location.hash = '#guest';
      }

      $("#guest-signup-form").on('submit', function(e) {
        e.preventDefault();
        const isValid = validateGuestSignup();
        if (isValid) {
          this.submit();
        }
      });


      function validateGuestSignup(){
        const name = $("#guest-name").val().trim();
        const email = $("#guest-email").val().trim();
        const contactNo = $("#guest-cnumber").val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const contactRegex = /^\d{11}$/; // 11 digits
        
        if(!name || !email || !contactNo){
          showGuestSignupError("All fields are required!");
          return false;
        }else if((!emailRegex.test(email))){   
          showGuestSignupError("Invalid email! (Sample: example@domain.com)");
          return false;
        }else if(!(contactRegex.test(contactNo))){
          showGuestSignupError("Invalid contact number! (11 digits)");
          return false;
        }else{
          return true;
        }
      }

      function showGuestSignupError(message) {
        $(".guest-message").html(message).css("opacity", "1");
        $(".gsignup-warning").addClass("show");
        $("#guest-name, #guest-email, #guest-cnumber").css("border-color","red");
      }

      function clearGuestSignUp(){
          $("#guest-name, #guest-email, #guest-cnumber").val('');
          $("#guest-name, #guest-email, #guest-cnumber").css("border-color","#ccc");
          $(".guest-message").css("opacity", "0");
          $(".gsignup-warning").removeClass("show");//unshow warning icons
      }

      function checkPasswordLength(password){
        if(password.length < 8 ){
          return true;
        }else return false;
      }

      async function validateMemberSignup(){
        const email = $("#email-input").val().trim();
        const password = $("#password-input").val().trim();
        const confirmPassword = $("#confirm-password-input").val().trim();
        const Invalid = email === '' || password === '' || confirmPassword == '';
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
       
        if(Invalid){
          showMemSignupError("All fields are required!");
          return false;
        }else if(checkPasswordLength(password) || checkPasswordLength(confirmPassword)){  
          showMemSignupError("Password minimum length is 8 characters!");
          return false;
        }else if (!emailRegex.test(email)) {
        showMemSignupError("Please enter a valid email address");
          return false
        }else if(password != confirmPassword){
          showMemSignupError("Password do not match!");
          return false;
        }

          // Check if email exists via AJAX
          try {
            const response = await $.ajax({
                url: 'check_email.php',
                type: 'POST',
                data: { email: email },
                dataType: 'json'
            });
            
            if(response.exists) {
                showMemSignupError("Email already in use");
                return false;
            }
            
        } catch (error) {
            console.error("Error checking email:", error);
            showMemSignupError("Error validating email. Please try again.");
            return false;
        }

         
          return true;
      }

      // Modify your form submission to use the async validation
      $("#mem-signup-form").on('submit', async function(e) {
          e.preventDefault();
          
          const isValid = await validateMemberSignup();
          if (isValid) {
              this.submit(); // Submit the form if validation passes
          }
      });

      function clearMemberSignup(){
        $("#email-input, #password-input , #confirm-password-input").val('');
        $("#email-input, #password-input, #confirm-password-input").css("border-color","#ccc");
        $(".member-message").css("opacity", "0");
        $(".msignup-warning").removeClass("show");
      }

      function showMemSignupError(message) {
        $(".member-message").html(message).css("opacity", "1");
        $(".msignup-warning").addClass("show");
        $("#email-input, #password-input, #confirm-password-input").css("border-color","red");
      }

      // if (window.location.search.includes('error=')) {
      //   window.history.replaceState({}, document.title, window.location.pathname + window.location.hash);
      // }

      let resizeTimeout;

      $(window).on("resize", function () {
        // Disable transitions during resize
        slider.css("transition", "none");
        slider1.css("transition", "none");

        // Clear the timeout if it exists
        if (resizeTimeout) clearTimeout(resizeTimeout);

        // Re-enable transitions after a short delay
        resizeTimeout = setTimeout(function () {
          slider.css("transition", "1.5s");
          slider1.css("transition", "1.5s");
        }, 100);
      });

    </script>
    
  </body>
</html>

