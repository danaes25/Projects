function toggleReadMore(button, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    const card = button.closest('.attraction-card');
    const content = card.querySelector('.read-more-content');

    button.classList.toggle('active');
    content.style.maxHeight = content.style.maxHeight ? null : content.scrollHeight + 'px';
}
const Trip = require('../models/trips');

exports.renderHomePage = async (req, res) => {
  try {
    const trips = await Trip.find({ isApproved: true }).populate('country');
    res.render('home', { trips }); // pass trips to EJS
  } catch (err) {
    res.status(500).send('Error loading homepage');
  }
};


function performSearch() {
    const input = document.getElementById('searchInput').value.trim().toLowerCase();

    if (!input) {
        alert("Please enter a search term.");
        return;
    }

    const filename = `${input}.html`;

    // Redirect directly — works for local files
    window.location.href = filename;
}



