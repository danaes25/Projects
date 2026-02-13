document.addEventListener("DOMContentLoaded", function () {
  // === Restaurant Filter ===
  const restaurantCheckboxes = document.querySelectorAll('.restaurant-system .filter-buttons input[type="checkbox"]');
  const restaurantCards = document.querySelectorAll('.restaurant-card');

  function filterRestaurants() {
    const selected = Array.from(restaurantCheckboxes)
      .filter(cb => cb.checked)
      .map(cb => cb.value.toLowerCase());

    restaurantCards.forEach(card => {
      const cuisine = card.getAttribute('data-cuisine').toLowerCase();
      card.style.display = selected.length === 0 || selected.includes(cuisine) ? "block" : "none";
    });
  }

  restaurantCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', filterRestaurants);
  });

  filterRestaurants();

  // === Hotel Filter ===
  const hotelCheckboxes = document.querySelectorAll('.hotel-system .filter-buttons input[type="checkbox"]');
  const hotelCards = document.querySelectorAll('.hotel-card');

  function filterHotels() {
    const selected = Array.from(hotelCheckboxes)
      .filter(cb => cb.checked)
      .map(cb => cb.value.toLowerCase());

    hotelCards.forEach(card => {
      const rate = card.getAttribute('data-rate').toLowerCase();
      card.style.display = selected.length === 0 || selected.includes(rate) ? "block" : "none";
    });
  }

  hotelCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', filterHotels);
  });

  filterHotels();
  
  // Reservation form
  const reservationForm = document.getElementById("reservation-form");
  if (reservationForm) {
    reservationForm.addEventListener("submit", function (e) {
      e.preventDefault(); // Prevent actual form submission

      const name = document.getElementById("name").value;
      const date = document.getElementById("date").value;
      const time = document.getElementById("time").value;
      const people = document.getElementById("people").value;

      // Fun confirmation alert
      alert(Reservation) ;

      reservationForm.reset(); // Optional: clear the form after submission
    });
  }

  // Hotel reservation form
  const hotelReservationForm = document.getElementById("hotel-reservation-form");
  if (hotelReservationForm) {
    hotelReservationForm.addEventListener("submit", function (e) {
      e.preventDefault();
      alert("ð Your hotel reservation has been confirmed!");
      this.reset(); // Optional: clears form after submission
    });
  }


  // === Load Footer ===
  fetch('footer.html')
    .then(response => response.text())
    .then(data => {
      document.getElementById('footer-placeholder').innerHTML = data;
    })
    .catch(error => {
      console.error('Error loading footer:', error);
    });
});
