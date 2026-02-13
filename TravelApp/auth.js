const express = require('express')
const router = express.Router()
const passport = require('passport')
const User = require('../models/user')
const authController = require('../controllers/authController');


// Show login form
router.get('/login', (req, res) => {
    res.render('auth/login'); // This loads views/auth/login.ejs
});

// Show register form
router.get('/register', (req, res) => {
    res.render('auth/register'); // This loads views/auth/register.ejs
});


// REGISTER
router.post('/register', async (req, res) => {
  const {
    name,
    email,
    password,
    role,
    businessName,
    phone,
    website,
    description
  } = req.body;

  if (!name || !email || !password) {
    req.flash('error', 'Name, email, and password are required.');
    return res.redirect('/register');
  }

  // 🛑 Prevent manual admin role manipulation
  let finalRole = role;
  if (role === 'admin') {
    req.flash('error', 'You cannot register as an admin.');
    return res.redirect('/register');
  }

  // 🧾 Validate agency-only fields
  if (finalRole === 'agency') {
    if (!businessName || !phone || !description) {
      req.flash('error', 'All agency fields are required.');
      return res.redirect('/register');
    }
  }

  try {
    const existingUser = await User.findOne({ email });
    if (existingUser) {
      req.flash('error', 'Email already registered.');
      return res.redirect('/register');
    }

    // Prepare user object conditionally
    let newUserData = { name, email, password, role: finalRole };

    if (finalRole === 'agency') {
      newUserData.businessName = businessName;
      newUserData.phone = phone;
      newUserData.website = website;
      newUserData.description = description;
    }

    const newUser = new User(newUserData);
    await newUser.save();

    req.flash('success', 'Registration successful. Please log in.');
    res.redirect('/login');
  } catch (err) {
    console.error(err);
    req.flash('error', 'Something went wrong.');
    res.redirect('/register');
  }
});


// LOGIN
router.post('/login', (req, res, next) => {
  passport.authenticate('local', (err, user, info) => {
    if (err) return next(err);
    if (!user) return res.redirect('/login');

    req.logIn(user, (err) => {
      if (err) return next(err);

      if (user.role === 'admin') {
        return res.redirect('/admin/dashboard');
      } else if (user.role === 'agency') {
        return res.redirect('/agency/dashboard');
      } else {
        return res.redirect('/');
      }
    });
  })(req, res, next);
});

//logoutt
router.get('/logout', (req, res) => {
  req.logout((err) => {
    if (err) {
      req.flash('error', 'Error during logout');
      return res.redirect('/');
    }
    req.flash('success', 'Logged out successfully');
    res.redirect('/'); 
  });
});

// //forgetpass
// router.get('/forgetpass', (req, res) => res.render('auth/forgetpass'));
// router.post('/forgetpass', authController.sendResetLink);




module.exports = router;
