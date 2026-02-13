const express = require('express')
const router = express.Router()
const Country = require('../models/country')
const { ensureAgency } = require('../middleware/auth');

// Suggest a country (pending approval)
router.post('/suggest', ensureAgency, async (req, res) => {
    try {
        const { name, description } = req.body
        const country = new Country({
            name,
            description,
            createdBy: req.user._id,
            isApproved: false,
        })
        await country.save()
        res.status(201).json({
            message: 'Country suggestion submitted',
            country,
        })
    } catch (err) {
        res.status(500).json({ message: 'Server error', error: err.message })
    }
})

// Get countries suggested by the logged-in agency
router.get('/mine', ensureAgency, async (req, res) => {
    try {
        const countries = await Country.find({ createdBy: req.user._id })
        res.status(200).json(countries)
    } catch (err) {
        res.status(500).json({
            message: 'Failed to fetch your countries',
            error: err.message,
        })
    }
})

module.exports = router
