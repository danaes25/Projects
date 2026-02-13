const express = require('express');
const router = express.Router();
const { ensureAuth } = require('../middleware/auth');
const Booking = require('../models/booking');

router.get('/', ensureAuth, (req, res) => {
  const bookingId = req.query.bookingId;
  res.render('payment', { bookingId });
});

router.post('/confirm', ensureAuth, async (req, res) => {
  const { bookingId, method, cardNumber, expiry, cvv, password } = req.body;

  try {
    // Optional: Save payment details (not recommended in real apps without encryption)
    const booking = await Booking.findById(bookingId);
    if (!booking) {
      return res.status(404).send('Booking not found');
    }

    booking.payment = {
      method,
      cardNumber: `**** **** **** ${cardNumber.slice(-4)}`,
      paidAt: new Date()
    };

    await booking.save();

res.redirect('/bookings/mybookings?success=Booking confirmed!');

  } catch (err) {
    console.error('Payment error:', err);
    res.redirect('/mybookings?error=Payment%20failed');
  }
});

module.exports = router;
