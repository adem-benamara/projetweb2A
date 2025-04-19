document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  const nameInput = form.querySelector("input[name='events_name']");
  const placeInput = form.querySelector("input[name='events_place']");
  const dateInput = form.querySelector("input[name='events_date']");

  form.addEventListener("submit", function (e) {
      let errors = [];

      const name = nameInput.value.trim();
      const place = placeInput.value.trim();
      const date = dateInput.value;

      if (!name) errors.push("Le nom de l'événement est requis.");
      if (!place) errors.push("Le lieu de l'événement est requis.");

      const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
      if (!dateRegex.test(date)) {
          errors.push("Le format de la date est invalide (YYYY-MM-DD).");
      } else {
          //const today = new Date().toISOString().split('T')[0];
          const today = "2025-04-18";
          if (date < today) {
              errors.push("La date doit être aujourd'hui ou dans le futur.");
          }
      }

      if (errors.length > 0) {
          e.preventDefault(); // bloque l'envoi du formulaire
          alert(errors.join("\n"));
      }
  });
});
