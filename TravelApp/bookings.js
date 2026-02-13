const express = require('express');
const router = express.Router();
const Booking = require('../models/booking');
const Attraction = require('../models/attraction');
const Trip = require('../models/trips');

const bookingController = require('../controllers/bookingController');
const { ensureAuth } = require('../middleware/auth');

// Create a new booking
router.post('/', ensureAuth, async (req, res) => {
  try {
    const { tripId, attractionId } = req.body;
    
    if (!tripId && !attractionId) {
      return res.status(400).json({ message: 'Either tripId or attractionId is required' });
    }

    const bookingData = {
      user: req.user._id
    };

    if (tripId) {
      bookingData.trip = tripId;
    } else {
      bookingData.attraction = attractionId;
    }

    const newBooking = await Booking.create(bookingData);
    res.redirect(`/payment?bookingId=${newBooking._id}`);
  } catch (err) {
    console.error('Booking failed:', err);
    res.redirect('/?error=Booking failed');
  }
});

// Get booking form for new attraction booking
router.get('/new', ensureAuth, async (req, res) => {
  try {
    const { attractionId } = req.query;
    if (!attractionId) {
      return res.redirect('/?error=Attraction ID is required');
    }

    const attraction = await Attraction.findById(attractionId);
    if (!attraction) {
      return res.redirect('/?error=Attraction not found');
    }

    res.render('bookings/new', {
      attraction,
      user: req.user
    });
  } catch (err) {
    console.error('Failed to load booking form:', err);
    res.redirect('/?error=Failed to load booking form');
  }
});

// View user's bookings
router.get('/mybookings', ensureAuth, async (req, res) => {
  try {
    const bookings = await Booking.find({ user: req.user._id })
      .populate({ path: 'trip', populate: { path: 'country' } })
      .populate({ path: 'attraction', populate: { path: 'country' } });
    
    res.render('mybookings', {
      bookings,
      success: req.query.success,
      error: req.query.error
    });
  } catch (err) {
    console.error('Failed to load mybookings:', err);
    res.status(500).send('Error loading bookings');
  }
});

module.exports = router;
