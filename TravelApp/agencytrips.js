const express = require('express');
const router = express.Router();
const ensureAgency = require('../middleware/ensureAgency');
const tripController = require('../controllers/tripController');
const agencyController = require('../controllers/agencyController');
const upload = require('../middleware/upload');

// FORM: Add New Trip
router.get('/new', ensureAgency, agencyController.renderNewTripForm);

// HANDLE: Add New Trip (with image upload)
router.post('/new', ensureAgency, upload.single('image'), tripController.agencyCreateTrip);

// VIEW: All Trips by logged-in agency
router.get('/', ensureAgency, tripController.getAgencyTrips);

// DYNAMIC LOCATIONS FETCH
router.get('/locations/by-country/:countryId', ensureAgency, tripController.getLocationsByCountry);

module.exports = router;
