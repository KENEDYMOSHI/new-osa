class FormEngine {
  constructor(formId, url, method = "POST") {
    this.form = document.getElementById(formId);
    if (!this.form) {
      console.error(`Form with ID "${formId}" not found`);
      return;
    }

    this.url = url;
    this.method = typeof method === "string" ? method.toUpperCase() : "POST";
    this.submitBtn = this.form.querySelector('[type="submit"]');
    this.errorContainer = this.createErrorContainer();
    this.bindSubmit();
  }

  createErrorContainer() {
    const container = document.createElement("div");
    container.className = "alert alert-danger d-none mb-4";
    container.id = `${this.form.id}-error-container`;
    this.form.prepend(container);
    return container;
  }

  bindSubmit() {
    this.form.addEventListener("submit", (e) => {
      e.preventDefault();
      this.submit();
    });
  }

  // 🔧 Hook to override: add extra fields or files before submission
  beforeSubmit(formData) {
    // Example: formData.append('userId', '123');
  }

  async submit() {
    this.clearErrors();

    const formData = new FormData(this.form);

    // 🔧 Allow injection of extra fields/files before submitting
    this.beforeSubmit(formData);

    const originalText = this.submitBtn.innerHTML;
    this.submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Submitting...`;
    this.submitBtn.disabled = true;

    try {
      const response = await fetch(this.url, {
        method: this.method,
        body: this.method === "GET" ? null : formData,
        headers:
          this.method === "GET"
            ? {}
            : {
              Accept: "application/json",
            },
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      const { status, token, msg } = data;

      // update csrf token
      document.querySelector(".token").value = token;
      //TODO checking http status  returned and response status to determine which message to show
      if (status == 1) {
        this.onSuccess(data);
        // swal({
        //   title: "Success",
        //   text: msg,
        //   icon: "success",
        // });
      } else {
        this.showErrors(data.errors || {});
        this.onError(data);
        // swal({
        //   title: "Error",
        //   text: msg,
        //   icon: "error",
        // });
      }
    } catch (error) {
      console.error("Form submission failed:", error);
      // swal({
      //   title: "Error",
      //   text: "An error occurred while submitting the form. Please try again.",
      //   icon: "error",
      // });
      this.onError({ message: error.message });
    } finally {
      this.submitBtn.innerHTML = originalText;
      this.submitBtn.disabled = false;
    }
  }

  formatErrorMessage(str) {
    if (!str) return "";

    return str.replace(/\b([a-zA-Z]+[A-Z][a-zA-Z]*)\b/g, (match) => {
      const spaced = match.replace(/([a-z])([A-Z])/g, "$1 $2");
      return spaced
        .split(" ")
        .map((w) => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase())
        .join(" ");
    });
  }

  showErrors(errors) {
    console.log("ERRORS:");
    const form = this.form;
    if (!form) return;

    form.querySelectorAll(".error-message").forEach((span) => span.remove());

    Object.keys(errors).forEach((key) => {
      const input = form.querySelector(`[name="${key}"]`);
      if (input) {
        const span = document.createElement("span");
        span.classList.add("error-message");
        span.style.color = "red";
        span.textContent = this.formatErrorMessage(errors[key]);

        input.insertAdjacentElement("afterend", span);

        input.removeEventListener("input", this.removeErrorMessage);
        input.removeEventListener("change", this.removeErrorMessage);
        //     const errorSpan = selectElement.closest('.form-group').querySelector('.error-message');
        const removeError = () => {
          const existingError =
            input.parentNode.querySelector(".error-message");
          if (existingError) existingError.remove();

          input.removeEventListener("input", removeError);
          input.removeEventListener("change", removeError);
        };

        if (
          input.tagName === "SELECT" ||
          input.type === "checkbox" ||
          input.type === "radio"
        ) {
          input.addEventListener("change", removeError);
        } else {
          input.addEventListener("input", removeError);
        }
      }
    });
  }

  clearErrors() {
    this.form.querySelectorAll(".is-invalid").forEach((input) => {
      input.classList.remove("is-invalid");
    });

    this.form.querySelectorAll(".invalid-feedback").forEach((el) => {
      el.textContent = "";
    });

    this.form.querySelectorAll(".error-message").forEach((span) => {
      span.remove();
    });

    this.errorContainer.classList.add("d-none");
    this.errorContainer.innerHTML = "";
  }

  onSuccess(data) {
    console.log("Success:", data);
  }

  onError(data) {
    console.warn("Error:", data);
  }
}
