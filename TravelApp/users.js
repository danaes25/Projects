// const express = require('express');
// const router = express.Router();
// const { ensureAuth } = require('../middleware/auth');


// router.get('/', ensureAuth, (req, res) => {
//     res.render('dashboard', {
//         user: req.user,
//     });
// });

// module.exports = router;
const express = require('express');
const router = express.Router();
const { ensureAuth } = require('../middleware/auth');
const userController = require('../controllers/userController');


router.get('/client/dashboard', ensureAuth, userController.clientDashboard);


module.exports = router;
