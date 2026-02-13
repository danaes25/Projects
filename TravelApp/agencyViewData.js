// // routes/agencyViewData.js
// const express = require('express')
// const router = express.Router()
// const Country = require('../models/country')
// const Category = require('../models/category')

// const ensureAgency = require('../middleware/ensureAgency') // 

//approved countries (agency-only)

// const ensureAgency = require('../middleware/ensureAgency') 

// // Get approved countries (agency-only)

// router.get('/countries', ensureAgency, async (req, res) => {
//     try {
//         const countries = await Country.find({ isApproved: true })
//         res.json(countries)
//     } catch (err) {
//         res.status(500).json({ message: 'Server error', error: err.message })
//     }
// })


//  Get all categories (agency-only)


// router.get('/categories', ensureAgency, async (req, res) => {
//     try {
//         const categories = await Category.find()
//         res.json(categories)
//     } catch (err) {
//         res.status(500).json({ message: 'Server error', error: err.message })
//     }
// })

// module.exports = router
const express = require('express')
const router = express.Router()
const ensureAgency = require('../middleware/ensureAgency')
const agencyController = require('../controllers/agencyController')

router.get('/countries', ensureAgency, agencyController.getApprovedCountries);
router.get('/categories', ensureAgency, agencyController.getAllCategories);
router.get('/agency/dashboard', ensureAgency, agencyController.agencyDashboard);


module.exports = router
