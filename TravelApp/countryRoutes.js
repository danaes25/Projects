const express = require('express');
const router = express.Router();
const countryController = require('../controllers/countryController');

router.get('/', countryController.listCountries);
router.get('/:id', countryController.viewCountry);

router.get('/', async (req, res) => {
  try {
    const countries = await Country.find(); // You can add `.sort({ name: 1 })` if needed
    res.render('countries/index', { countries }); // pass countries to EJS
  } catch (err) {
    console.error(err);
    res.status(500).send('Server Error');
  }
});

module.exports = router;
