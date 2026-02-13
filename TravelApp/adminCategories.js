// const express = require('express')
// const router = express.Router()
// const Category = require('../models/category')


// const { ensureAdmin } = require('../middleware/auth'); 




// // Add a new category
// router.post('/', ensureAdmin, async (req, res) => {
//     try {
//         const { name } = req.body
//         const category = new Category({ name })
//         await category.save()
//         res.status(201).json({ message: 'Category added', category })
//     } catch (err) {
//         res.status(500).json({ message: 'Server error', error: err.message })
//     }
// })

// // Get all categories
// router.get('/', ensureAdmin, async (req, res) => {
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
const { ensureAdmin } = require('../middleware/auth')
const adminController = require('../controllers/adminController')

router.post('/', ensureAdmin, adminController.createCategory)
router.get('/', ensureAdmin, adminController.getAllAdminCategories)

module.exports = router
