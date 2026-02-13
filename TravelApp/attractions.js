const express = require('express')
const router = express.Router()
const attractionController = require('../controllers/attractionController')
const { ensureAuth } = require('../middleware/auth')
const uploadAttractionImage = require('../middleware/attractionUpload');

// NOTE: The /attractions POST route below is for creating attractions, likely by an admin.
// If you intend for users to create attractions via a UI form, you'll need a separate EJS view and route for that.
// For now, I'm keeping it as a direct API endpoint.

// Route to create a new attraction (typically for admin use)
router.post('/', uploadAttractionImage.single('image'), attractionController.createAttraction)

// Get all attractions with average rating
router.get('/', attractionController.getAllAttractions)

// Route to display a single attraction with its reviews and average rating
router.get('/:id', attractionController.getAttractionById)

// Add a review to an attraction
// console.log('attractionController.addReview:', attractionController.addReview)
router.post('/:id/reviews', ensureAuth, attractionController.addReview)

module.exports = router

