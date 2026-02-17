document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");
  const errorBox = document.getElementById("errorBox");
  const successBox = document.getElementById("successBox");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(form);

    try {
      const response = await fetch(form.action, {
        method: "POST",
        headers: {
          Accept: "application/json",
        },
        body: formData,
      });

      // If backend returns non-JSON (like PHP error), handle safely
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }

      const result = await response.json();

      if (result.success) {
        // Show success
        successBox.textContent = "Login successful! Redirecting...";
        successBox.style.display = "block";
        errorBox.style.display = "none";

        // Redirect after 1.5 sec
        setTimeout(() => {
          window.location.href = result.redirect;
        }, 600);
      } else {
        // Show backend validation or wrong credentials message
        errorBox.textContent = result.message || "Invalid credentials.";
        errorBox.style.display = "block";
        successBox.style.display = "none";
      }
    } catch (err) {
      console.error("Login error:", err);
      errorBox.textContent = "Error connecting to server. Please try again.";
      errorBox.style.display = "block";
      successBox.style.display = "none";
    }
  });
});
