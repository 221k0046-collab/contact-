const form = document.getElementById("contactForm");

form.addEventListener("submit", e => {
  e.preventDefault();
  let valid = true;

  clearErrors();

  const first = document.getElementById("first-name");
  const last = document.getElementById("last-name");
  const email = document.getElementById("email");
  const message = document.getElementById("message");
  const consent = document.getElementById("consent");
  const query = document.querySelector("input[name='query-type']:checked");

  // VALIDACIONES FRONTEND
  if (!first.value.trim()) error(first, "error-first-name", "This field is required", () => valid = false);
  if (!last.value.trim()) error(last, "error-last-name", "This field is required", () => valid = false);

  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    error(email, "error-email", "Please enter a valid email address", () => valid = false);
  }

  if (!query) {
    document.getElementById("error-query").textContent = "Please select a query type";
    document.querySelectorAll(".option").forEach(o => o.classList.add("option-error"));
    valid = false;
  }

  if (!message.value.trim()) error(message, "error-message", "This field is required", () => valid = false);

  if (!consent.checked) {
    document.getElementById("error-consent").textContent =
      "To submit this form, please consent to being contacted";
    valid = false;
  }

  // SI PASA VALIDACIONES, ENVIAR A PHP
  if (valid) {
    enviarFormulario();
  }
});

// FUNCIÓN PARA ENVIAR DATOS A PHP
function enviarFormulario() {
  const formData = new FormData();
  
  formData.append('first_name', document.getElementById("first-name").value);
  formData.append('last_name', document.getElementById("last-name").value);
  formData.append('email', document.getElementById("email").value);
  formData.append('query_type', document.querySelector("input[name='query-type']:checked").value);
  formData.append('message', document.getElementById("message").value);
  formData.append('consent', document.getElementById("consent").checked ? '1' : '0');

  // Mostrar indicador de carga
  const submitBtn = form.querySelector('button[type="submit"]');
  const textoOriginal = submitBtn.textContent;
  submitBtn.textContent = "Enviando...";
  submitBtn.disabled = true;

  // ENVIAR CON FETCH
  fetch('procesar_formulario.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showToast('✅ Message Sent!', 'Thanks for completing the form. We\'ll be in touch soon!', 'success');
      form.reset();
    } else {
      showToast('❌ Error', data.error || 'Hubo un problema al enviar el formulario', 'error');
      console.error('Errores:', data.errores || data.error);
    }
  })
  .catch(error => {
    showToast('❌ Error de conexión', 'No se pudo conectar con el servidor', 'error');
    console.error('Error:', error);
  })
  .finally(() => {
    submitBtn.textContent = textoOriginal;
    submitBtn.disabled = false;
  });
}

function error(input, errorId, msg, cb) {
  input.classList.add("input-error");
  document.getElementById(errorId).textContent = msg;
  cb();
}

function clearErrors() {
  document.querySelectorAll(".error").forEach(e => e.textContent = "");
  document.querySelectorAll(".input-error").forEach(e => e.classList.remove("input-error"));
  document.querySelectorAll(".option").forEach(e => e.classList.remove("option-error"));
}

function showToast(titulo = 'Message Sent!', mensaje = 'Thanks for completing the form. We\'ll be in touch soon!', tipo = 'success') {
  const toast = document.createElement("div");
  toast.className = `toast toast-${tipo}`;
  toast.innerHTML = `
    <strong>${titulo}</strong><br>
    <span>${mensaje}</span>
  `;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 4000);
}
