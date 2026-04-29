const eyeIcon = document.getElementById("eye");
const passwordField = document.getElementById("password");
eyeIcon.addEventListener("click", () => {
  if (passwordField.type === "password" && passwordField.value) {
    passwordField.type = "text";
    eyeIcon.classList.remove("fa-eye");
    eyeIcon.classList.add("fa-eye-slash");
  } else {
    passwordField.type = "password";
    eyeIcon.classList.remove("fa-eye-slash");
    eyeIcon.classList.add("fa-eye");
  }
});


document.addEventListener("DOMContentLoaded", function () {
  const togglePasswordVisibility = (fieldId, toggleIconId) => {
      const passwordField = document.getElementById(fieldId);
      const toggleIcon = document.getElementById(toggleIconId);

      toggleIcon.addEventListener("click", function () {
          const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
          passwordField.setAttribute("type", type);
          toggleIcon.classList.toggle("fa-eye");
          toggleIcon.classList.toggle("fa-eye-slash");
      });
  };

  togglePasswordVisibility("current_password", "eye-current");
  togglePasswordVisibility("new_password", "eye-new");

  // Add frontend validation for new password length
  const form = document.querySelector("form");
  form.addEventListener("submit", function (e) {
      const newPassword = document.getElementById("new_password").value;
      if (newPassword && newPassword.length < 8) {
          e.preventDefault(); // Prevent form submission
          alert("New password must be at least 8 characters long.");
      }
  });
});


