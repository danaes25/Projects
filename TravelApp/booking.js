const mongoose = require('mongoose');

const bookingSchema = new mongoose.Schema({
  user: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User',
    required: true,
  },
  trip: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Trip',
  },
  attraction: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Attraction',
  },
  createdAt: {
    type: Date,
    default: Date.now,
  }
});

// Add validation to ensure either trip or attraction is provided
bookingSchema.pre('save', function(next) {
  if (!this.trip && !this.attraction) {
    next(new Error('Either trip or attraction must be provided'));
  }
  if (this.trip && this.attraction) {
    next(new Error('Cannot book both trip and attraction at the same time'));
  }
  next();
});

// ✅ Prevent OverwriteModelError
const Booking = mongoose.models.Booking || mongoose.model('Booking', bookingSchema);

module.exports = Booking;
