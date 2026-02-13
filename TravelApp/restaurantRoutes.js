const express = require('express');
const router = express.Router();
const ensureAgency = require('../middleware/ensureAgency');
const upload = require('../middleware/upload');
const controller = require('../controllers/restaurantController')


router.get('/', controller.getAllRestaurants);
router.get('/add', ensureAgency, controller.getAddRestaurantForm);
// This handles the form submission (POST request)
router.post('/add', upload.single('image'), ensureAgency, controller.postAddRestaurant);

module.exports = router;
