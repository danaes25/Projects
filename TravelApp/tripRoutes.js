const express = require('express');
const router  = express.Router();
const Trip    = require('../models/trips');
const tripController = require('../controllers/tripController');
router.get('/search', tripController.searchTrips);
const ensureAgency = require('../middleware/ensureAgency');
const upload = require('../middleware/upload');

module.exports = router;
// routes/tripRoutes.js or app.js or wherever your home route is


//  SEARCH — must come first
// GET /trips/search


// FETCH ONE BY ID
router.get('/:id', async (req, res) => {
  try {
    const trip = await Trip.findById(req.params.id);
    if (!trip) return res.status(404).json({ message: 'Trip not found' });
    res.json(trip);
  } catch (err) {
    res.status(500).json({ message: 'Failed to fetch trip', error: err });
  }
});

router.post('/new', ensureAgency, upload.single('image'), tripController.agencyCreateTrip);

router.get('/trips', ensureAgency, tripController.getAgencyTrips);

module.exports = router;
