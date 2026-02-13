const express = require('express')
const router = express.Router()
const { ensureAdmin } = require('../middleware/auth')
const adminController = require('../controllers/adminController')
const uploadAttraction = require('../middleware/attractionUpload');

// Route to render the form for adding a new attraction
router.get('/new', ensureAdmin, adminController.renderNewAttractionForm);

// Route to handle the submission of the new attraction form with image upload
router.post('/', ensureAdmin, uploadAttraction.single('image'), adminController.createAttraction);

// Route to display all attractions for admin management
router.get('/', ensureAdmin, adminController.renderAdminAttractionsList);

// Route to handle deleting an attraction
router.delete('/:id', ensureAdmin, adminController.deleteAttraction);

module.exports = router; 