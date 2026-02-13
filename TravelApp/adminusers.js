// const express = require('express')
// const router = express.Router()
// const User = require('../models/user')
// const ensureAdmin = require('../middleware/ensureAdmin')

// // Mark agency as trusted
// router.put('/users/:id/trust', ensureAdmin, async (req, res) => {
//     try {
//         const user = await User.findByIdAndUpdate(
//             req.params.id,
//             { isTrusted: true },
//             { new: true }
//         )
//         if (!user) return res.status(404).json({ message: 'User not found' })
//         res.json({ message: 'Agency marked as trusted', user })
//     } catch (err) {
//         res.status(500).json({ message: 'Server error', error: err.message })
//     }
// })

// module.exports = router

const express = require('express');
const router = express.Router();



const ensureAdmin = require('../middleware/ensureAdmin');
const adminController = require('../controllers/adminController');

router.put('/users/:id/trust', ensureAdmin, adminController.trustAgency);
router.get('/dashboard', ensureAdmin, adminController.adminDashboard);
router.get('/', ensureAdmin, adminController.listUsers);


router.get('/', adminController.listUsers); // for /admin/users
router.get('/agencies', adminController.listAgencies); // for /admin/agencies
router.patch('/agencies/:id/trust', adminController.markAgencyTrusted);
router.delete('/agencies/:id', adminController.deleteAgency);
router.delete('/bookings/:id', adminController.deleteBooking);





module.exports = router;

