document.addEventListener("DOMContentLoaded", () => {
  console.log("JavaScript chargé avec succès.");

  // Récupérer les éléments
  const menuToggle = document.getElementById("menu-toggle"); // Nouveau nom d'ID
  const navLinks = document.getElementById("nav-links");

  // Vérifiez que les éléments existent avant d'ajouter l'événement
  if (menuToggle && navLinks) {
      // Ajoutez un écouteur d'événement pour le clic
      menuToggle.addEventListener('click', () => {
          navLinks.classList.toggle('visible');  // Afficher ou masquer le menu
      });
  } else {
      console.error('Les éléments requis (menu-toggle ou nav-links) sont introuvables.');
  }

});
