// const express = require('express')
// const router = express.Router()
// const Location = require('../models/location')
// const ensureAgency = require('../middleware/ensureAgency')

// // Hybrid approach: One endpoint, logic inside
// router.post('/', ensureAgency, async (req, res) => {
//     try {
//         const { name, description, country } = req.body

//         if (!name || !country) {
//             return res
//                 .status(400)
//                 .json({ message: 'Name and country are required' })
//         }

//         const location = new Location({
//             name,
//             description,
//             country,
//             createdBy: req.user._id,
//             isApproved: req.user.isTrusted || false,
//         })

//         await location.save()

//         res.status(201).json({
//             message: req.user.isTrusted
//                 ? 'Location added and approved'
//                 : 'Location suggested, awaiting approval',
//             location,
//         })
//     } catch (err) {
//         res.status(500).json({ message: 'Server error', error: err.message })
//     }
// })

// module.exports = router
const express = require('express');
const router = express.Router();
const ensureAgency = require('../middleware/ensureAgency');
const agencyController = require('../controllers/agencyController');
router.get('/new', ensureAgency, agencyController.renderNewLocationForm);
router.post('/', ensureAgency, agencyController.createLocation);
router.get('/dashboard', agencyController.agencyDashboard);


module.exports = router;

