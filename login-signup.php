<?php include("header.html")?>
<!DOCTYPE html>
<html>
  <head>
    <script src="https://kit.fontawesome.com/a39233b32c.js" crossorigin="anonymous"></script>
    <title>Signup | Login</title>
    <link rel="stylesheet" href="./css/login-signup.css?v=1.1">
    <link rel="stylesheet" href="./css/main.css?v=1.1">
  </head>
  <body>

    <div class="outer-signup-container">
      <div class="signup-container">
        <!-- Form Section -->
        <div class="form-container" id="slider1">
          <!-- Login Form -->
          <div class="form-content" id="login-box" style="display: none;">
            <h1 class="login">Login</h1>

            <form id="login-form" method="post" action="MemberSignup.php">
              <div class="input-container">
                <input id="login-email" name="login-email" type="text" placeholder="Email"/>
                <i class="fa-solid fa-circle-exclamation login-warning" style="color: #ff6f61;"></i>
              </div> 

              <div class="role-dropdown">
                <label for="signup-role">Role:</label>
                <select id="signup-role" name="signup-role" required class="role-pick">
                  <option value="user">User</option>
                  <option value="staff">Staff</option>
                  <option value="admin">Admin</option>
                </select>
              </div>

              <div class="input-container">
                <input id="login-password" name="login-password" type="password" placeholder="Password"/>
                <i class="fa-solid fa-circle-exclamation login-warning" style="color: #ff6f61;"></i>
              </div>
              
              <p class="invalid-message login-message">Invalid email or password input!</p>
              <button class="signup-btn login-button" type="submit">Login</button>
            </form>
            
            <div class="switch-signup-login-msg">
              Don't have an account?
              <button class="switch-signup" onclick="showSignUp()">Sign Up</button>
            </div>
          </div>

          <!-- Signup Form -->
          <div class="form-content" id="signup-box">
            <h1 class="signup">Sign Up</h1>
            <form id="signup-form" method="post" action="">
              <div class="input-container">
                <div class="name-container">
                  <div class="name-field">
                    <input id="firstname" name="firstname" class="user-inputs-name" type="text" placeholder="First Name"/>
                    <i class="fa-solid fa-circle-exclamation signup-warning" style="color: #ff6f61;"></i>
                  </div>
                  <div class="name-field">
                    <input id="lastname" name="lastname" class="user-inputs-name" type="text" placeholder="Last Name"/>
                    <i class="fa-solid fa-circle-exclamation signup-warning" style="color: #ff6f61;"></i>
                  </div>
                </div>
              </div>

              <div class="input-container">
                <input id="user-email" name="user-email" class="user-inputs" type="text" placeholder="Email"/>
                <i class="fa-solid fa-circle-exclamation signup-warning" style="color: #ff6f61;"></i>
              </div>

              <div class="input-container">
                <input id="user-cell-no" name="user-cell-no" class="user-inputs" type="text" placeholder="Contact No."/>
                <i class="fa-solid fa-circle-exclamation signup-warning" style="color: #ff6f61;"></i>
              </div>

              <div class="role-dropdown">
                <label for="signup-role">Role:</label>
                <select id="signup-role" name="signup-role" required class="role-pick">
                  <option value="user">User</option>
                  <option value="staff">Staff</option>
                  <option value="admin">Admin</option>
                </select>
              </div>

              <div class="input-container">
                <input id="signup-password" name="signup-password" type="password" placeholder="Password"/>
                <i class="fa-solid fa-circle-exclamation signup-warning" style="color: #ff6f61;"></i>
              </div>

              <div class="input-container">
                <input id="confirm-password-input" name="confirm-password-input" type="password" placeholder="Confirm Password"/>
                <i class="fa-solid fa-circle-exclamation signup-warning" style="color: #ff6f61;"></i>
              </div>

              <p class="invalid-message signup-message">Invalid input!</p>
              <button class="signup-btn" type="submit">Sign Up</button>
              <div class="switch-signup-login-msg">
                Already have an account?
                <button class="switch-login" onclick="showLogin()">Login</button>
              </div>
            </form>
          </div>
        </div>

        <div class="design" id="slider">
          <p class="message"> Welcome to<span> AnimoBowl!</span></p>
        </div>
      </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
      const slider = $("#slider"); // design div
      const slider1 = $("#slider1"); // form div
      const signupBox = $("#signup-box");
      const loginBox = $("#login-box");

      // Function to reset all form styles
      function resetAllFormStyles() {
        // Reset login form styles
        $("#login-email, #login-password").css("border-color", "#ccc");
        $(".login-message").css("opacity", "0");
        $(".login-warning").removeClass("show");
        
        // Reset signup form styles
        $("#firstname, #lastname, #user-email, #user-cell-no, #signup-password, #confirm-password-input").css("border-color", "#ccc");
        $(".signup-message").css("opacity", "0");
        $(".signup-warning").removeClass("show");
      }

      function showLogin() {
        resetAllFormStyles();
        slider.css("transform", "translateX(-100%)"); // move design to the left
        slider1.css("transform", "translateX(100%)"); // move form to the right
        slider.css("transition", "1.5s");
        slider1.css("transition", "1.5s");
        signupBox.hide(); // Hide signup form
        loginBox.show();  // Show login form
        loginBox.addClass('fadeAnimation');
        signupBox.removeClass('fadeAnimation');
        clearSignUp();
      }

      function showSignUp() {
        resetAllFormStyles();
        slider.css("transform", "translateX(0)"); // move design back to the right
        slider1.css("transform", "translateX(0)"); // move form back to the left
        slider.css("transition", "1.5s");
        slider1.css("transition", "1.5s");
        signupBox.show(); // Show signup form
        loginBox.hide();  // Hide login form
        signupBox.addClass('fadeAnimation');
        loginBox.removeClass('fadeAnimation');
        clearLogin();
      }

      // Signup form validation
      $("#signup-form").on('submit', function(e) {
        e.preventDefault();
        const isValid = validateSignup();
        if (isValid) {
          this.submit();
        }
      });

      function validateSignup(){
        const firstName = $("#firstname").val().trim();
        const lastName = $("#lastname").val().trim();
        const email = $("#user-email").val().trim();
        const contactNo = $("#user-cell-no").val().trim();
        const password = $("#signup-password").val().trim();
        const confirmPassword = $("#confirm-password-input").val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const contactRegex = /^\d{11}$/; // 11 digits
        
        if(!firstName || !lastName || !email || !contactNo || !password || !confirmPassword){
          showSignupError("All fields are required!");
          return false;
        }else if((!emailRegex.test(email))){   
          showSignupError("Invalid email! (Sample: example@domain.com)");
          return false;
        }else if(!(contactRegex.test(contactNo))){
          showSignupError("Invalid contact number! (11 digits)");
          return false;
        }else if(password.length < 8){
          showSignupError("Password must be at least 8 characters long!");
          return false;
        }else if(password !== confirmPassword){
          showSignupError("Passwords do not match!");
          return false;
        }else{
          return true;
        }
      }

      function showSignupError(message) {
        $(".signup-message").html(message).css("opacity", "1");
        $(".signup-warning").addClass("show");
        $("#firstname, #lastname, #user-email, #user-cell-no, #signup-password, #confirm-password-input").css("border-color","red");
      }

      function clearSignUp(){
        $("#firstname, #lastname, #user-email, #user-cell-no, #signup-password, #confirm-password-input").val('');
        $("#firstname, #lastname, #user-email, #user-cell-no, #signup-password, #confirm-password-input").css("border-color","#ccc");
        $(".signup-message").css("opacity", "0");
        $(".signup-warning").removeClass("show");
      }

      // Login form validation
      $("#login-form").on('submit', function(e) {
        e.preventDefault();
        const isValid = validateLogin();
        if (isValid) {
          this.submit();
        }
      });

      function validateLogin(){
        const email = $("#login-email").val().trim();
        const password = $("#login-password").val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
       
        if(!email || !password){
          showLoginError("All fields are required!");
          return false;
        }else if (!emailRegex.test(email)) {
          showLoginError("Please enter a valid email address");
          return false;
        }else if(password.length < 8){
          showLoginError("Password must be at least 8 characters long!");
          return false;
        }else{
          return true;
        }
      }

      function showLoginError(message) {
        $(".login-message").html(message).css("opacity", "1");
        $(".login-warning").addClass("show");
        $("#login-email, #login-password").css("border-color","red");
      }

      function clearLogin(){
        $("#login-email, #login-password").val('');
        $("#login-email, #login-password").css("border-color","#ccc");
        $(".login-message").css("opacity", "0");
        $(".login-warning").removeClass("show");
      }

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