// const express = require('express')
// const router = express.Router()
// const Location = require('../models/location')
// const ensureAdmin = require('../middleware/ensureAdmin')

// router.get('/', ensureAdmin, async (req, res) => {
//     const locations = await Location.find().populate('country createdBy')
//     res.json(locations)
// })

// router.put('/:id/approve', ensureAdmin, async (req, res) => {
//     const location = await Location.findByIdAndUpdate(
//         req.params.id,
//         { isApproved: true },
//         { new: true }
//     )
//     if (!location)
//         return res.status(404).json({ message: 'Location not found' })
//     res.json({ message: 'Location approved', location })
// })

// router.put('/:id/reject', ensureAdmin, async (req, res) => {
//     const location = await Location.findByIdAndDelete(req.params.id)
//     if (!location)
//         return res.status(404).json({ message: 'Location not found' })
//     res.json({ message: 'Location rejected and removed' })
// })

// module.exports = router
const express = require('express');
const router = express.Router();
const ensureAdmin = require('../middleware/ensureAdmin');
const adminController = require('../controllers/adminController');

// Show all pending locations (HTML view)
router.get('/', ensureAdmin, adminController.getPendingLocations);

// Approve or reject locations
router.post('/:id/approve', ensureAdmin, adminController.approveLocation);
router.post('/:id/reject', ensureAdmin, adminController.rejectLocation);
router.delete('/:id/delete', ensureAdmin, adminController.deleteLocation);


router.get('/all', ensureAdmin, adminController.getAllLocations);


module.exports = router;
