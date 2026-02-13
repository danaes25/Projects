// // routes/admintrips.js
// const express = require('express')
// const router = express.Router()
// const Trip = require('../models/trips')
// const { ensureAdmin } = require('../middleware/auth')

// // GET all trips (incuding pending ones)
// router.get('/', ensureAdmin, async (req, res) => {
//     try {
//         const trips = await Trip.find().populate(
//             'country category createdBy locations'
//         )
//         res.json(trips)
//     } catch (err) {
//         res.status(500).json({
//             message: 'Failed to fetch trips',
//             error: err.message,
//         })
//     }
// })

// // Admin creates a new trip

// // Create a trip
// router.post('/', ensureAdmin, async (req, res) => {
//     try {
//         const { title, description, country, category, locations } = req.body

//         const newTrip = new Trip({
//             title,
//             description,
//             country,
//             category,
//             locations: locations || [],
//             isApproved: true,
//         })

//         await newTrip.save()
//         res.status(201).json(newTrip)
//     } catch (err) {
//         res.status(500).json({
//             message: 'Failed to create trip',
//             error: err.message,
//         })
//     }
// })

// // Approve a trip
// router.put('/:id/approve', ensureAdmin, async (req, res) => {
//     try {
//         const trip = await Trip.findByIdAndUpdate(
//             req.params.id,
//             { isApproved: true },
//             { new: true }
//         )
//         if (!trip) return res.status(404).json({ message: 'Trip not found' })
//         res.json({ message: 'Trip approved', trip })
//     } catch (err) {
//         res.status(500).json({
//             message: 'Failed to approve trip',
//             error: err.message,
//         })
//     }
// })

// // Reject a trip
// router.put('/:id/reject', ensureAdmin, async (req, res) => {
//     try {
//         const trip = await Trip.findByIdAndUpdate(
//             req.params.id,
//             { isApproved: false },
//             { new: true }
//         )
//         if (!trip) return res.status(404).json({ message: 'Trip not found' })
//         res.json({ message: 'Trip rejected', trip })
//     } catch (err) {
//         res.status(500).json({
//             message: 'Failed to reject trip',
//             error: err.message,
//         })
//     }
// })

// module.exports = router
const express = require('express')
const router = express.Router()
const { ensureAdmin } = require('../middleware/auth')
const tripController = require('../controllers/tripController')
const adminController = require('../controllers/adminController');

// GET all trips
router.get('/', ensureAdmin, tripController.getAllTrips)



// Approve a trip
router.put('/:id/approve', ensureAdmin, tripController.approveTrip)

// Reject a trip
router.put('/:id/reject', ensureAdmin, tripController.rejectTrip)


router.delete('/:id', tripController.deleteTrip);
module.exports = router;
