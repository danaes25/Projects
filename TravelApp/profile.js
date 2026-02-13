// === profile.js ===

const express = require('express');
const router = express.Router();
const multer = require('multer');
const bcrypt = require('bcrypt');
const path = require('path');
const User = require('../models/user');

// ✅ Better storage config — saves clear filenames
const storage = multer.diskStorage({
  destination: 'public/uploads/',
  filename: (req, file, cb) => {
    cb(null, req.user._id + '-' + Date.now() + path.extname(file.originalname));
  }
});
const upload = multer({ storage });

// ✅ Auth check middleware
function isAuthenticated(req, res, next) {
  if (req.isAuthenticated()) return next();
  res.redirect('/login');
}

// ✅ GET profile page — protected
router.get('/', isAuthenticated, (req, res) => {
  res.render('profile', { user: req.user });
});

// ✅ POST update profile info — protected
router.post('/', isAuthenticated, async (req, res) => {
  try {
    const updates = req.body;
    const user = await User.findById(req.user._id);

    user.name = updates.name || user.name;
    user.email = updates.email || user.email;
    user.phone = updates.phone || user.phone;

    if (user.role === 'agency') {
      user.businessName = updates.businessName || user.businessName;
      user.website = updates.website || user.website;
      user.description = updates.description || user.description;
    }

    if (updates.newPassword && updates.newPassword.trim() !== '') {
      const hashed = await bcrypt.hash(updates.newPassword, 10);
      user.password = hashed;
    }

    await user.save();
    //res.redirect('/profile');
    res.json({ success: true });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'Update failed' });
  }
});


router.post('/upload', isAuthenticated, upload.single('profilePic'), async (req, res) => {
  try {
    if (!req.file) return res.status(400).json({ error: 'No file uploaded' });

    const user = await User.findById(req.user._id);
    user.profilePic = '/uploads/' + req.file.filename;
    await user.save();

    res.json({ 
      success: true,
      profilePic: user.profilePic 
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'Error uploading profile picture' });
  }
});


module.exports = router;
